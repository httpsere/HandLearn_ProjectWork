<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/components/sign_visual.php';

$errore = '';
$successo = '';
$token = $_GET['token'] ?? $_POST['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if (!preg_match('/^(?=.*[0-9])(?=.*[\W]).{8,}$/', $password)) {
        $errore = 'La password deve avere almeno 8 caratteri, un numero e un carattere speciale.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare(
            'UPDATE utenti SET password_hash = ?, token_reset = NULL WHERE token_reset = ?'
        );
        $stmt->bind_param('ss', $hash, $token);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $successo = 'Password aggiornata con successo. Puoi <a href="login.php">accedere</a>.';
        } else {
            $errore = 'Token non valido o già utilizzato.';
        }
    }
}

$pageTitle   = 'Nuova password';
$currentPage = 'login';
include __DIR__ . '/includes/header.php';
?>
<main class="auth-page">
    <aside class="auth-side">
        <h2>Quasi fatto 🔒</h2>
        <p>Imposta una nuova password sicura e torna a imparare la LIS.</p>
    </aside>
    <section class="auth-form-wrap">
        <div class="auth-card card card-pad-lg">
            <h1>Nuova password</h1>
            <p class="subtitle">Scegli una password sicura</p>

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

            <?php if (!$successo): ?>
            <form method="POST" autocomplete="off">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <div class="form-group">
                    <label for="password">Nuova password</label>
                    <div class="input-icon">
                        <?= hl_icon('lock', 18) ?>
                        <input type="password" id="password" name="password" class="form-control"
                               placeholder="••••••••" required>
                    </div>
                    <p class="form-help">Minimo 8 caratteri, almeno un numero e un carattere speciale.</p>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg">Salva nuova password</button>
            </form>
            <?php endif; ?>

            <p class="auth-foot">
                <a href="login.php">← Torna al login</a>
            </p>
        </div>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
