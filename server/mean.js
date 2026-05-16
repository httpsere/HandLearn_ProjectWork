const fs = require("fs");
const csv = require("csv-parser");
const path = require("path");

const CSV_PATH = path.join(__dirname, "gestures.csv");
const OUTPUT_PATH = path.join(__dirname, "model", "mean_gestures.json");

const gestures = {}; // { A: [ [63 valori], [63 valori], ... ] }

function loadCSV() {
  return new Promise((resolve) => {
    fs.createReadStream(CSV_PATH)
      .pipe(csv())
      .on("data", (row) => {

        const label = row.label;

        const features = [];

        for (let i = 0; i < 21; i++) {
          features.push(
            parseFloat(row[`x${i}`]),
            parseFloat(row[`y${i}`]),
            parseFloat(row[`z${i}`])
          );
        }

        if (!gestures[label]) gestures[label] = [];
        gestures[label].push(features);
      })
      .on("end", resolve);
  });
}

function computeMeans() {
  const means = {};

  for (const label in gestures) {
    const samples = gestures[label];
    const mean = new Array(63).fill(0);

    for (const sample of samples) {
      for (let i = 0; i < 63; i++) {
        mean[i] += sample[i];
      }
    }

    for (let i = 0; i < 63; i++) {
      mean[i] /= samples.length;
    }

    means[label] = mean;
  }

  return means;
}

async function main() {
  console.log("Carico CSV...");
  await loadCSV();

  console.log("Calcolo medie...");
  const means = computeMeans();

  if (!fs.existsSync(path.join(__dirname, "model"))) {
    fs.mkdirSync(path.join(__dirname, "model"));
  }

  fs.writeFileSync(OUTPUT_PATH, JSON.stringify(means, null, 2));

  console.log("File salvato in:", OUTPUT_PATH);
}

main();