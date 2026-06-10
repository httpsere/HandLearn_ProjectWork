/* =============================================================
   HandLearn — practice.js
   Gestisce la pagina "Esercitati": webcam, MediaPipe Hands,
   modello TF.js servito dal backend Node, feedback UI.
============================================================= */

(function () {
  const cfg = window.HL || {};
  const NODE_URL = cfg.NODE_URL || "http://localhost:3000";

  const els = {
    video: document.getElementById("video"),
    canvas: document.getElementById("canvas"),
    loader: document.getElementById("stageLoader"),
    loaderText: document.getElementById("loaderText"),
    overlay: document.getElementById("practiceOverlay"),
    detected: document.getElementById("detectedLabel"),
    targetVisual: document.getElementById("targetVisual"),
    targetName: document.getElementById("targetName"),
    prevBtn: document.getElementById("prevTargetBtn"),
    nextBtn: document.getElementById("nextTargetBtn"),
    skipBtn: document.getElementById("skipTargetBtn"),
    confBadge: document.getElementById("confidenceBadge"),
    confFill: document.getElementById("confidenceFill"),
    feedback: document.getElementById("feedbackMsg"),
    progressText: document.getElementById("progressText"),
    progressPills: document.getElementById("progressPills"),
    banner: document.getElementById("aiStatusBanner"),
    bannerMsg: document.getElementById("aiStatusMsg"),
    bannerIcon: document.getElementById("aiStatusIcon"),
  };

  const ctx = els.canvas.getContext("2d");

  let model = null;
  let labels = [];
  let setLabels = [];
  let currentIdx = 0;
  let completedFlags = []; // indici completati
  let lastSuccessAt = 0;
  let camera = null;
  let hands = null;

  /* =====================================================
       UI helpers
    ===================================================== */
  function setBanner(type, text) {
    els.banner.style.display = "";
    els.banner.classList.remove(
      "alert-info",
      "alert-success",
      "alert-warning",
      "alert-error",
    );
    els.banner.classList.add("alert-" + type);
    els.bannerMsg.textContent = text;
  }
  function hideBanner() {
    els.banner.style.display = "none";
  }

  function makeHandSvg() {
    // versione semplificata della "mano" usata nelle card
    return `
        <svg viewBox="0 0 96 96" aria-hidden="true">
          <g fill="white" stroke="rgba(0,0,0,.08)" stroke-width="1">
            <rect x="22" y="20" width="10" height="46" rx="5"/>
            <rect x="36" y="14" width="10" height="52" rx="5"/>
            <rect x="50" y="10" width="10" height="56" rx="5"/>
            <rect x="64" y="18" width="10" height="48" rx="5"/>
            <rect x="6"  y="32" width="10" height="34" rx="5" transform="rotate(-30 11 49)"/>
            <path d="M8 60 Q8 88 44 88 H 70 Q 88 88 88 68 V 50 Q 88 42 76 42 V 60 Z"/>
          </g>
        </svg>`;
  }
  function renderTarget() {
    const label = setLabels[currentIdx] || "—";

    els.targetName.textContent = label;

    // usa immagini locali invece della SVG generata
    els.targetVisual.innerHTML = `
        <img
            src="assets/segni_immagini/${label.toLowerCase()}.png"
            alt="${label}"
            style="
                width:100%;
                height:220px;
                object-fit:contain;
                border-radius:16px;
                background:#fff;
                padding:10px;
            "
            onerror="this.src='assets/default.svg'"
        >
    `;

    renderProgress();
  }

  function renderProgress() {
    els.progressText.textContent = `${completedFlags.filter(Boolean).length} / ${setLabels.length}`;
    els.progressPills.innerHTML = setLabels
      .map((_, i) => {
        const cls = completedFlags[i] ? "pill-step done" : "pill-step";
        return `<span class="${cls}" title="${setLabels[i]}"></span>`;
      })
      .join("");
  }

  function setFeedback(state, text) {
    els.feedback.textContent = text;
    els.feedback.className = "feedback-message";
    els.feedback.classList.add(
      {
        good: "is-good",
        warn: "is-warn",
        bad: "is-bad",
        neutral: "is-neutral",
      }[state] || "is-neutral",
    );
  }

  function setConfidence(pct) {
    const v = Math.max(0, Math.min(100, Math.round(pct)));
    els.confFill.style.width = v + "%";
    els.confBadge.textContent = v + "%";
  }

  /* =====================================================
       Navigazione del set
    ===================================================== */
  function nextTarget(markAsDone = false) {
    if (markAsDone) completedFlags[currentIdx] = true;
    currentIdx = (currentIdx + 1) % setLabels.length;
    renderTarget();
  }
  function prevTarget() {
    currentIdx = (currentIdx - 1 + setLabels.length) % setLabels.length;
    renderTarget();
  }

  els.nextBtn.addEventListener("click", () => nextTarget(false));
  els.prevBtn.addEventListener("click", prevTarget);
  els.skipBtn.addEventListener("click", () => nextTarget(false));

  /* =====================================================
       Caricamento modello
    ===================================================== */
  async function loadModel() {
    els.loaderText.textContent = "Carico il modello AI…";
    try {
      model = await tf.loadLayersModel(NODE_URL + "/model/model.json");
      labels = await fetch(NODE_URL + "/model/labels.json").then((r) =>
        r.json(),
      );

      // Filtra il set al solo intersect col modello (se config viene da DB, evita tentativi inutili)
      if (Array.isArray(cfg.SET_LABELS) && cfg.SET_LABELS.length) {
        setLabels = cfg.SET_LABELS.filter((l) => labels.includes(l));
        if (!setLabels.length) {
          // nessuna label compatibile col modello: cadi sull'alfabeto
          setLabels = labels.slice();
          setBanner(
            "warning",
            "Il set richiesto non è coperto dal modello attuale. Esercitati con tutto l'alfabeto.",
          );
        } else {
          hideBanner();
        }
      } else {
        setLabels = labels.slice();
      }
      completedFlags = new Array(setLabels.length).fill(false);
      renderTarget();
      return true;
    } catch (err) {
      console.error("Modello AI non raggiungibile", err);
      setBanner(
        "error",
        "Backend AI non raggiungibile su " +
          NODE_URL +
          '. Avvia "npm start" nella cartella server/ e ricarica la pagina.',
      );
      els.loader.style.display = "none";
      return false;
    }
  }

  /* =====================================================
       MediaPipe + webcam
    ===================================================== */
  async function startCamera() {
    els.loaderText.textContent = "Inizializzo la webcam…";

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

    try {
      camera = new Camera(els.video, {
        onFrame: async () => {
          if (els.video.readyState === 4)
            await hands.send({ image: els.video });
        },
        width: 640,
        height: 480,
      });
      await camera.start();
      els.loader.style.display = "none";
      els.overlay.style.display = "";
      setFeedback("neutral", "Pronto! Mostra il segno con la mano dominante.");
    } catch (err) {
      console.error(err);
      els.loaderText.textContent = "";
      setBanner(
        "error",
        "Impossibile accedere alla webcam. Concedi i permessi e ricarica.",
      );
      els.loader.style.display = "none";
    }
  }

  /* =====================================================
       Frame handler
    ===================================================== */
  function onResults(results) {
    ctx.save();
    ctx.clearRect(0, 0, els.canvas.width, els.canvas.height);
    ctx.drawImage(results.image, 0, 0, els.canvas.width, els.canvas.height);

    if (!results.multiHandLandmarks || !results.multiHandLandmarks.length) {
      els.detected.textContent = "nessuna mano";
      setConfidence(0);
      setFeedback("neutral", "Avvicina la mano alla webcam.");
      ctx.restore();
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
    try {
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
    } catch (err) {
      /* model not yet loaded */
    }

    els.detected.textContent = predicted;
    setConfidence(confidence * 100);

    const target = setLabels[currentIdx];
    const isMatch = predicted === target;

    let color = "#ef4444"; // rosso
    if (isMatch && confidence >= 0.85) {
      color = "#10b981";
      const now = Date.now();
      if (now - lastSuccessAt > 1000) {
        setFeedback("good", `Bravo! Hai eseguito "${target}".`);
        lastSuccessAt = now;
        setTimeout(() => nextTarget(true), 700);
      }
    } else if (isMatch && confidence >= 0.6) {
      color = "#f59e0b";
      setFeedback("warn", `Quasi! Tieni il segno fermo per "${target}".`);
    } else if (confidence >= 0.85) {
      color = "#f59e0b";
      setFeedback(
        "warn",
        `Stai facendo "${predicted}". Prova invece "${target}".`,
      );
    } else {
      setFeedback("bad", `Non riconosciuto. Il segno target è "${target}".`);
    }

    if (window.HAND_CONNECTIONS) {
      drawConnectors(ctx, lm, HAND_CONNECTIONS, { color, lineWidth: 3 });
    }
    drawLandmarks(ctx, lm, { color, lineWidth: 1 });
    ctx.restore();
  }

  /* =====================================================
       Cleanup pulito (importante per ritorno alla pagina)
    ===================================================== */
  function stopAll() {
    try {
      camera && camera.stop();
    } catch (e) {}
    try {
      hands && hands.close();
    } catch (e) {}
    if (els.video.srcObject) {
      els.video.srcObject.getTracks().forEach((t) => t.stop());
    }
  }
  window.addEventListener("beforeunload", stopAll);
  window.addEventListener("pagehide", stopAll);

  /* =====================================================
       Boot
    ===================================================== */
  (async function boot() {
    const ok = await loadModel();
    if (ok) await startCamera();
  })();
})();
