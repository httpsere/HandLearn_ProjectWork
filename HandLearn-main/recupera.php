<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/components/sign_visual.php';

$errore = '';
$successo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errore = 'Inserisci una email valida.';
    } else {
        $stmt = $conn->prepare('SELECT id FROM utenti WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if ($row) {
            $token = bin2hex(random_bytes(32));
            $u = $conn->prepare('UPDATE utenti SET token_reset = ? WHERE id = ?');
            $u->bind_param('si', $token, $row['id']);
            $u->execute();

            $link = sprintf('%s://%s%s/reset_password.php?token=%s',
                isset($_SERVER['HTTPS']) ? 'https' : 'http',
                $_SERVER['HTTP_HOST'],
                rtrim(dirname($_SERVER['PHP_SELF']), '/\\'),
                $token);

            $successo = 'Link di reset generato (in produzione verrebbe inviato via mail):'
                      . '<br><a href="' . htmlspecialchars($link) . '">'
                      . htmlspecialchars($link) . '</a>';
        } else {
            $errore = 'Nessun account trovato con questa email.';
        }
    }
}

$pageTitle   = 'Recupera password';
$currentPage = 'login';
include __DIR__ . '/includes/header.php';
?>
<main class="auth-page">
    <aside class="auth-side">
        <h2>Hai dimenticato la password?</h2>
        <p>Nessun problema: ti generiamo un link sicuro per reimpostarla.</p>
        <ul>
            <li><?= hl_icon('shield', 22) ?> Token unico e monouso</li>
            <li><?= hl_icon('check-circle', 22) ?> Riprendi subito da dove eri</li>
        </ul>
    </aside>
    <section class="auth-form-wrap">
        <div class="auth-card card card-pad-lg">
            <h1>Recupera password</h1>
            <p class="subtitle">Inserisci la tua email per ricevere il link</p>

            <?php if ($errore): ?>
                <div class="alert alert-error">
                    <?= hl_icon('x', 22, 'alert-icon') ?>
                    <span><?= htmlspecialchars($errore) ?></span>
                </div>
            <?php endif; ?>
            <?php if ($successo): ?>
                <div class="alert alert-success">
                    <?= hl_icon('check', 22, 'alert-icon') ?>
                    <span><?= $successo ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-icon">
                        <?= hl_icon('mail', 18) ?>
                        <input type="email" id="email" name="email" class="form-control"
                               placeholder="nome@esempio.com" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    Invia link di reset
                </button>
            </form>

            <p class="auth-foot">
                <a href="login.php">← Torna al login</a>
            </p>
        </div>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
