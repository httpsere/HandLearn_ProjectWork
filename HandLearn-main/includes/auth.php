<?php
/**
 * HandLearn - Helper per la sessione utente
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    return [
        'id'       => $_SESSION['user_id'],
        'username' => $_SESSION['username']  ?? '',
        'nome'     => $_SESSION['nome']      ?? '',
        'cognome'  => $_SESSION['cognome']   ?? '',
        'email'    => $_SESSION['email']     ?? '',
        'livello'  => $_SESSION['livello']   ?? 'principiante',
        'xp'       => $_SESSION['xp']        ?? 0,
    ];
}

function requireLogin(string $redirect = 'login.php'): void {
    if (!isLoggedIn()) {
        header("Location: $redirect");
        exit;
    }
}

function loginUser(array $row): void {
    $_SESSION['user_id']  = (int)$row['id'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['nome']     = $row['nome'];
    $_SESSION['cognome']  = $row['cognome'];
    $_SESSION['email']    = $row['email'];
    $_SESSION['livello']  = $row['livello'] ?? 'principiante';
    $_SESSION['xp']       = (int)($row['xp'] ?? 0);
    session_regenerate_id(true);
}

function logoutUser(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']);
    }
    session_destroy();
}
