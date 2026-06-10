/* HandLearn — Sfida AI a tempo */
(function () {
  const NODE_URL = (window.HL && window.HL.NODE_URL) || "http://localhost:3000";
  const DURATION = 60; // secondi
  const SUCCESS_COOLDOWN = 900; // ms tra due successi

  const intro = document.getElementById("introCard");
  const game = document.getElementById("gameCard");
  const endCard = document.getElementById("endCard");
  const startBtn = document.getElementById("sfStartBtn");
  const restart = document.getElementById("sfRestart");
  const quitBtn = document.getElementById("sfQuit");

  const video = document.getElementById("sfVideo");
  const canvas = document.getElementById("sfCanvas");
  const ctx = canvas.getContext("2d");

  const elScore = document.getElementById("sfScore");
  const elCombo = document.getElementById("sfCombo");
  const elTime = document.getElementById("sfTime");
  const elTimer = document.getElementById("sfTimerFill");
  const elTarget = document.getElementById("sfTarget");
  const elTargetV = document.getElementById("sfTargetVisual");
  const elDetect = document.getElementById("sfDetected");
  const elFeedback = document.getElementById("sfFeedback");
  const elLives = document.getElementById("sfLives");
  const elBoot = document.getElementById("sfBootMsg");

  const elEndScore = document.getElementById("sfEndScore");
  const elEndCombo = document.getElementById("sfEndCombo");
  const elEndTitle = document.getElementById("sfEndTitle");
  const elEndText = document.getElementById("sfEndText");
  const elBoard = document.getElementById("sfLeaderboard");

  let model = null,
    labels = [];
  let camera = null,
    hands = null;
  let timeLeft = DURATION,
    score = 0,
    combo = 0,
    comboMax = 1,
    lives = 3;
  let target = null,
    lastTarget = null,
    lastSuccessAt = 0;
  let interval = null,
    running = false;

  function pickRandomTarget() {
    if (!labels.length) return;

    let next;

    do {
      next = labels[Math.floor(Math.random() * labels.length)];
    } while (next === lastTarget && labels.length > 1);

    lastTarget = next;
    target = next;

    // TESTO TARGET
    elTarget.textContent = next;

    // NOME FILE IMMAGINE
    const imgName = next.toLowerCase().trim().replace(/\s+/g, "_");

    // IMMAGINE DALLA CARTELLA
    elTargetV.innerHTML = `
        <img
            src="../assets/segni_immagini/${imgName}.png"
            alt="${next}"
            style="
                width:100%;
                max-width:220px;
                height:auto;
                object-fit:contain;
                border-radius:16px;
            "
            onerror="this.src='../assets/default.svg'"
        >
    `;
  }

  function updateLives() {
    const all = elLives.querySelectorAll("svg.heart");
    all.forEach((h, i) => h.classList.toggle("lost", i >= lives));
  }
  function setFeedback(state, text) {
    elFeedback.className = "feedback-message";
    elFeedback.classList.add(
      {
        good: "is-good",
        warn: "is-warn",
        bad: "is-bad",
        neutral: "is-neutral",
      }[state] || "is-neutral",
    );
    elFeedback.textContent = text;
  }

  async function loadAi() {
    try {
      model = await tf.loadLayersModel(NODE_URL + "/model/model.json");
      labels = await fetch(NODE_URL + "/model/labels.json").then((r) =>
        r.json(),
      );
      return true;
    } catch (err) {
      elBoot.style.display = "";
      return false;
    }
  }

  function setupHands() {
    hands = new Hands({
      locateFile: (f) => "https://cdn.jsdelivr.net/npm/@mediapipe/hands/" + f,
    });
    hands.setOptions({
      selfieMode: true,
      maxNumHands: 1,
      modelComplexity: 1,
      minDetectionConfidence: 0.7,
      minTrackingConfidence: 0.7,
    });
    hands.onResults(onResults);

    camera = new Camera(video, {
      onFrame: async () => {
        if (video.readyState === 4) await hands.send({ image: video });
      },
      width: 640,
      height: 480,
    });
    return camera.start();
  }

  function onResults(results) {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(results.image, 0, 0, canvas.width, canvas.height);

    if (!running) return;

    if (!results.multiHandLandmarks || !results.multiHandLandmarks.length) {
      elDetect.textContent = "—";
      return;
    }

    const lm = results.multiHandLandmarks[0];
    const base = lm[0];
    const scale = Math.hypot(
      lm[9].x - base.x,
      lm[9].y - base.y,
      lm[9].z - base.z,
    );
    const features = lm.flatMap((p) => [
      (p.x - base.x) / scale,
      (p.y - base.y) / scale,
      (p.z - base.z) / scale,
    ]);

    let predicted = "—",
      confidence = 0;
    tf.tidy(() => {
      const input = tf.tensor2d([features]);
      const out = model.predict(input);
      const arr = Array.from(out.dataSync());
      let best = 0;
      arr.forEach((s, i) => {
        if (s > arr[best]) best = i;
      });
      predicted = labels[best];
      confidence = arr[best];
    });

    elDetect.textContent = predicted;
    let color = "#ef4444";

    if (predicted === target && confidence >= 0.85) {
      const now = Date.now();
      if (now - lastSuccessAt > SUCCESS_COOLDOWN) {
        lastSuccessAt = now;
        onSuccess();
      }
      color = "#10b981";
    } else if (predicted === target && confidence >= 0.6) {
      color = "#f59e0b";
      setFeedback("warn", "Quasi! Tieni il segno fermo.");
    } else if (confidence >= 0.85) {
      color = "#f59e0b";
      setFeedback(
        "warn",
        `Stai facendo "${predicted}", invece di "${target}".`,
      );
    }

    if (window.HAND_CONNECTIONS)
      drawConnectors(ctx, lm, HAND_CONNECTIONS, { color, lineWidth: 3 });
    drawLandmarks(ctx, lm, { color, lineWidth: 1 });
  }

  function onSuccess() {
    combo++;
    if (combo > comboMax) comboMax = combo;
    const mult = combo >= 10 ? 5 : combo >= 5 ? 3 : combo >= 3 ? 2 : 1;
    score += 10 * mult;
    elScore.textContent = score;
    elCombo.textContent = mult;
    setFeedback("good", `+${10 * mult} punti! ${target} corretto.`);
    pickRandomTarget();
  }

  function tick() {
    timeLeft -= 0.1;
    if (timeLeft < 0) timeLeft = 0;
    elTime.textContent = Math.ceil(timeLeft) + "s";
    elTimer.style.width = (timeLeft / DURATION) * 100 + "%";
    if (timeLeft <= 0) endGame();
  }

  function stopAll() {
    running = false;
    if (interval) clearInterval(interval);
    try {
      camera && camera.stop();
    } catch (e) {}
    try {
      hands && hands.close();
    } catch (e) {}
    if (video.srcObject) video.srcObject.getTracks().forEach((t) => t.stop());
  }

  async function startGame() {
    intro.style.display = "none";
    endCard.style.display = "none";
    game.style.display = "";

    timeLeft = DURATION;
    score = 0;
    combo = 0;
    comboMax = 1;
    lives = 3;
    elScore.textContent = "0";
    elCombo.textContent = "1";
    elTime.textContent = DURATION + "s";
    updateLives();
    setFeedback("neutral", "Pronto, mostra il segno!");

    const ok = await loadAi();
    if (!ok) return;

    await setupHands();

    pickRandomTarget();
    running = true;
    interval = setInterval(tick, 100);
  }

  async function endGame() {
    if (!running && interval) return; // evita doppio end
    stopAll();

    elEndScore.textContent = score;
    elEndCombo.textContent = "x" + comboMax;
    elEndTitle.textContent =
      score >= 200
        ? "Incredibile!"
        : score >= 100
          ? "Ottimo!"
          : "Tempo scaduto";
    elEndText.textContent =
      score >= 200
        ? "Sei velocissimo. Prova a battere il record."
        : score >= 100
          ? "Ottimo punteggio. Riprova per migliorare."
          : "Continua ad allenarti nelle lezioni e nella sezione Esercitati.";

    // salva sulla classifica Node + nel DB se loggato
    const playerName =
      document.querySelector(".user-chip")?.textContent.trim() || "Player";
    try {
      const r = await fetch(NODE_URL + "/saveScore", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ name: playerName, score }),
      });
      const top = await r.json();
      elBoard.innerHTML = top
        .map(
          (s) => `<li>${escapeHtml(s.name)} — <strong>${s.score}</strong></li>`,
        )
        .join("");
    } catch (e) {
      elBoard.innerHTML =
        "<li>Classifica non disponibile (server AI offline).</li>";
    }
    try {
      fetch("../api/save_progress.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          type: "score",
          gioco: "sfida-ai",
          punteggio: score,
        }),
      }).catch(() => {});
    } catch (e) {}

    game.style.display = "none";
    endCard.style.display = "";
  }

  function escapeHtml(s) {
    return String(s).replace(
      /[&<>"']/g,
      (c) =>
        ({
          "&": "&amp;",
          "<": "&lt;",
          ">": "&gt;",
          '"': "&quot;",
          "'": "&#39;",
        })[c],
    );
  }

  startBtn.addEventListener("click", startGame);
  restart.addEventListener("click", startGame);
  quitBtn.addEventListener("click", endGame);
  window.addEventListener("beforeunload", stopAll);
  window.addEventListener("pagehide", stopAll);
})();
