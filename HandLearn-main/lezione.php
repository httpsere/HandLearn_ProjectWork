<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/components/sign_visual.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: impara.php'); exit;
}

$stmt = $conn->prepare(
    'SELECT l.*, c.nome AS cat_nome, c.slug AS cat_slug
     FROM lezioni l LEFT JOIN categorie c ON c.id = l.categoria_id
     WHERE l.id = ? LIMIT 1'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$lezione = $stmt->get_result()->fetch_assoc();

if (!$lezione) {
    header('Location: impara.php'); exit;
}

// Segni associati alla lezione (se la migrazione v2 è eseguita)
$stmt2 = $conn->prepare(
    'SELECT s.*, ls.ordine
     FROM lezioni_segni ls
     JOIN segni s ON s.id = ls.segno_id
     WHERE ls.lezione_id = ?
     ORDER BY ls.ordine, s.parola'
);
$stmt2->bind_param('i', $id);
$stmt2->execute();
$segni = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

// Fallback: se non ci sono segni associati, prendi quelli della stessa categoria
if (!$segni && !empty($lezione['categoria_id'])) {
    $stmt3 = $conn->prepare(
        'SELECT * FROM segni WHERE categoria_id = ? ORDER BY parola LIMIT 12'
    );
    $stmt3->bind_param('i', $lezione['categoria_id']);
    $stmt3->execute();
    $segni = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Lezioni successive (stessa categoria)
$lezioniAltre = [];
if (!empty($lezione['categoria_id'])) {
    $stmt4 = $conn->prepare(
        'SELECT id, titolo, descrizione, livello, durata_min FROM lezioni
         WHERE categoria_id = ? AND id <> ? ORDER BY id LIMIT 4'
    );
    $stmt4->bind_param('ii', $lezione['categoria_id'], $id);
    $stmt4->execute();
    $lezioniAltre = $stmt4->get_result()->fetch_all(MYSQLI_ASSOC);
}

$pageTitle   = $lezione['titolo'];
$currentPage = 'impara';
$colorMap    = ['principiante' => 'emerald', 'intermedio' => 'amber', 'avanzato' => 'rose'];
$badgeMap    = ['principiante' => 'badge-success', 'intermedio' => 'badge-accent', 'avanzato' => 'badge-danger'];
$color       = $colorMap[$lezione['livello']] ?? 'violet';

include __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <?= hl_icon('arrow-right', 12) ?>
            <a href="impara.php">Impara</a>
            <?= hl_icon('arrow-right', 12) ?>
            <span><?= htmlspecialchars($lezione['titolo']) ?></span>
        </nav>
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container">

        <!-- Hero della lezione -->
        <div class="lesson-hero">
            <?= render_sign_visual($lezione['titolo'], ['color' => $color, 'size' => 'lg', 'label' => false]) ?>
            <div>
                <div class="flex gap-2 mb-3">
                    <span class="badge <?= $badgeMap[$lezione['livello']] ?? 'badge-primary' ?>">
                        <?= htmlspecialchars(ucfirst($lezione['livello'])) ?>
                    </span>
                    <?php if (!empty($lezione['cat_nome'])): ?>
                        <span class="badge"><?= htmlspecialchars($lezione['cat_nome']) ?></span>
                    <?php endif; ?>
                    <span class="badge">⏱ <?= (int)$lezione['durata_min'] ?> min</span>
                </div>
                <h1><?= htmlspecialchars($lezione['titolo']) ?></h1>
                <p class="muted mt-3"><?= htmlspecialchars($lezione['descrizione']) ?></p>

                <div class="flex mt-5 flex-wrap">
                    <?php if ($segni): ?>
                        <a href="esercitati.php?lezione=<?= (int)$lezione['id'] ?>" class="btn btn-primary btn-lg">
                            <?= hl_icon('camera', 18) ?>
                            Esercitati con questa lezione
                        </a>
                    <?php endif; ?>
                    <a href="impara.php" class="btn btn-secondary btn-lg">
                        <?= hl_icon('arrow-left', 16) ?>
                        Torna alle lezioni
                    </a>
                </div>
            </div>
        </div>

        <!-- Contenuto: i segni della lezione -->
        <?php if ($segni): ?>
            <h2 class="mb-5">I segni di questa lezione</h2>
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                <?php foreach ($segni as $s):
                    $sc = hl_pick_sign_color($s['parola']);
                ?>
                    <div class="card hoverable" style="overflow:hidden;">
                        <a href="segno.php?id=<?= (int)$s['id'] ?>" style="display:block;">
                            <?= render_sign_visual($s['parola'], ['color' => $sc, 'label' => false]) ?>
                        </a>
                        <div class="card-body">
                            <h3><?= htmlspecialchars($s['parola']) ?></h3>
                            <p class="text-sm muted mb-3"><?= htmlspecialchars($s['tipo']) ?></p>
                            <?php if (!empty($s['descrizione'])): ?>
                                <p class="text-sm"><?= htmlspecialchars($s['descrizione']) ?></p>
                            <?php endif; ?>
                            <div class="flex gap-2 mt-4">
                                <a href="segno.php?id=<?= (int)$s['id'] ?>" class="btn btn-secondary btn-sm">
                                    Dettaglio
                                </a>
                                <a href="esercitati.php?segno=<?= urlencode($s['parola']) ?>"
                                   class="btn btn-primary btn-sm">
                                    <?= hl_icon('camera', 14) ?>
                                    Esercitati
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <?= hl_icon('sparkles', 22, 'alert-icon') ?>
                <span>I contenuti dettagliati di questa lezione saranno disponibili a breve. Nel frattempo,
                      puoi esplorare il <a href="dizionario.php">dizionario</a> o
                      <a href="esercitati.php">esercitarti con la webcam</a>.</span>
            </div>
        <?php endif; ?>

        <!-- Lezioni correlate -->
        <?php if ($lezioniAltre): ?>
            <h2 class="mt-8 mb-5">Continua il percorso</h2>
            <div class="grid grid-2">
                <?php foreach ($lezioniAltre as $alt): ?>
                    <a href="lezione.php?id=<?= (int)$alt['id'] ?>" class="lesson-card">
                        <?= render_sign_visual($alt['titolo'], ['color' => 'violet', 'label' => false]) ?>
                        <div>
                            <h3><?= htmlspecialchars($alt['titolo']) ?></h3>
                            <p><?= htmlspecialchars($alt['descrizione']) ?></p>
                            <div class="meta">
                                <span class="badge"><?= htmlspecialchars(ucfirst($alt['livello'])) ?></span>
                                <span class="badge">⏱ <?= (int)$alt['durata_min'] ?> min</span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
