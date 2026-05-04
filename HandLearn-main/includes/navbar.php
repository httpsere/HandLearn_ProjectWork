<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/components/sign_visual.php';
$user = currentUser();
$ini  = $user
    ? strtoupper(substr($user['nome'], 0, 1) . substr($user['cognome'], 0, 1))
    : '';
?>
<nav class="navbar" aria-label="Menu principale">
    <div class="nav-inner">

        <a href="index.php" class="navbar-brand" aria-label="HandLearn home">
            <span class="logo-mark">
                <?= hl_icon('sparkles', 22) ?>
            </span>
            HandLearn
        </a>

        <ul class="nav-links" id="navLinks">
            <li><a href="index.php"      class="<?= $currentPage==='home'       ? 'active' : '' ?>">Home</a></li>
            <li><a href="impara.php"     class="<?= $currentPage==='impara'     ? 'active' : '' ?>">Impara</a></li>
            <li><a href="esercitati.php" class="cta-link <?= $currentPage==='esercitati' ? 'active' : '' ?>"
                   title="La pratica con AI è il cuore di HandLearn">
                Esercitati
            </a></li>
            <li><a href="gioca.php"      class="<?= $currentPage==='gioca'      ? 'active' : '' ?>">Gioca</a></li>
            <li><a href="dizionario.php" class="<?= $currentPage==='dizionario' ? 'active' : '' ?>">Dizionario</a></li>
            <li><a href="about.php"      class="<?= $currentPage==='about'      ? 'active' : '' ?>">Chi siamo</a></li>
        </ul>

        <div class="nav-auth" id="navAuth">
            <?php if ($user): ?>
                <a href="profilo.php" class="user-chip" aria-label="Vai al profilo">
                    <span class="avatar-sm"><?= htmlspecialchars($ini) ?></span>
                    <?= htmlspecialchars($user['nome']) ?>
                </a>
                <a href="logout.php" class="btn-link" title="Esci">
                    <?= hl_icon('logout', 18) ?>
                </a>
            <?php else: ?>
                <a href="login.php"    class="btn-link">Accedi</a>
                <a href="register.php" class="btn btn-primary btn-sm">Registrati</a>
            <?php endif; ?>
        </div>

        <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Apri menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>
