<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/components/sign_visual.php';

$pageTitle   = 'Memory LIS';
$currentPage = 'gioca';

$pool = $conn->query('SELECT parola FROM segni ORDER BY RAND() LIMIT 8')->fetch_all(MYSQLI_ASSOC);
$words = array_map(fn($r) => $r['parola'], $pool);
if (count($words) < 6) {
    $words = ['Ciao','Grazie','Acqua','Casa','Mamma','Felice','Amore','Cane'];
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
            <span>Memory LIS</span>
        </nav>
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container" style="max-width:760px;">

        <div id="introCard" class="game-stage">
            <div class="feature-icon amber" style="margin: 0 auto 1rem;"><?= hl_icon('puzzle', 28) ?></div>
            <h1>Memory LIS</h1>
            <p class="muted mt-3">Trova le coppie segno ↔ parola. Fai meno mosse possibili!</p>
            <div class="end-actions mt-6">
                <button class="btn btn-primary btn-lg" id="memStartBtn">
                    <?= hl_icon('play', 18) ?> Inizia
                </button>
                <a href="../gioca.php" class="btn btn-secondary btn-lg">Torna alla lista</a>
            </div>
        </div>

        <div id="gameCard" class="game-stage" style="display:none;">
            <div class="game-topbar">
                <span class="score-pill">Mosse: <span id="movesVal">0</span></span>
                <span class="muted text-sm">Coppie: <strong id="pairsVal">0/8</strong></span>
            </div>
            <div class="memory-board" id="memoryBoard"></div>
        </div>

        <div id="endCard" class="game-stage end-screen" style="display:none;">
            <div class="end-trophy"><?= hl_icon('trophy', 56) ?></div>
            <h2>Fantastico!</h2>
            <p class="muted">Hai trovato tutte le coppie.</p>
            <div class="end-stats">
                <div class="end-stat">
                    <div class="num" id="endMoves">0</div>
                    <div class="lab">Mosse totali</div>
                </div>
                <div class="end-stat">
                    <div class="num" id="endScore">0</div>
                    <div class="lab">Punteggio</div>
                </div>
            </div>
            <div class="end-actions">
                <button class="btn btn-primary btn-lg" id="memRestart">
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
window.MEMORY_WORDS = <?= json_encode($words) ?>;
</script>
<script src="../js/games/memory.js"></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
