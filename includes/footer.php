<?php
require_once __DIR__ . '/../config/db.php';
?>
<footer class="footer">

    <div class="container">

        <div class="footer-grid">

            <div class="footer-brand">

                <a href="<?= BASE_URL ?>/index.php"
                   class="navbar-brand">

                    <span class="logo-mark">
                        <?= hl_icon('sparkles', 22) ?>
                    </span>

                    HandLearn
                </a>

                <p>
                    Piattaforma didattica italiana per imparare la
                    Lingua dei Segni Italiana con lezioni guidate,
                    giochi interattivi e AI per il riconoscimento
                    dei segni in tempo reale.
                </p>

            </div>

            <div class="footer-column">

                <h4>Impara</h4>

                <ul>

                    <li>
                        <a href="<?= BASE_URL ?>/impara.php">
                            Lezioni
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>/dizionario.php">
                            Dizionario
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>/esercitati.php">
                            Esercitati con AI
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>/gioca.php">
                            Giochi didattici
                        </a>
                    </li>

                </ul>

            </div>

            <div class="footer-column">

                <h4>Account</h4>

                <ul>

                    <?php if (isLoggedIn()): ?>

                        <li>
                            <a href="<?= BASE_URL ?>/profilo.php">
                                Il mio profilo
                            </a>
                        </li>

                        <li>
                            <a href="<?= BASE_URL ?>/logout.php">
                                Esci
                            </a>
                        </li>

                    <?php else: ?>

                        <li>
                            <a href="<?= BASE_URL ?>/login.php">
                                Accedi
                            </a>
                        </li>

                        <li>
                            <a href="<?= BASE_URL ?>/register.php">
                                Registrati
                            </a>
                        </li>

                    <?php endif; ?>

                </ul>

            </div>

            <div class="footer-column">

                <h4>Progetto</h4>

                <ul>

                    <li>
                        <a href="<?= BASE_URL ?>/about.php">
                            Chi siamo
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>/about.php#tecnologia">
                            Tecnologia
                        </a>
                    </li>

                    <li>
                        <a href="<?= BASE_URL ?>/about.php#contesto">
                            Contesto
                        </a>
                    </li>

                </ul>

            </div>

        </div>

        <div class="footer-bottom">
            &copy; <?= date('Y') ?>
            HandLearn — progetto didattico per l'inclusione.
        </div>

    </div>

</footer>

<script src="<?= BASE_URL ?>/js/main.js"></script>

</body>
</html>
