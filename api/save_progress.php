<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$user = currentUser();
$type = $data['type'] ?? '';

if ($type === 'lesson') {
    $lessonId    = (int)($data['lesson_id']   ?? 0);
    $percentuale = max(0, min(100, (int)($data['percentuale'] ?? 0)));
    $completata  = $percentuale >= 100 ? 1 : 0;

    $stmt = $conn->prepare(
        'INSERT INTO progressi (utente_id, lezione_id, percentuale, completata)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE percentuale = VALUES(percentuale),
                                 completata  = VALUES(completata)'
    );
    $stmt->bind_param('iiii', $user['id'], $lessonId, $percentuale, $completata);
    $stmt->execute();
    echo json_encode(['ok' => true]);
    exit;
}

if ($type === 'score') {
    $gioco     = trim($data['gioco']     ?? 'sconosciuto');
    $punteggio = (int)($data['punteggio'] ?? 0);

    $stmt = $conn->prepare(
        'INSERT INTO punteggi (utente_id, gioco, punteggio) VALUES (?, ?, ?)'
    );
    $stmt->bind_param('isi', $user['id'], $gioco, $punteggio);
    $stmt->execute();

    $u = $conn->prepare('UPDATE utenti SET xp = xp + ? WHERE id = ?');
    $delta = max(0, (int)floor($punteggio / 10));
    $u->bind_param('ii', $delta, $user['id']);
    $u->execute();

    echo json_encode(['ok' => true, 'xp_delta' => $delta]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid type']);
