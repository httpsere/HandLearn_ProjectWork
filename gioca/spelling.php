<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/components/sign_visual.php';

$pageTitle   = 'Spelling';
$currentPage = 'gioca';

// Una mini lista di parole semplici da fare in spelling
$words = ['CIAO','CASA','MAMMA','CANE','SOLE','PANE','ARTE','LIBRO'];
include __DIR__ . '/../includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="../index.php">Home</a>
            <?= hl_icon('arrow-right', 12) ?>
            <a href="../gioca.php">Gioca</a>
            <?= hl_icon('arrow-right', 12) ?>
            <span>Spelling</span>
        </nav>
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container" style="max-width:760px;">

        <div id="introCard" class="game-stage">
            <div class="feature-icon green" style="margin: 0 auto 1rem;"><?= hl_icon('book', 28) ?></div>
            <h1>Spelling</h1>
            <p class="muted mt-3">Vedrai la sequenza dei segni dell'alfabeto: scrivi la parola corrispondente.</p>
            <div class="end-actions mt-6">
                <button class="btn btn-primary btn-lg" id="spStart">
                    <?= hl_icon('play', 18) ?> Inizia
                </button>
                <a href="../gioca.php" class="btn btn-secondary btn-lg">Torna alla lista</a>
            </div>
        </div>

        <div id="gameCard" class="game-stage" style="display:none;">
            <div class="game-topbar">
                <span class="score-pill"><?= hl_icon('star', 16) ?> <span id="spScore">0</span></span>
                <span class="muted text-sm" id="spRound">Parola 1 / <?= count($words) ?></span>
            </div>
            <div id="spLetters" class="flex" style="justify-content:center; flex-wrap:wrap; margin-bottom: var(--s-6);"></div>
            <input type="text" id="spInput" class="form-control" autocomplete="off"
                   style="text-align:center; font-size: 1.4rem; font-weight: 700; max-width: 320px; margin: 0 auto;"
                   placeholder="Scrivi la parola...">
            <p id="spHint" class="text-sm muted mt-3"></p>
            <button id="spCheck" class="btn btn-primary btn-lg mt-4">Verifica</button>
        </div>

        <div id="endCard" class="game-stage end-screen" style="display:none;">
            <div class="end-trophy"><?= hl_icon('trophy', 56) ?></div>
            <h2>Spelling completato!</h2>
            <div class="end-stats">
                <div class="end-stat">
                    <div class="num" id="endScore">0</div>
                    <div class="lab">Punteggio</div>
                </div>
                <div class="end-stat">
                    <div class="num" id="endRight">0</div>
                    <div class="lab">Parole corrette</div>
                </div>
            </div>
            <div class="end-actions">
                <button class="btn btn-primary btn-lg" id="spRestart">
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
window.SPELLING_WORDS = <?= json_encode($words) ?>;
</script>
<script src="../js/games/spelling.js"></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
