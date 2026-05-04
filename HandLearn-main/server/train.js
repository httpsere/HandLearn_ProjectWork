const tf = require("@tensorflow/tfjs");
const fs = require("fs");
const path = require("path");
const csv = require("csv-parser");

const INPUT_SIZE = 63;

const features = [];
const labels = [];

const labelMap = {};
let labelIndex = 0;

// Percorso dataset
const CSV_PATH = path.join(__dirname, "gestures.csv");

// Percorso dove salvare modello e labels
const MODEL_PATH = path.join(__dirname, "model");

function loadCSV(filePath) {
  return new Promise((resolve) => {
    fs.createReadStream(filePath)
      .pipe(csv())
      .on("data", (row) => {
        const input = [];

        // ---- NORMALIZZAZIONE (UGUALE AL BROWSER) ----
        const baseX = parseFloat(row["x0"]);
        const baseY = parseFloat(row["y0"]);
        const baseZ = parseFloat(row["z0"]);

        const scale = Math.hypot(
          parseFloat(row["x9"]) - baseX,
          parseFloat(row["y9"]) - baseY,
          parseFloat(row["z9"]) - baseZ
        );
        if (scale < 0.00001) return;

        for (let i = 0; i < 21; i++) {
          const x = parseFloat(row[`x${i}`]);
          const y = parseFloat(row[`y${i}`]);
          const z = parseFloat(row[`z${i}`]);

          input.push(
            (x - baseX) / scale,
            (y - baseY) / scale,
            (z - baseZ) / scale
          );
        }

        features.push(input);

        if (!(row.label in labelMap)) {
          labelMap[row.label] = labelIndex++;
        }

        labels.push(labelMap[row.label]);
      })
      .on("end", resolve);
  });
}

async function main() {
  console.log("Carico dataset...");
  await loadCSV(CSV_PATH);
  tf.util.shuffleCombo(features, labels);
  const NUM_CLASSES = Object.keys(labelMap).length;
  console.log("Label map:", labelMap);
  console.log("Numero classi:", NUM_CLASSES);

  const xs = tf.tensor2d(features);
  const ys = tf.oneHot(tf.tensor1d(labels, "int32"), NUM_CLASSES);

  console.log("Creo il modello...");
  const model = tf.sequential();

model.add(tf.layers.dense({
  inputShape: [INPUT_SIZE],
  units: 256,
  activation: "relu"
}));

model.add(tf.layers.batchNormalization());

model.add(tf.layers.dense({
  units: 128,
  activation: "relu"
}));

model.add(tf.layers.dropout({ rate: 0.5 }));

model.add(tf.layers.dense({
  units: 64,
  activation: "relu"
}));

model.add(tf.layers.dense({
  units: NUM_CLASSES,
  activation: "softmax"
}));
model.compile({
  optimizer: tf.train.adam(0.0008),
  loss: "categoricalCrossentropy",
  metrics: ["accuracy"],
});

  console.log("Avvio training...");
  await model.fit(xs, ys, {
    epochs: 120,
    batchSize: 16,
    validationSplit: 0.2,
    shuffle: true,
    callbacks: {
      onEpochEnd: (epoch, logs) => {
      console.log(
      `Epoch ${epoch + 1}: ` +
      `loss=${logs.loss.toFixed(3)} ` +
      `acc=${logs.acc?.toFixed(3)} ` +
      `val_loss=${logs.val_loss.toFixed(3)} ` +
      `val_acc=${logs.val_acc?.toFixed(3)}`
    );
    },
  earlyStopping: tf.callbacks.earlyStopping({
    monitor: "val_loss",
    patience: 10
  })
}
  });

  // ---- SALVATAGGIO MODELLO ----
  if (!fs.existsSync(MODEL_PATH)) fs.mkdirSync(MODEL_PATH, { recursive: true });

  const saveResult = await model.save(tf.io.withSaveHandler(async (artifacts) => {

    if (!fs.existsSync(MODEL_PATH)) {
      fs.mkdirSync(MODEL_PATH, { recursive: true });
    }

    fs.writeFileSync(
      path.join(MODEL_PATH, "model.json"),
      JSON.stringify({
        modelTopology: artifacts.modelTopology,
        weightsManifest: [{
          paths: ["weights.bin"],
          weights: artifacts.weightSpecs
        }]
      }, null, 2)
    );

    fs.writeFileSync(
      path.join(MODEL_PATH, "weights.bin"),
      Buffer.from(artifacts.weightData)
    );

  }));

  // Salva anche la lista ordinata delle label
  const labelsArray = Object.keys(labelMap).sort((a, b) => labelMap[a] - labelMap[b]);
  fs.writeFileSync(path.join(MODEL_PATH, "labels.json"), JSON.stringify(labelsArray, null, 2));

  console.log("Modello salvato in:", MODEL_PATH);
  console.log("Labels salvate in labels.json");
}

main();