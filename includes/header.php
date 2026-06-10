<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/components/sign_visual.php';


if (!isset($pageTitle))   $pageTitle   = 'HandLearn';
if (!isset($currentPage)) $currentPage = '';

$flashType = $_GET['msg']  ?? '';
$flashText = $_GET['text'] ?? '';
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#4f46e5">

<title><?= htmlspecialchars($pageTitle) ?> · HandLearn</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/assets/segni_immagini/LOGO.png">

<link rel="stylesheet" href="http://localhost/HandLearn-main/css/style.css">
</head>

<body>

<?php
require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/navbar.php'; ?>

<?php if ($flashType && $flashText): ?>
<div class="toast-stack" id="flashStack">
    <div class="toast <?= htmlspecialchars($flashType) ?>">
        <?= htmlspecialchars($flashText) ?>
    </div>
</div>

<script>
setTimeout(() => {
    const f = document.getElementById('flashStack');
    if (f) {
        f.style.opacity = 0;
        setTimeout(() => f.remove(), 400);
    }
}, 3500);
</script>
<?php endif; ?>