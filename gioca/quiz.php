<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/components/sign_visual.php';

$pageTitle = 'Quiz dei segni';
$currentPage = 'gioca';

$mode = $_GET['mode'] ?? 'alfabeto';

/*
|--------------------------------------------------------------------------
| SOLO ALFABETO oppure TUTTI
|--------------------------------------------------------------------------
*/
if ($mode === 'all') {
    $stmt = $conn->prepare("
        SELECT parola
        FROM segni
        ORDER BY RAND()
        LIMIT 30
    ");
} else {

    // SOLO LETTERE
    $stmt = $conn->prepare("
        SELECT parola
        FROM segni
        WHERE tipo = 'alfabeto'
        ORDER BY RAND()
        LIMIT 30
    ");
}

$stmt->execute();
$res = $stmt->get_result();

$pool = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

if (count($pool) < 4) {
    $pool = [
        ['parola' => 'A'],
        ['parola' => 'B'],
        ['parola' => 'C'],
        ['parola' => 'D'],
        ['parola' => 'E'],
        ['parola' => 'F'],
        ['parola' => 'G'],
        ['parola' => 'H'],
        ['parola' => 'I'],
        ['parola' => 'J'],
        ['parola' => 'K'],
        ['parola' => 'L'],
        ['parola' => 'M'],
        ['parola' => 'N'],
        ['parola' => 'O'],
        ['parola' => 'P'],
        ['parola' => 'Q'],
        ['parola' => 'R'],
        ['parola' => 'S'],
        ['parola' => 'T'],
        ['parola' => 'U'],
        ['parola' => 'V'],
        ['parola' => 'W'],
        ['parola' => 'X'],
        ['parola' => 'Y'],
        ['parola' => 'Z']
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

            <div class="feature-icon" style="margin: 0 auto 1rem;">
                <?= hl_icon('target', 28) ?>
            </div>

            <h1>Quiz dei segni</h1>

            <p class="muted mt-3">
                Guarda l'immagine del segno e scegli la risposta corretta.
            </p>

            <!-- SCELTA MODALITÀ -->
            <div style="margin-top:2rem; display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">

                <a href="?mode=alfabeto" class="btn <?= $mode !== 'all' ? 'btn-primary' : 'btn-secondary' ?>">
                    Solo alfabeto
                </a>

                <a href="?mode=all" class="btn <?= $mode === 'all' ? 'btn-primary' : 'btn-secondary' ?>">
                    Tutti i segni
                </a>

            </div>

            <div class="end-actions mt-6">
                <button class="btn btn-primary btn-lg" id="quizStartBtn">
                    <?= hl_icon('play', 18) ?> Inizia ora
                </button>

                <a href="../gioca.php" class="btn btn-secondary btn-lg">
                    Torna alla lista
                </a>
            </div>
        </div>

        <!-- GAME -->
        <div id="gameCard" class="game-stage" style="display:none;">

            <div class="game-topbar">
                <span class="score-pill">
                    <?= hl_icon('star', 16) ?>
                    <span id="scoreVal">0</span>
                </span>

                <span class="muted text-sm" id="roundInfo">
                    Round 1 / 10
                </span>
            </div>

            <div id="signTarget" style="margin:0 auto var(--s-6); max-width:260px;">
            </div>

            <h2>Quale segno è questo?</h2>

            <div class="choices" id="choices"></div>

            <p class="text-sm muted" id="hint">
                Clicca una risposta
            </p>

        </div>

        <!-- END -->
        <div id="endCard" class="game-stage end-screen" style="display:none;">

            <div class="end-trophy">
                <?= hl_icon('trophy', 56) ?>
            </div>

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
                    <?= hl_icon('rotate', 16) ?>
                    Rigioca
                </button>

                <a href="../gioca.php" class="btn btn-secondary btn-lg">

                    <?= hl_icon('arrow-left', 16) ?>
                    Lista giochi

                </a>

            </div>
        </div>

    </div>
</section>

<script>
    window.QUIZ_POOL =
        <?= json_encode(array_values(array_unique(array_column($pool, 'parola')))) ?>;
</script>

<script src="../js/games/quiz.js"></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>