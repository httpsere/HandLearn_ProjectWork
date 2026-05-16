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
    $nome     = trim($_POST['nome']     ?? '');
    $cognome  = trim($_POST['cognome']  ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password'] ?? '';

    if ($nome === '' || $cognome === '' || $username === '' || $email === '' || $password === '') {
        $errore = 'Compila tutti i campi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errore = 'Email non valida.';
    } elseif (strlen($username) < 3) {
        $errore = 'Lo username deve avere almeno 3 caratteri.';
    } elseif (!preg_match('/^(?=.*[0-9])(?=.*[\W]).{8,}$/', $password)) {
        $errore = 'La password deve avere almeno 8 caratteri, un numero e un carattere speciale.';
    } else {
        $check = $conn->prepare('SELECT id FROM utenti WHERE username = ? OR email = ? LIMIT 1');
        $check->bind_param('ss', $username, $email);
        $check->execute();
        if ($check->get_result()->fetch_assoc()) {
            $errore = 'Username o email già esistenti. Prova ad accedere.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare(
                'INSERT INTO utenti (nome, cognome, username, email, password_hash)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->bind_param('sssss', $nome, $cognome, $username, $email, $hash);

            if ($stmt->execute()) {
                $newId = $stmt->insert_id;
                $row = [
                    'id'       => $newId,
                    'nome'     => $nome,
                    'cognome'  => $cognome,
                    'username' => $username,
                    'email'    => $email,
                    'livello'  => 'principiante',
                    'xp'       => 0,
                ];
                loginUser($row);
                header('Location: index.php?msg=success&text=' .
                       urlencode('Benvenuto/a in HandLearn, ' . $nome . '! Inizia ora.'));
                exit;
            }
            $errore = 'Errore durante la registrazione, riprova.';
        }
    }
}

$pageTitle   = 'Registrati';
$currentPage = 'register';
include __DIR__ . '/includes/header.php';
?>
<main class="auth-page">

    <aside class="auth-side">
        <h2>Unisciti a HandLearn 🎉</h2>
        <p>Crea un account gratuito e inizia subito il tuo percorso nella Lingua dei Segni Italiana.</p>
        <ul>
            <li><?= hl_icon('check-circle', 22) ?> Lezioni passo passo</li>
            <li><?= hl_icon('check-circle', 22) ?> Esercizi con webcam e AI</li>
            <li><?= hl_icon('check-circle', 22) ?> Quiz, memory, sfide a tempo</li>
            <li><?= hl_icon('check-circle', 22) ?> Dizionario di segni completo</li>
            <li><?= hl_icon('check-circle', 22) ?> Tracciamento dei tuoi progressi</li>
        </ul>
    </aside>

    <section class="auth-form-wrap">
        <div class="auth-card card card-pad-lg">
            <h1>Crea il tuo account</h1>
            <p class="subtitle">È gratis e ci vuole meno di un minuto</p>

            <?php if ($errore): ?>
                <div class="alert alert-error">
                    <?= hl_icon('x', 22, 'alert-icon') ?>
                    <span><?= htmlspecialchars($errore) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" autocomplete="on" novalidate>
                <div class="grid grid-2" style="gap: var(--s-3);">
                    <div class="form-group">
                        <label for="nome">Nome</label>
                        <input type="text" id="nome" name="nome" class="form-control"
                               value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                               placeholder="Mario" required>
                    </div>
                    <div class="form-group">
                        <label for="cognome">Cognome</label>
                        <input type="text" id="cognome" name="cognome" class="form-control"
                               value="<?= htmlspecialchars($_POST['cognome'] ?? '') ?>"
                               placeholder="Rossi" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-icon">
                        <?= hl_icon('user', 18) ?>
                        <input type="text" id="username" name="username" class="form-control"
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                               placeholder="mariorossi" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-icon">
                        <?= hl_icon('mail', 18) ?>
                        <input type="email" id="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               placeholder="nome@esempio.com" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-icon">
                        <?= hl_icon('lock', 18) ?>
                        <input type="password" id="password" name="password" class="form-control"
                               placeholder="••••••••" required>
                    </div>
                    <p class="form-help">Minimo 8 caratteri, almeno un numero e un carattere speciale.</p>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    Crea account e inizia
                    <?= hl_icon('arrow-right', 18) ?>
                </button>
            </form>

            <p class="auth-foot">
                Hai già un account? <a href="login.php">Accedi</a>
            </p>
        </div>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
