<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/components/sign_visual.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: dizionario.php'); exit; }

$stmt = $conn->prepare(
    'SELECT s.*, c.nome AS categoria, c.slug AS cat_slug
     FROM segni s LEFT JOIN categorie c ON c.id = s.categoria_id
     WHERE s.id = ? LIMIT 1'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$segno = $stmt->get_result()->fetch_assoc();

if (!$segno) { header('Location: dizionario.php'); exit; }

// Lezioni che includono questo segno
$stmt2 = $conn->prepare(
    'SELECT l.id, l.titolo, l.descrizione, l.livello
     FROM lezioni l
     JOIN lezioni_segni ls ON ls.lezione_id = l.id
     WHERE ls.segno_id = ?'
);
$stmt2->bind_param('i', $id);
$stmt2->execute();
$lezioniContenenti = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

// Segni correlati (stessa categoria)
$correlati = [];
if (!empty($segno['categoria_id'])) {
    $stmt3 = $conn->prepare(
        'SELECT id, parola, tipo FROM segni WHERE categoria_id = ? AND id <> ? LIMIT 6'
    );
    $stmt3->bind_param('ii', $segno['categoria_id'], $id);
    $stmt3->execute();
    $correlati = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
}

$color = hl_pick_sign_color($segno['parola']);
$pageTitle   = $segno['parola'];
$currentPage = 'dizionario';
include __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <?= hl_icon('arrow-right', 12) ?>
            <a href="dizionario.php">Dizionario</a>
            <?= hl_icon('arrow-right', 12) ?>
            <span><?= htmlspecialchars($segno['parola']) ?></span>
        </nav>
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container">

        <div class="sign-detail">
            <?= render_sign_visual($segno['parola'], ['color' => $color, 'size' => 'lg', 'label' => false]) ?>

            <div>
                <div class="flex gap-2 mb-3">
                    <span class="badge badge-primary"><?= htmlspecialchars($segno['tipo']) ?></span>
                    <?php if (!empty($segno['categoria'])): ?>
                        <a href="dizionario.php?cat=<?= urlencode($segno['cat_slug']) ?>"
                           class="badge"><?= htmlspecialchars($segno['categoria']) ?></a>
                    <?php endif; ?>
                    <span class="badge badge-success"><?= htmlspecialchars(ucfirst($segno['livello'])) ?></span>
                </div>

                <h1><?= htmlspecialchars($segno['parola']) ?></h1>

                <?php if (!empty($segno['descrizione'])): ?>
                    <p class="lead muted mt-4"><?= htmlspecialchars($segno['descrizione']) ?></p>
                <?php endif; ?>

                <div class="card card-pad-lg mt-5" style="background: var(--surface-2); border:none;">
                    <h3 class="mb-3">Come si esegue</h3>
                    <ol style="padding-left: 1.2rem; color: var(--text-2); line-height: 1.9;">
                        <li>Posizionati davanti alla webcam con buona illuminazione.</li>
                        <li>Tieni la mano dominante visibile, palmo orientato secondo la descrizione.</li>
                        <li>Fai il segno tenendolo fermo per circa un secondo.</li>
                        <li>Il sistema ti darà un feedback visivo sulla correttezza.</li>
                    </ol>
                </div>

                <div class="flex mt-6 flex-wrap">
                    <a href="esercitati.php?segno=<?= urlencode($segno['parola']) ?>"
                       class="btn btn-primary btn-lg">
                        <?= hl_icon('camera', 18) ?>
                        Esercitati con questo segno
                    </a>
                    <a href="dizionario.php" class="btn btn-secondary btn-lg">
                        <?= hl_icon('arrow-left', 16) ?>
                        Torna al dizionario
                    </a>
                </div>
            </div>
        </div>

        <?php if ($lezioniContenenti): ?>
            <h2 class="mt-8 mb-5">Lezioni che includono questo segno</h2>
            <div class="grid grid-2">
                <?php foreach ($lezioniContenenti as $l): ?>
                    <a href="lezione.php?id=<?= (int)$l['id'] ?>" class="lesson-card">
                        <?= render_sign_visual($l['titolo'], ['color' => 'violet', 'label' => false]) ?>
                        <div>
                            <h3><?= htmlspecialchars($l['titolo']) ?></h3>
                            <p><?= htmlspecialchars($l['descrizione']) ?></p>
                            <div class="meta">
                                <span class="badge"><?= htmlspecialchars(ucfirst($l['livello'])) ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($correlati): ?>
            <h2 class="mt-8 mb-5">Segni correlati</h2>
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));">
                <?php foreach ($correlati as $r): ?>
                    <a href="segno.php?id=<?= (int)$r['id'] ?>" class="word-card">
                        <?= render_sign_visual($r['parola'], ['color' => hl_pick_sign_color($r['parola']), 'label' => false]) ?>
                        <div class="word-card-body">
                            <h3><?= htmlspecialchars($r['parola']) ?></h3>
                            <p><?= htmlspecialchars($r['tipo']) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
