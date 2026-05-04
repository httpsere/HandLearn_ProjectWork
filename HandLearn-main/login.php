<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/components/sign_visual.php';

if (isLoggedIn()) {
    header('Location: profilo.php');
    exit;
}

$errore = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identita = trim($_POST['identita'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($identita === '' || $password === '') {
        $errore = 'Compila tutti i campi.';
    } else {
        $stmt = $conn->prepare(
            'SELECT id, nome, cognome, username, email, password_hash, livello, xp
             FROM utenti
             WHERE username = ? OR email = ?
             LIMIT 1'
        );
        $stmt->bind_param('ss', $identita, $identita);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if ($row && password_verify($password, $row['password_hash'])) {
            loginUser($row);
            header('Location: index.php?msg=success&text=' . urlencode('Bentornato, ' . $row['nome'] . '!'));
            exit;
        }
        $errore = 'Credenziali non valide. Verifica username/email e password.';
    }
}

$pageTitle   = 'Accedi';
$currentPage = 'login';
include __DIR__ . '/includes/header.php';
?>
<main class="auth-page">

    <aside class="auth-side">
        <h2>Bentornato 👋</h2>
        <p>Accedi per continuare il tuo percorso nella Lingua dei Segni Italiana.</p>
        <ul>
            <li><?= hl_icon('check-circle', 22) ?> Riprendi da dove avevi lasciato</li>
            <li><?= hl_icon('check-circle', 22) ?> Salva XP e progressi</li>
            <li><?= hl_icon('check-circle', 22) ?> Riconoscimento gesti AI in tempo reale</li>
            <li><?= hl_icon('check-circle', 22) ?> Sfida i tuoi record personali</li>
        </ul>
    </aside>

    <section class="auth-form-wrap">
        <div class="auth-card card card-pad-lg">
            <h1>Accedi al tuo account</h1>
            <p class="subtitle">Inserisci le credenziali per continuare</p>

            <?php if ($errore): ?>
                <div class="alert alert-error">
                    <?= hl_icon('x', 22, 'alert-icon') ?>
                    <span><?= htmlspecialchars($errore) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" autocomplete="on" novalidate>
                <div class="form-group">
                    <label for="identita">Username o email</label>
                    <div class="input-icon">
                        <?= hl_icon('user', 18) ?>
                        <input type="text" id="identita" name="identita" class="form-control"
                               value="<?= htmlspecialchars($_POST['identita'] ?? '') ?>"
                               placeholder="es: mariorossi" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-icon">
                        <?= hl_icon('lock', 18) ?>
                        <input type="password" id="password" name="password" class="form-control"
                               placeholder="••••••••" required>
                    </div>
                </div>

                <div class="flex-between mb-5">
                    <span></span>
                    <a href="recupera.php" class="text-sm">Password dimenticata?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    Accedi
                    <?= hl_icon('arrow-right', 18) ?>
                </button>
            </form>

            <p class="auth-foot">
                Non hai un account? <a href="register.php">Registrati gratis</a>
            </p>
        </div>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
