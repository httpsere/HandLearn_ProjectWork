/**
 * HandLearn - Backend Node.js
 *
 * Espone:
 *  - GET  /                         pagina di test (recognize)
 *  - GET  /<page>.html              UI integrate (recognize/tutor/collect/giocoPunti)
 *  - GET  /model/*                  modello TensorFlow.js (model.json + weights.bin + labels.json)
 *  - POST /save                     salva un campione di gesto nel CSV (training data)
 *  - GET  /scores                   top 10 punteggi
 *  - POST /saveScore                salva un nuovo punteggio
 *
 * CORS abilitato per permettere al frontend PHP di caricare il modello
 * e usare le API senza errori cross-origin.
 */

const express = require('express');
const path    = require('path');
const fs      = require('fs');

const app  = express();
const PORT = process.env.PORT || 3000;

const PUBLIC_DIR  = path.join(__dirname, 'public');
const MODEL_DIR   = path.join(__dirname, 'model');
const CSV_FILE    = path.join(__dirname, 'gestures.csv');
const SCORES_FILE = path.join(__dirname, 'scores.json');

/* ---------- Middleware ---------- */
app.use((req, res, next) => {
    res.setHeader('Access-Control-Allow-Origin',  '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');
    if (req.method === 'OPTIONS') return res.sendStatus(204);
    next();
});

app.use(express.json());
app.use(express.static(PUBLIC_DIR));
app.use('/model', express.static(MODEL_DIR));

/* ---------- Inizializzazione CSV ---------- */
if (!fs.existsSync(CSV_FILE)) {
    const header = ['label'];
    for (let i = 0; i < 21; i++) header.push(`x${i}`, `y${i}`, `z${i}`);
    fs.writeFileSync(CSV_FILE, header.join(',') + '\n');
    console.log('[handlearn] CSV creato con header.');
}

/* ---------- Endpoints ---------- */

app.get('/', (_req, res) => {
    res.sendFile(path.join(PUBLIC_DIR, 'recognize.html'));
});

app.post('/save', (req, res) => {
    const { features, label } = req.body || {};
    if (!features || features.length !== 63 || !label) {
        return res.status(400).send('Invalid data');
    }
    const line = label + ',' + features.join(',') + '\n';
    fs.appendFileSync(CSV_FILE, line);
    console.log(`[handlearn] gesto salvato: ${label}`);
    res.send('OK');
});

/* ---------- Punteggi ---------- */
function loadScores() {
    if (!fs.existsSync(SCORES_FILE)) return [];
    try { return JSON.parse(fs.readFileSync(SCORES_FILE, 'utf8')); }
    catch { return []; }
}
function saveScores(scores) {
    fs.writeFileSync(SCORES_FILE, JSON.stringify(scores, null, 2));
}

app.post('/saveScore', (req, res) => {
    const { name, score } = req.body || {};
    if (!name || typeof score !== 'number') {
        return res.status(400).send('Invalid data');
    }
    const scores = loadScores();
    scores.push({ name, score, date: new Date().toISOString() });
    scores.sort((a, b) => b.score - a.score);
    if (scores.length > 10) scores.length = 10;
    saveScores(scores);
    res.json(scores);
});

app.get('/scores', (_req, res) => {
    res.json(loadScores().slice(0, 10));
});

/* ---------- Avvio ---------- */
app.listen(PORT, () => {
    console.log(`[handlearn] Backend AI in ascolto su http://localhost:${PORT}`);
});
