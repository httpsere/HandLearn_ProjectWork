<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/components/sign_visual.php';

$q       = trim($_GET['q']    ?? '');
$catSlug = trim($_GET['cat']  ?? '');

$where  = [];
$params = [];
$types  = '';

if ($q !== '') {
    $where[]  = '(s.parola LIKE ? OR s.descrizione LIKE ?)';
    $like     = '%' . $q . '%';
    $params[] = $like; $params[] = $like; $types .= 'ss';
}
if ($catSlug !== '') {
    $where[]  = 'c.slug = ?';
    $params[] = $catSlug; $types .= 's';
}
$sql = 'SELECT s1.*, c.nome AS categoria, c.slug AS cat_slug
        FROM segni s1
        INNER JOIN (
            SELECT MIN(id) as id
            FROM segni
            GROUP BY LOWER(parola)
        ) s2 ON s1.id = s2.id
        LEFT JOIN categorie c ON c.id = s1.categoria_id';

if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY s1.parola';

if ($params) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $segni = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $segni = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

$lettere = [];
foreach ($segni as $s) {
    $lettere[strtoupper($s['parola'][0] ?? '')] = true;
}
ksort($lettere);

$categorie = $conn->query('SELECT id, slug, nome FROM categorie ORDER BY id')->fetch_all(MYSQLI_ASSOC);

$pageTitle   = 'Dizionario';
$currentPage = 'dizionario';
include __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <?= hl_icon('arrow-right', 12) ?>
            <span>Dizionario</span>
        </nav>
        <h1>Dizionario LIS</h1>
        <p>Cerca tra i segni per parola, lettera o categoria. Clicca su una scheda
           per il dettaglio e l'opportunità di esercitarti con quel segno.</p>
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container">

        <form method="GET" class="dict-search">
            <?= hl_icon('search', 22) ?>
            <input type="text" name="q" placeholder="Cerca un segno (es: Ciao, A, Mamma...)"
                   value="<?= htmlspecialchars($q) ?>"
                   data-dictionary-search autocomplete="off">
        </form>

        <!-- Filtri categoria -->
        <div class="flex flex-wrap mb-6">
            <a href="dizionario.php" class="badge <?= $catSlug==='' ? 'badge-primary' : '' ?>">Tutte</a>
            <?php foreach ($categorie as $c): ?>
                <a href="dizionario.php?cat=<?= urlencode($c['slug']) ?>"
                   class="badge <?= $catSlug===$c['slug'] ? 'badge-primary' : '' ?>">
                    <?= htmlspecialchars($c['nome']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Filtro alfabetico -->
        <div class="alphabet-nav">
            <a href="#" class="active" data-letter="all">Tutte</a>
            <?php foreach ($lettere as $L => $_): ?>
                <a href="#" data-letter="<?= htmlspecialchars($L) ?>"><?= htmlspecialchars($L) ?></a>
            <?php endforeach; ?>
        </div>

        <?php if (!$segni): ?>
            <div class="alert alert-info">
                <?= hl_icon('search', 22, 'alert-icon') ?>
                <span>Nessun segno trovato per "<?= htmlspecialchars($q) ?>". Prova un'altra parola o
                rimuovi i filtri.</span>
            </div>
        <?php else: ?>
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
                <?php foreach ($segni as $s):
                    $color = hl_pick_sign_color($s['parola']);
                ?>
                    <a href="segno.php?id=<?= (int)$s['id'] ?>"
                       class="word-card"
                       data-word="<?= htmlspecialchars($s['parola']) ?>">
                        <?= render_sign_visual($s['parola'], ['color' => $color, 'label' => false]) ?>
                        <div class="word-card-body">
                            <h3><?= htmlspecialchars($s['parola']) ?></h3>
                            <p><?= htmlspecialchars($s['tipo']) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
