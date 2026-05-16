<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/components/sign_visual.php';
require_once __DIR__ . '/config/db.php';

$pageTitle   = 'Home';
$currentPage = 'home';

$user = currentUser();

// Conta record per la sezione "in numeri"
$totLezioni = (int)($conn->query('SELECT COUNT(*) AS n FROM lezioni')->fetch_assoc()['n'] ?? 0);
$totSegni   = (int)($conn->query('SELECT COUNT(*) AS n FROM segni')->fetch_assoc()['n']   ?? 0);
$totUtenti  = (int)($conn->query('SELECT COUNT(*) AS n FROM utenti')->fetch_assoc()['n']  ?? 0);

include __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     HERO
============================================================ -->
<section class="hero">
    <div class="container hero-grid">

        <div>
            <span class="badge badge-primary mb-4">
                <?= hl_icon('sparkles', 14) ?>
                Nuovo · Riconoscimento gesti AI in tempo reale
            </span>
            <h1>Impara la <span class="gradient">Lingua dei Segni</span> Italiana</h1>
            <p class="lead">
                Lezioni guidate, dizionario interattivo, giochi didattici e una webcam
                AI che ti corregge mentre fai i segni. Tutto in un'unica piattaforma
                pensata per studenti, insegnanti e curiosi.
            </p>

            <div class="hero-actions">
                <?php if ($user): ?>
                    <a href="esercitati.php" class="btn btn-primary btn-xl">
                        <?= hl_icon('camera', 20) ?>
                        Esercitati con la webcam
                    </a>
                    <a href="impara.php" class="btn btn-secondary btn-xl">
                        Continua le lezioni
                    </a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary btn-xl">
                        Inizia gratis
                        <?= hl_icon('arrow-right', 20) ?>
                    </a>
                    <a href="esercitati.php" class="btn btn-secondary btn-xl">
                        <?= hl_icon('play', 18) ?>
                        Prova subito senza registrarti
                    </a>
                <?php endif; ?>
            </div>

            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="num"><?= max(26, $totSegni) ?>+</div>
                    <div class="lab">Segni nel dizionario</div>
                </div>
                <div class="hero-stat">
                    <div class="num"><?= max(10, $totLezioni) ?>+</div>
                    <div class="lab">Lezioni</div>
                </div>
                <div class="hero-stat">
                    <div class="num">100%</div>
                    <div class="lab">Open &amp; gratis</div>
                </div>
            </div>
        </div>

        <div class="hero-visual" aria-hidden="true">
            <div class="hero-blob"></div>
            <div class="hero-card">
                <?= render_sign_visual('A', ['size' => 'lg', 'color' => 'violet']) ?>
                <h3 class="mt-4">Lettera A</h3>
                <p class="muted text-sm">Pugno chiuso, pollice di lato</p>
            </div>
            <div class="hero-floats">
                <div class="float-card f1">
                    <span class="float-icon green"><?= hl_icon('check', 18) ?></span>
                    <div>
                        <div class="fw-700 text-sm">Lezione completata</div>
                        <div class="text-xs muted">Alfabeto · Livello 1</div>
                    </div>
                </div>
                <div class="float-card f2">
                    <span class="float-icon amber"><?= hl_icon('flame', 16) ?></span>
                    <div>
                        <div class="fw-700 text-sm">Streak 7 giorni</div>
                        <div class="text-xs muted">+50 XP guadagnati</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ============================================================
     QUICK ACCESS — sezioni principali
============================================================ -->
<section class="section">
    <div class="container">
        <h2 class="section-title">Tutto quello che ti serve, in un solo posto</h2>
        <p class="section-subtitle">Quattro sezioni, un percorso integrato</p>

        <div class="grid grid-3" style="grid-auto-rows: 1fr;">

            <a href="esercitati.php" class="section-card featured">
                <div>
                    <span class="ico"><?= hl_icon('camera', 28) ?></span>
                    <span class="badge" style="background:rgba(255,255,255,.2); color:#fff; border:none;">
                        Cuore della piattaforma
                    </span>
                    <h3 class="mt-3">Esercitati con AI</h3>
                    <p>Mostra il segno alla webcam: il modello AI ti dice se è corretto, in tempo reale.</p>
                </div>
                <span class="arrow"><?= hl_icon('arrow-right', 20) ?></span>
            </a>

            <a href="impara.php" class="section-card violet">
                <div>
                    <span class="ico"><?= hl_icon('book', 28) ?></span>
                    <h3>Impara</h3>
                    <p>Lezioni divise per categorie e livelli, dal principiante all'avanzato.</p>
                </div>
                <span class="arrow"><?= hl_icon('arrow-right', 20) ?></span>
            </a>

            <a href="gioca.php" class="section-card amber">
                <div>
                    <span class="ico"><?= hl_icon('gamepad', 28) ?></span>
                    <h3>Gioca</h3>
                    <p>Quiz, memory, spelling e sfida AI a tempo: impara divertendoti.</p>
                </div>
                <span class="arrow"><?= hl_icon('arrow-right', 20) ?></span>
            </a>

            <a href="dizionario.php" class="section-card emerald">
                <div>
                    <span class="ico"><?= hl_icon('dictionary', 28) ?></span>
                    <h3>Dizionario</h3>
                    <p>Cerca un segno per parola o lettera, con descrizione e collegamento alla pratica.</p>
                </div>
                <span class="arrow"><?= hl_icon('arrow-right', 20) ?></span>
            </a>

            <a href="<?= isLoggedIn() ? 'profilo.php' : 'register.php' ?>" class="section-card sky">
                <div>
                    <span class="ico"><?= hl_icon('user', 28) ?></span>
                    <h3><?= isLoggedIn() ? 'Profilo' : 'Account' ?></h3>
                    <p><?= isLoggedIn()
                            ? 'Visualizza i tuoi progressi, XP e punteggi.'
                            : 'Crea un account per salvare progressi e XP.' ?></p>
                </div>
                <span class="arrow"><?= hl_icon('arrow-right', 20) ?></span>
            </a>

        </div>
    </div>
</section>

<!-- ============================================================
     COSA È LIS / VALORE EDUCATIVO
============================================================ -->
<section class="section" style="padding-top:0;">
    <div class="container">
        <div class="grid grid-2" style="gap:var(--s-8); align-items:center;">
            <div>
                <span class="badge badge-accent mb-3">Inclusione e cultura</span>
                <h2 class="mb-4">Perché imparare la LIS?</h2>
                <p class="muted mb-4">
                    La <strong>Lingua dei Segni Italiana</strong> è una vera lingua,
                    con grammatica, lessico e cultura proprie. È il modo principale
                    di comunicare per la comunità sorda italiana, riconosciuta dallo
                    Stato dal 2021.
                </p>
                <p class="muted mb-4">
                    Imparare la LIS significa abbattere barriere comunicative,
                    avvicinarsi a una cultura ricca e contribuire a una società più
                    accessibile per tutti.
                </p>
                <a href="about.php" class="btn btn-secondary">
                    Scopri il progetto
                    <?= hl_icon('arrow-right', 16) ?>
                </a>
            </div>
            <div class="grid" style="grid-template-columns: repeat(3, 1fr); gap:var(--s-3);">
                <?= render_sign_visual('Ciao',    ['color' => 'violet']) ?>
                <?= render_sign_visual('Grazie',  ['color' => 'amber']) ?>
                <?= render_sign_visual('Amico',   ['color' => 'emerald']) ?>
                <?= render_sign_visual('Mamma',   ['color' => 'pink']) ?>
                <?= render_sign_visual('Felice',  ['color' => 'sky']) ?>
                <?= render_sign_visual('Acqua',   ['color' => 'rose']) ?>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     FEATURES
============================================================ -->
<section class="section" style="background: var(--surface); border-top:1px solid var(--border); border-bottom:1px solid var(--border);">
    <div class="container">
        <h2 class="section-title">Una piattaforma didattica completa</h2>
        <p class="section-subtitle">Pensata per studenti, insegnanti e famiglie</p>

        <div class="grid">
            <div class="feature-card">
                <div class="feature-icon"><?= hl_icon('book', 24) ?></div>
                <h3>Percorsi strutturati</h3>
                <p class="muted">Lezioni divise per categoria e livello, con obiettivi chiari.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon amber"><?= hl_icon('camera', 24) ?></div>
                <h3>Riconoscimento gesti AI</h3>
                <p class="muted">MediaPipe + TensorFlow.js verificano il tuo segno in tempo reale.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon green"><?= hl_icon('gamepad', 24) ?></div>
                <h3>Apprendimento gamificato</h3>
                <p class="muted">Quiz, memory, spelling e sfide a tempo per allenarti divertendoti.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon blue"><?= hl_icon('dictionary', 24) ?></div>
                <h3>Dizionario consultabile</h3>
                <p class="muted">Cerca per parola, lettera o categoria. Ogni segno con scheda dedicata.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon pink"><?= hl_icon('trophy', 24) ?></div>
                <h3>Progressi e XP</h3>
                <p class="muted">Traccia lezioni completate, punteggi e streak quotidiani.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon purple"><?= hl_icon('shield', 24) ?></div>
                <h3>Privacy &amp; locale</h3>
                <p class="muted">Webcam elaborata in locale: nessun video lascia il tuo dispositivo.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     CTA finale
============================================================ -->
<section class="section">
    <div class="container">
        <div class="card card-pad-lg" style="
            background: linear-gradient(135deg, var(--primary), var(--primary-700) 60%, var(--secondary));
            color:#fff; text-align:center; border:none;
        ">
            <h2 style="color:#fff;">Pronto a iniziare?</h2>
            <p style="color:rgba(255,255,255,.85); max-width:560px; margin:var(--s-3) auto var(--s-6);">
                <?= $user
                    ? 'Salta direttamente alla pratica e migliora i tuoi segni con il tutor AI.'
                    : 'Crea un account in meno di un minuto e inizia il tuo percorso nella LIS.' ?>
            </p>
            <div class="hero-actions" style="justify-content:center;">
                <?php if ($user): ?>
                    <a href="esercitati.php" class="btn btn-accent btn-xl">
                        <?= hl_icon('camera', 20) ?>
                        Vai all'esercitazione
                    </a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-accent btn-xl">
                        Registrati gratis
                        <?= hl_icon('arrow-right', 20) ?>
                    </a>
                    <a href="esercitati.php" class="btn btn-secondary btn-xl"
                       style="background:rgba(255,255,255,.18); color:#fff; border-color:rgba(255,255,255,.3);">
                        <?= hl_icon('play', 18) ?>
                        Prova senza registrarti
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
