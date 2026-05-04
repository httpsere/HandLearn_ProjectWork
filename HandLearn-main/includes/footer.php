<footer class="footer">
    <div class="container">
        <div class="footer-grid">

            <div class="footer-brand">
                <a href="index.php" class="navbar-brand">
                    <span class="logo-mark">
                        <?= hl_icon('sparkles', 22) ?>
                    </span>
                    HandLearn
                </a>
                <p>
                    Piattaforma didattica italiana per imparare la Lingua dei Segni
                    Italiana con lezioni guidate, giochi interattivi e AI per il
                    riconoscimento dei segni in tempo reale.
                </p>
            </div>

            <div class="footer-column">
                <h4>Impara</h4>
                <ul>
                    <li><a href="impara.php">Lezioni</a></li>
                    <li><a href="dizionario.php">Dizionario</a></li>
                    <li><a href="esercitati.php">Esercitati con AI</a></li>
                    <li><a href="gioca.php">Giochi didattici</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Account</h4>
                <ul>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="profilo.php">Il mio profilo</a></li>
                        <li><a href="logout.php">Esci</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Accedi</a></li>
                        <li><a href="register.php">Registrati</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Progetto</h4>
                <ul>
                    <li><a href="about.php">Chi siamo</a></li>
                    <li><a href="about.php#tecnologia">Tecnologia</a></li>
                    <li><a href="about.php#contesto">Contesto</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            &copy; <?= date('Y') ?> HandLearn — progetto didattico per l'inclusione.
        </div>
    </div>
</footer>

<script src="js/main.js"></script>
</body>
</html>
