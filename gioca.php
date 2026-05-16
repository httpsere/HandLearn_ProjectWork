<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/components/sign_visual.php';

$pageTitle   = 'Gioca';
$currentPage = 'gioca';
include __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <?= hl_icon('arrow-right', 12) ?>
            <span>Gioca</span>
        </nav>
        <h1>Giochi didattici</h1>
        <p>Allenati divertendoti: quiz, memory, spelling e sfida AI. Ogni partita
           tiene traccia del punteggio e ti mostra dove migliorare.</p>
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container">
        <div class="grid grid-2">

            <a href="gioca/quiz.php" class="game-card">
                <div class="game-card-cover violet">
                    <span class="game-tag">Tutti i livelli</span>
                    <?= hl_icon('target', 80) ?>
                </div>
                <div class="game-card-body">
                    <h3>Quiz dei segni</h3>
                    <p>10 domande a scelta multipla: per ogni segno scegli la parola corretta.</p>
                    <div class="game-card-stats">
                        <span><?= hl_icon('sparkles', 14) ?> 10 round</span>
                        <span><?= hl_icon('star', 14) ?> +10 XP a risposta</span>
                    </div>
                    <span class="btn btn-game violet btn-block">
                        <?= hl_icon('play', 16) ?> Inizia quiz
                    </span>
                </div>
            </a>

            <a href="gioca/memory.php" class="game-card">
                <div class="game-card-cover amber">
                    <span class="game-tag">Principiante</span>
                    <?= hl_icon('puzzle', 80) ?>
                </div>
                <div class="game-card-body">
                    <h3>Memory LIS</h3>
                    <p>Trova le coppie segno ↔ parola sulla griglia. Allena la memoria visiva.</p>
                    <div class="game-card-stats">
                        <span><?= hl_icon('sparkles', 14) ?> 8 coppie</span>
                        <span><?= hl_icon('star', 14) ?> Bonus tempo</span>
                    </div>
                    <span class="btn btn-game amber btn-block">
                            <?= hl_icon('play', 16) ?> Gioca
                    </span>
                </div>
            </a>

            <a href="gioca/spelling.php" class="game-card">
                <div class="game-card-cover emerald">
                    <span class="game-tag">Intermedio</span>
                    <?= hl_icon('book', 80) ?>
                </div>
                <div class="game-card-body">
                    <h3>Spelling</h3>
                    <p>Una sequenza di lettere LIS: scrivi la parola che vedi composta.</p>
                    <div class="game-card-stats">
                        <span><?= hl_icon('sparkles', 14) ?> 8 parole</span>
                        <span><?= hl_icon('star', 14) ?> Combo streak</span>
                    </div>
                    <span class="btn btn-game emerald btn-block">
                            <?= hl_icon('play', 16) ?> Inizia
                    </span>
                </div>
            </a>

            <a href="gioca/sfida.php" class="game-card">
                <div class="game-card-cover rose">
                    <span class="game-tag">AI · Avanzato</span>
                    <?= hl_icon('camera', 80) ?>
                </div>
                <div class="game-card-body">
                    <h3>Sfida AI</h3>
                    <p>Riconoscimento gesti con webcam: quanti segni riesci a fare in 60 secondi?</p>
                    <div class="game-card-stats">
                        <span><?= hl_icon('flame', 14) ?> 60 sec</span>
                        <span><?= hl_icon('trophy', 14) ?> Classifica</span>
                    </div>
                    <span class="btn btn-game rose btn-block">
                        <?= hl_icon('play', 16) ?> Avvia sfida
                    </span>
                </div>
            </a>

        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
