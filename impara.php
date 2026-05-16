<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/components/sign_visual.php';

$categorie = $conn->query(
    'SELECT c.id, c.slug, c.nome,
            (SELECT COUNT(*) FROM lezioni l WHERE l.categoria_id = c.id) AS n
     FROM categorie c ORDER BY c.id'
)->fetch_all(MYSQLI_ASSOC);

$lezioni = $conn->query(
    'SELECT l.*, c.slug AS cat_slug, c.nome AS cat_nome
     FROM lezioni l
     LEFT JOIN categorie c ON c.id = l.categoria_id
     ORDER BY
       FIELD(l.livello, "principiante","intermedio","avanzato"),
       l.id'
)->fetch_all(MYSQLI_ASSOC);

$totaleLezioni = count($lezioni);

$pageTitle   = 'Impara';
$currentPage = 'impara';
include __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <?= hl_icon('arrow-right', 12) ?>
            <span>Impara</span>
        </nav>
        <h1>Lezioni di Lingua dei Segni</h1>
        <p>Scegli una lezione e inizia a imparare. Ogni lezione ha una serie di segni
           con descrizione e collegamento diretto all'esercitazione.</p>
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container">
        <div class="lessons-layout">

            <aside class="lessons-sidebar">
                <h4>Categorie</h4>
                <ul class="cat-list">
                    <li><a href="#" class="active" data-category="all">
                        Tutte <span class="count"><?= $totaleLezioni ?></span>
                    </a></li>
                    <?php foreach ($categorie as $c): ?>
                        <li><a href="#" data-category="<?= htmlspecialchars($c['slug']) ?>">
                            <?= htmlspecialchars($c['nome']) ?>
                            <span class="count"><?= (int)$c['n'] ?></span>
                        </a></li>
                    <?php endforeach; ?>
                </ul>

                <h4>Livello</h4>
                <ul class="cat-list">
                    <li><a href="#" data-category="principiante">
                        <span><span class="dot" style="color:#10b981; margin-right:6px;"></span>Principiante</span>
                    </a></li>
                    <li><a href="#" data-category="intermedio">
                        <span><span class="dot" style="color:#f59e0b; margin-right:6px;"></span>Intermedio</span>
                    </a></li>
                    <li><a href="#" data-category="avanzato">
                        <span><span class="dot" style="color:#ef4444; margin-right:6px;"></span>Avanzato</span>
                    </a></li>
                </ul>

                <a href="esercitati.php" class="btn btn-primary btn-block mt-6">
                    <?= hl_icon('camera', 18) ?> Esercitati ora
                </a>
            </aside>

            <div class="lessons-list flex-col" style="gap:var(--s-4);">
                <?php foreach ($lezioni as $l):
                    $color = ['principiante' => 'emerald', 'intermedio' => 'amber', 'avanzato' => 'rose'][$l['livello']] ?? 'violet';
                    $badge = ['principiante' => 'badge-success', 'intermedio' => 'badge-accent', 'avanzato' => 'badge-danger'][$l['livello']] ?? 'badge-primary';
                ?>
                    <a href="lezione.php?id=<?= (int)$l['id'] ?>"
                       class="lesson-card"
                       data-lesson-category="<?= htmlspecialchars($l['cat_slug'] ?? '') ?>"
                       data-lesson-level="<?= htmlspecialchars($l['livello']) ?>">

                        <?= render_sign_visual($l['titolo'], ['color' => $color, 'label' => false]) ?>

                        <div>
                            <h3><?= htmlspecialchars($l['titolo']) ?></h3>
                            <p><?= htmlspecialchars($l['descrizione']) ?></p>
                            <div class="meta">
                                <span class="badge <?= $badge ?>">
                                    <?= htmlspecialchars(ucfirst($l['livello'])) ?>
                                </span>
                                <?php if (!empty($l['cat_nome'])): ?>
                                    <span class="badge"><?= htmlspecialchars($l['cat_nome']) ?></span>
                                <?php endif; ?>
                                <span class="badge">⏱ <?= (int)$l['durata_min'] ?> min</span>
                            </div>
                        </div>

                        <span class="btn btn-secondary btn-sm hide-mobile">
                            Apri <?= hl_icon('arrow-right', 14) ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div> 

        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
