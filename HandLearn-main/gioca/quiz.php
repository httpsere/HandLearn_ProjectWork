<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/components/sign_visual.php';

$pageTitle   = 'Quiz dei segni';
$currentPage = 'gioca';

// Genero un set casuale di parole dal DB (10 round + distrattori)
$res = $conn->query('SELECT parola, tipo FROM segni ORDER BY RAND() LIMIT 30');
$pool = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

if (count($pool) < 4) {
    $pool = [
        ['parola'=>'Ciao'],['parola'=>'Grazie'],['parola'=>'Acqua'],['parola'=>'Casa'],
        ['parola'=>'Mamma'],['parola'=>'Felice'],['parola'=>'Amore'],['parola'=>'Cane'],
    ];
}

include __DIR__ . '/../includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="../index.php">Home</a>
            <?= hl_icon('arrow-right', 12) ?>
            <a href="../gioca.php">Gioca</a>
            <?= hl_icon('arrow-right', 12) ?>
            <span>Quiz</span>
        </nav>
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container" style="max-width:760px;">

        <!-- INTRO -->
        <div id="introCard" class="game-stage">
            <div class="feature-icon" style="margin: 0 auto 1rem;"><?= hl_icon('target', 28) ?></div>
            <h1>Quiz dei segni</h1>
            <p class="muted mt-3">10 domande: per ogni segno mostrato, scegli la parola corretta.</p>
            <div class="end-actions mt-6">
                <button class="btn btn-primary btn-lg" id="quizStartBtn">
                    <?= hl_icon('play', 18) ?> Inizia ora
                </button>
                <a href="../gioca.php" class="btn btn-secondary btn-lg">Torna alla lista</a>
            </div>
        </div>

        <!-- GAME -->
        <div id="gameCard" class="game-stage" style="display:none;">
            <div class="game-topbar">
                <span class="score-pill">
                    <?= hl_icon('star', 16) ?> <span id="scoreVal">0</span>
                </span>
                <span class="muted text-sm" id="roundInfo">Round 1 / 10</span>
            </div>

            <div id="signTarget" style="margin: 0 auto var(--s-6); max-width: 240px;"></div>
            <h2 id="questionText">Quale parola corrisponde a questo segno?</h2>

            <div class="choices" id="choices"></div>
            <p class="text-sm muted" id="hint">Clicca una risposta</p>
        </div>

        <!-- END -->
        <div id="endCard" class="game-stage end-screen" style="display:none;">
            <div class="end-trophy"><?= hl_icon('trophy', 56) ?></div>
            <h2 id="endTitle">Quiz completato!</h2>
            <p class="muted" id="endText"></p>
            <div class="end-stats">
                <div class="end-stat">
                    <div class="num" id="endScore">0</div>
                    <div class="lab">Punteggio</div>
                </div>
                <div class="end-stat">
                    <div class="num" id="endCorrect">0/10</div>
                    <div class="lab">Risposte corrette</div>
                </div>
            </div>
            <div class="end-actions">
                <button class="btn btn-primary btn-lg" id="restartBtn">
                    <?= hl_icon('rotate', 16) ?> Rigioca
                </button>
                <a href="../gioca.php" class="btn btn-secondary btn-lg">
                    <?= hl_icon('arrow-left', 16) ?> Lista giochi
                </a>
            </div>
        </div>

    </div>
</section>

<script>
window.QUIZ_POOL = <?= json_encode(array_values(array_unique(array_column($pool, 'parola')))) ?>;
</script>
<script src="../js/games/quiz.js"></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
