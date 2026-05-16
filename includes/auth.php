<?php
/**
 * HandLearn - Helper sessione utente
 */
require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| BASE URL
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Auth helpers
|--------------------------------------------------------------------------
*/

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function currentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id'       => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? '',
        'nome'     => $_SESSION['nome'] ?? '',
        'cognome'  => $_SESSION['cognome'] ?? '',
        'email'    => $_SESSION['email'] ?? '',
        'livello'  => $_SESSION['livello'] ?? 'principiante',
        'xp'       => $_SESSION['xp'] ?? 0,
    ];
}

/*
|--------------------------------------------------------------------------
| Redirect login required
|--------------------------------------------------------------------------
*/

function requireLogin(string $redirect = null): void
{
    if (!isLoggedIn()) {

        if ($redirect === null) {
            $redirect = BASE_URL . '/login.php';
        }

        header("Location: {$redirect}");
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/

function loginUser(array $row): void
{
    session_regenerate_id(true);

    $_SESSION['user_id']  = (int)($row['id'] ?? 0);
    $_SESSION['username'] = $row['username'] ?? '';
    $_SESSION['nome']     = $row['nome'] ?? '';
    $_SESSION['cognome']  = $row['cognome'] ?? '';
    $_SESSION['email']    = $row['email'] ?? '';
    $_SESSION['livello']  = $row['livello'] ?? 'principiante';
    $_SESSION['xp']       = (int)($row['xp'] ?? 0);
}

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

function logoutUser(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}
