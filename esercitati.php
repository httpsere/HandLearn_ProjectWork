<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/components/sign_visual.php';

$pageTitle = 'Esercitati';
$currentPage = 'esercitati';

$nodeUrl = NODE_SERVER_URL;

// Determina il set di segni da praticare
$mode = 'alfabeto';   // alfabeto | lezione | segno
$setLabels = null;
$titoloSet = 'Alfabeto';

if (isset($_GET['segno']) && $_GET['segno'] !== '') {
    $mode = 'segno';
    $setLabels = [strtoupper(trim($_GET['segno']))];
    $titoloSet = 'Segno: ' . htmlspecialchars($_GET['segno']);
} elseif (isset($_GET['lezione'])) {
    $mode = 'lezione';
    $lid = (int) $_GET['lezione'];
    $stmt = $conn->prepare(
        'SELECT s.parola
         FROM lezioni_segni ls
         JOIN segni s ON s.id = ls.segno_id
         WHERE ls.lezione_id = ?
         ORDER BY ls.ordine, s.parola'
    );
    $stmt->bind_param('i', $lid);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $setLabels = array_map(fn($r) => strtoupper($r['parola']), $rows);

    $stmt2 = $conn->prepare('SELECT titolo FROM lezioni WHERE id=?');
    $stmt2->bind_param('i', $lid);
    $stmt2->execute();
    $titoloSet = 'Lezione: ' . ($stmt2->get_result()->fetch_assoc()['titolo'] ?? '');
}

include __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <?= hl_icon('arrow-right', 12) ?>
            <span>Esercitati</span>
        </nav>
        <h1>Esercitati con la webcam</h1>
        <p>Mostra il segno alla telecamera: il modello AI ti dice in tempo reale se è
            corretto. Il riconoscimento avviene <strong>localmente nel tuo browser</strong>:
            nessun video viene inviato a nessuno.</p>
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container">

        <!-- Banner stato server -->
        <div id="aiStatusBanner" class="alert alert-info" style="display:none;">
            <span id="aiStatusIcon"></span>
            <span id="aiStatusMsg">Controllo del backend AI...</span>
        </div>

        <div class="practice-grid">

            <!-- ============ Stage webcam ============ -->
            <div>
                <div class="practice-stage">
                    <video id="video" autoplay playsinline muted></video>
                    <canvas id="canvas" width="640" height="480"></canvas>

                    <!-- Loader iniziale -->
                    <div id="stageLoader" style="
                        position:absolute; inset:0; z-index:4;
                        display:grid; place-items:center; gap:1rem;
                        background:rgba(15,23,42,.85); color:#fff; text-align:center; padding:1rem;">
                        <span class="spinner"></span>
                        <div>
                            <strong id="loaderText">Inizializzazione webcam e modello AI…</strong>
                            <p class="text-sm" style="opacity:.85; margin-top:.5rem;">
                                Concedi l'accesso alla webcam quando richiesto dal browser.
                            </p>
                        </div>
                    </div>

                    <!-- Overlay live -->
                    <div class="practice-overlay" id="practiceOverlay" style="display:none;">
                        <div class="practice-pill live">
                            <span class="dot"></span> LIVE
                        </div>
                        <div class="practice-status-line">
                            <span>Rilevato: <strong id="detectedLabel">—</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Progress pills -->
                <div class="card card-pad-lg mt-5">
                    <div class="flex-between mb-3">
                        <strong>Progresso esercizio</strong>
                        <span class="muted text-sm" id="progressText">0 / 0</span>
                    </div>
                    <div class="progress-pills" id="progressPills"></div>
                </div>
            </div>

            <!-- ============ Side panel ============ -->
            <div class="practice-side">

                <!-- Target card -->
                <div class="target-card" id="targetCard">
                    <h3>Riproduci questo segno</h3>
                    <div class="target-visual" id="targetVisual">
                        <i
                        g id="targetImage" src="assets/signs/default.jpg" alt="Segno"
                          
                        style="width:100%; height:100%; object-fit:contain; border-radius:16px;">
                    </div>
                    
                    <div class="target-name" id="targetName">—</div>
                    <div class="actions">
                        <button class="btn btn-secondary btn-sm" id="prevTargetBtn" aria-label="Segno precedente">
                            <?= hl_icon('arrow-left', 16) ?>
                        </button>
                        <button class="btn btn-secondary btn-sm" id="skipTargetBtn">
                            Salta
                        </button>
                        <button class="btn btn-secondary btn-sm" id="nextTargetBtn" aria-label="Segno successivo">
                            <?= hl_icon('arrow-right', 16) ?>
                        </button>
                    </div>
                </div>

                <!-- Feedback -->
                <div class="feedback-card">
                    <div class="label-row">
                        <strong>Feedback</strong>
                        <span class="badge" id="confidenceBadge">0%</span>
                    </div>
                    <div class="feedback-message is-neutral" id="feedbackMsg">
                        In attesa del primo gesto…
                    </div>
                    <div class="confidence">
                        <div class="confidence-fill" id="confidenceFill"></div>
                    </div>
                </div>

                <!-- Set info -->
                <div class="card card-body">
                    <div class="flex-between">
                        <div>
                            <div class="muted text-sm">Set in pratica</div>
                            <strong id="setTitle"><?= htmlspecialchars($titoloSet) ?></strong>
                        </div>
                        <a href="impara.php" class="btn btn-ghost btn-sm">Cambia</a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

<!-- ============================================================
     TF.js + MediaPipe Hands
============================================================ -->
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-backend-webgl"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/hands/hands.js"></script>

<script>
    window.HL = {
        NODE_URL: <?= json_encode($nodeUrl) ?>,
        SET_LABELS: <?= json_encode($setLabels) ?>,
        SET_TITLE: <?= json_encode($titoloSet) ?>
    };
</script>
<script src="js/practice.js"></script>

<?php include __DIR__ . '/includes/footer.php'; ?>