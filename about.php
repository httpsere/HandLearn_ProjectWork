<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/components/sign_visual.php';

$pageTitle   = 'Chi siamo';
$currentPage = 'about';
include __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <?= hl_icon('arrow-right', 12) ?>
            <span>Chi siamo</span>
        </nav>
        <h1>Una piattaforma per l'inclusione</h1>
        <p>HandLearn nasce per rendere la Lingua dei Segni Italiana accessibile a tutti
           grazie a un mix di didattica strutturata, gamification e intelligenza artificiale.</p>
    </div>
</section>

<!-- MISSION -->
<section class="section" style="padding-top:0;">
    <div class="container">
        <div class="grid grid-2" style="align-items:center;">
            <div>
                <span class="badge badge-primary mb-3">La nostra missione</span>
                <h2 class="mb-4">Comunicare senza barriere</h2>
                <p class="muted mb-4">
                    Crediamo che la comunicazione sia un diritto fondamentale. La LIS
                    non è solo un mezzo: è una lingua ricca di cultura, storia e
                    identità, riconosciuta dallo Stato italiano dal 2021.
                </p>
                <p class="muted">
                    HandLearn vuole essere il punto di partenza per chiunque desideri
                    avvicinarsi alla LIS — studenti, insegnanti, familiari di persone
                    sorde o semplicemente curiosi — con un'esperienza didattica
                    moderna, accessibile e coinvolgente.
                </p>
            </div>
            <div class="grid" style="grid-template-columns: repeat(3, 1fr); gap: var(--s-3);">
                <?= render_sign_visual('Ciao',   ['color' => 'violet']) ?>
                <?= render_sign_visual('Amico',  ['color' => 'amber']) ?>
                <?= render_sign_visual('Grazie', ['color' => 'emerald']) ?>
                <?= render_sign_visual('Mamma',  ['color' => 'pink']) ?>
                <?= render_sign_visual('Felice', ['color' => 'sky']) ?>
                <?= render_sign_visual('Acqua',  ['color' => 'rose']) ?>
            </div>
        </div>
    </div>
</section>

<!-- VALUES -->
<section class="section" style="background: var(--surface); border-top:1px solid var(--border); border-bottom:1px solid var(--border);">
    <div class="container">
        <h2 class="section-title">I nostri valori</h2>
        <p class="section-subtitle">I principi che guidano ogni nostra scelta progettuale</p>

        <div class="grid">
            <div class="feature-card">
                <div class="feature-icon"><?= hl_icon('shield', 24) ?></div>
                <h3>Accessibilità</h3>
                <p class="muted">Interfaccia con focus visibile, contrasti AA, supporto tastiera e lettori di schermo.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon green"><?= hl_icon('heart', 24) ?></div>
                <h3>Inclusione</h3>
                <p class="muted">Una piattaforma pensata per avvicinare comunità sorda e udente, senza barriere.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon amber"><?= hl_icon('star', 24) ?></div>
                <h3>Qualità didattica</h3>
                <p class="muted">Lezioni strutturate per livelli, con esercizi mirati e feedback immediato.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon blue"><?= hl_icon('zap', 24) ?></div>
                <h3>Innovazione</h3>
                <p class="muted">AI di riconoscimento gesti, gamification e UX moderna al servizio dell'apprendimento.</p>
            </div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="section" id="tecnologia">
    <div class="container">
        <h2 class="section-title">Come funziona la tecnologia</h2>
        <p class="section-subtitle">Una pipeline trasparente, locale, privacy-first</p>

        <div class="grid grid-3">
            <div class="card card-pad-lg">
                <div class="feature-icon"><?= hl_icon('camera', 24) ?></div>
                <h3>1. Webcam locale</h3>
                <p class="muted">Il video viene elaborato direttamente nel browser. Nulla viene caricato online.</p>
            </div>
            <div class="card card-pad-lg">
                <div class="feature-icon amber"><?= hl_icon('sparkles', 24) ?></div>
                <h3>2. MediaPipe Hands</h3>
                <p class="muted">Estrae 21 punti chiave della mano (landmark) frame per frame.</p>
            </div>
            <div class="card card-pad-lg">
                <div class="feature-icon green"><?= hl_icon('check-circle', 24) ?></div>
                <h3>3. TensorFlow.js</h3>
                <p class="muted">Una rete neurale classifica il gesto e ti mostra immediatamente il feedback.</p>
            </div>
        </div>
    </div>
</section>

<!-- TEAM / CONTEXT -->
<section class="section" id="contesto" style="background: var(--surface); border-top:1px solid var(--border);">
    <div class="container">
        <h2 class="section-title">Contesto del progetto</h2>
        <p class="section-subtitle">HandLearn è un progetto didattico open</p>

        <div class="grid">
            <div class="feature-card">
                <div class="feature-icon"><?= hl_icon('graduation', 24) ?></div>
                <h3>Origine scolastica</h3>
                <p class="muted">Sviluppato come progetto didattico universitario/scolastico, con finalità educative.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon amber"><?= hl_icon('book', 24) ?></div>
                <h3>Stack open source</h3>
                <p class="muted">PHP, MySQL, JavaScript, Node.js, MediaPipe e TensorFlow.js — tutto verificabile.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon green"><?= hl_icon('globe', 24) ?></div>
                <h3>Per la comunità</h3>
                <p class="muted">Senza scopo di lucro: l'obiettivo è diffondere conoscenza della LIS.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section">
    <div class="container">
        <div class="card card-pad-lg" style="
            background: linear-gradient(135deg, var(--primary), var(--primary-700) 70%, var(--secondary));
            color:#fff; text-align:center; border:none;
        ">
            <h2 style="color:#fff;">Pronto a iniziare?</h2>
            <p style="color:rgba(255,255,255,.85); max-width:560px; margin:var(--s-3) auto var(--s-6);">
                Registrati gratis e prova subito il riconoscimento gesti AI dalla tua webcam.
            </p>
            <div class="hero-actions" style="justify-content:center;">
                <?php if (isLoggedIn()): ?>
                    <a href="esercitati.php" class="btn btn-accent btn-xl">
                        <?= hl_icon('camera', 20) ?> Vai a esercitarti
                    </a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-accent btn-xl">
                        Crea un account gratis
                        <?= hl_icon('arrow-right', 20) ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
