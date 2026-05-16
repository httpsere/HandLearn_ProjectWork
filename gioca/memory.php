<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/components/sign_visual.php';

$pageTitle = 'Memory LIS';
$currentPage = 'gioca';

/*
|--------------------------------------------------------------------------
| SOLO LETTERE ALFABETO
|--------------------------------------------------------------------------
*/

$alfabeto = [
    'A',
    'B',
    'C',
    'D',
    'E',
    'F',
    'G',
    'H',
    'I',
    'L',
    'M',
    'N',
    'O',
    'P',
    'Q',
    'R',
    'S',
    'T',
    'U',
    'V',
    'Z'
];

/*
|--------------------------------------------------------------------------
| Mischia e prende 8 lettere casuali
|--------------------------------------------------------------------------
*/

shuffle($alfabeto);

$words = array_slice($alfabeto, 0, 8);

include __DIR__ . '/../includes/header.php';
?>

<div class="memory-page">

    <section class="page-header">
        <div class="container">

            <nav class="breadcrumb">
                <a href="<?= BASE_URL ?>/index.php">Home</a>

                <?= hl_icon('arrow-right', 12) ?>

                <a href="<?= BASE_URL ?>/gioca.php">Gioca</a>

                <?= hl_icon('arrow-right', 12) ?>

                <span>Memory LIS</span>
            </nav>

        </div>
    </section>

    <section class="section" style="padding-top:0;">

        <div class="container" style="max-width:760px;">

            <!-- INTRO -->

            <div id="introCard" class="game-stage">

                <h1>Memory LIS</h1>

                <p class="muted mt-3">
                    Trova le coppie lettera ↔ segno LIS.
                </p>

                <div class="end-actions mt-6">

                    <button class="btn btn-primary" id="memStartBtn">
                        <?= hl_icon('play', 18) ?>
                        Inizia
                    </button>

                    <a href="<?= BASE_URL ?>/gioca.php" class="btn btn-secondary">
                        Torna alla lista
                    </a>

                </div>
            </div>

            <!-- GAME -->

            <div id="gameCard" class="game-stage" style="display:none;">

                <div class="game-topbar">

                    <div class="flex gap-2 flex-wrap">

                        <span class="score-pill">
                            Mosse: <span id="movesVal">0</span>
                        </span>

                        <span class="score-pill">
                            Coppie: <strong id="pairsVal">0/8</strong>
                        </span>

                    </div>

                    <a href="<?= BASE_URL ?>/gioca.php" class="btn btn-secondary btn-sm">

                        <?= hl_icon('arrow-left', 16) ?>

                        Torna ai giochi

                    </a>

                </div>

                <div class="memory-board" id="memoryBoard"></div>

            </div>

            <!-- END -->

            <div id="endCard" class="game-stage end-screen" style="display:none;">

                <h2>Fantastico!</h2>

                <p class="muted">
                    Hai trovato tutte le coppie.
                </p>

                <div class="end-stats">

                    <div class="end-stat">
                        <div class="num" id="endMoves">0</div>
                        <div class="lab">Mosse</div>
                    </div>

                    <div class="end-stat">
                        <div class="num" id="endScore">0</div>
                        <div class="lab">Punteggio</div>
                    </div>

                </div>

                <div class="end-actions">

                    <button class="btn btn-primary" id="memRestart">
                        <?= hl_icon('rotate', 16) ?>
                        Rigioca
                    </button>

                </div>

            </div>

        </div>

    </section>

</div>

<script>
    window.MEMORY_WORDS = <?= json_encode($words) ?>;
</script>

<script src="/HandLearn-main/js/games/memory.js"></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>