<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/components/sign_visual.php';

$pageTitle   = 'Sfida AI';
$currentPage = 'gioca';
$nodeUrl     = NODE_SERVER_URL;

include __DIR__ . '/../includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="../index.php">Home</a>
            <?= hl_icon('arrow-right', 12) ?>
            <a href="../gioca.php">Gioca</a>
            <?= hl_icon('arrow-right', 12) ?>
            <span>Sfida AI</span>
        </nav>
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container">

        <!-- INTRO -->
        <div id="introCard" class="game-stage" style="max-width:760px; margin:0 auto;">
            <div class="feature-icon" style="margin: 0 auto 1rem; background: var(--danger-50); color: var(--danger);">
                <?= hl_icon('flame', 28) ?>
            </div>
            <h1>Sfida AI a tempo</h1>
            <p class="muted mt-3">
                Hai <strong>60 secondi</strong>: per ogni segno richiesto, fallo davanti alla
                webcam. Il modello AI lo riconosce in tempo reale.
            </p>
            <ul style="display:inline-block; text-align:left; margin: var(--s-5) 0;">
                <li class="flex gap-2 mb-2"><?= hl_icon('check-circle', 18, 'alert-icon') ?> Più segni indovini, più punti.</li>
                <li class="flex gap-2 mb-2"><?= hl_icon('check-circle', 18, 'alert-icon') ?> Combo da 3+ moltiplica il punteggio.</li>
                <li class="flex gap-2"><?= hl_icon('check-circle', 18, 'alert-icon') ?> 3 vite: ogni segno saltato = -1 vita.</li>
            </ul>
            <div class="end-actions">
                <button id="sfStartBtn" class="btn btn-primary btn-lg">
                    <?= hl_icon('play', 18) ?> Avvia sfida
                </button>
                <a href="../gioca.php" class="btn btn-secondary btn-lg">Torna alla lista</a>
            </div>
            <div id="sfBootMsg" class="alert alert-info mt-6" style="text-align:left; display:none;">
                <?= hl_icon('camera', 22, 'alert-icon') ?>
                <span>Richiede webcam attiva e backend AI Node.js in esecuzione su <code><?= htmlspecialchars($nodeUrl) ?></code>.</span>
            </div>
        </div>

        <!-- GAME -->
        <div id="gameCard" style="display:none;">
            <div class="game-topbar" style="background: var(--surface); border:1px solid var(--border); border-radius: var(--r-lg); padding: var(--s-4) var(--s-5);">
                <span class="score-pill"><?= hl_icon('star', 16) ?> <span id="sfScore">0</span></span>
                <span class="muted text-sm">Combo x<span id="sfCombo">1</span></span>
                <span class="lives" id="sfLives">
                    <?= hl_icon('heart', 22, 'heart') ?>
                    <?= hl_icon('heart', 22, 'heart') ?>
                    <?= hl_icon('heart', 22, 'heart') ?>
                </span>
                <span class="muted text-sm" id="sfTime">60s</span>
            </div>
            <div class="timer-bar mb-5"><div class="timer-bar-fill" id="sfTimerFill"></div></div>

            <div class="practice-grid">
                <div class="practice-stage">
                    <video    id="sfVideo"  autoplay playsinline muted></video>
                    <canvas   id="sfCanvas" width="640" height="480"></canvas>
                </div>
                <div class="practice-side">
                    <div class="target-card">
                        <h3>Fai il segno</h3>
                        <div class="target-visual" id="sfTargetVisual"></div>
                        <div class="target-name" id="sfTarget">—</div>
                    </div>
                    <div class="feedback-card">
                        <div class="label-row">
                            <strong>Rilevato</strong>
                            <span class="badge" id="sfDetected">—</span>
                        </div>
                        <div class="feedback-message is-neutral" id="sfFeedback">
                            In attesa…
                        </div>
                    </div>
                    <button class="btn btn-secondary btn-block" id="sfQuit">
                        <?= hl_icon('x', 16) ?> Termina sfida
                    </button>
                </div>
            </div>
        </div>

        <!-- END -->
        <div id="endCard" class="game-stage end-screen" style="display:none; max-width:760px; margin:0 auto;">
            <div class="end-trophy"><?= hl_icon('trophy', 56) ?></div>
            <h2 id="sfEndTitle">Tempo scaduto</h2>
            <p class="muted" id="sfEndText"></p>
            <div class="end-stats">
                <div class="end-stat">
                    <div class="num" id="sfEndScore">0</div>
                    <div class="lab">Punteggio</div>
                </div>
                <div class="end-stat">
                    <div class="num" id="sfEndCombo">x1</div>
                    <div class="lab">Combo max</div>
                </div>
            </div>

            <h3 class="mb-3">Top 10 della classifica</h3>
            <ol id="sfLeaderboard" style="text-align:left; max-width: 360px; margin: 0 auto var(--s-5);"></ol>

            <div class="end-actions">
                <button class="btn btn-primary btn-lg" id="sfRestart">
                    <?= hl_icon('rotate', 16) ?> Rigioca
                </button>
                <a href="../gioca.php" class="btn btn-secondary btn-lg">
                    <?= hl_icon('arrow-left', 16) ?> Lista giochi
                </a>
            </div>
        </div>

    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-backend-webgl"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/hands/hands.js"></script>

<script>
window.HL = window.HL || {};
window.HL.NODE_URL = <?= json_encode($nodeUrl) ?>;
</script>
<script src="../js/games/sfida.js"></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
