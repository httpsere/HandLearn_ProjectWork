<?php
/**
 * HandLearn - Configurazione database
 *
 * Modifica qui le credenziali per adattare il progetto al tuo server MySQL.
 * Default: configurazione standard di XAMPP / MAMP / Laragon (root senza password).
 */
if (!defined('BASE_URL')) {
    define('BASE_URL', '/HandLearn-main');
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'handlearn');
define('DB_CHARSET', 'utf8mb4');

define('NODE_SERVER_URL', 'http://localhost:3000');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset(DB_CHARSET);
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die("<h1>Errore di connessione al database</h1>
         <p>Verifica che MySQL sia avviato e che il database <code>" . DB_NAME . "</code>
         sia stato creato eseguendo <code>sql/handlearn.sql</code>.</p>
         <p><small>Dettagli: " . htmlspecialchars($e->getMessage()) . "</small></p>");
}
