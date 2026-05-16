<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/components/sign_visual.php';

requireLogin();
$user = currentUser();

$stmt = $conn->prepare(
    'SELECT p.*, l.titolo, l.icona, l.livello
     FROM progressi p
     LEFT JOIN lezioni l ON l.id = p.lezione_id
     WHERE p.utente_id = ?
     ORDER BY p.updated_at DESC'
);
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$progressi = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt2 = $conn->prepare(
    'SELECT gioco, MAX(punteggio) AS max_score, COUNT(*) AS partite
     FROM punteggi WHERE utente_id = ? GROUP BY gioco'
);
$stmt2->bind_param('i', $user['id']);
$stmt2->execute();
$puntiPerGioco = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

$totLezioni = (int)($conn->query('SELECT COUNT(*) AS n FROM lezioni')->fetch_assoc()['n'] ?? 0);
$lezioniCompletate = [];

foreach ($progressi as $p) {
    if ((int)$p['percentuale'] >= 100) {
        $lezioniCompletate[$p['lezione_id']] = true;
    }
}

$completate = count($lezioniCompletate);

$percGenerale = $totLezioni ? round(($completate / $totLezioni) * 100) : 0;

$iniziali = strtoupper(substr($user['nome'], 0, 1) . substr($user['cognome'], 0, 1));

$pageTitle   = 'Il mio profilo';
$currentPage = 'profilo';
include __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <?= hl_icon('arrow-right', 12) ?>
            <span>Il mio profilo</span>
        </nav>
        <h1>Ciao, <?= htmlspecialchars($user['nome']) ?> 👋</h1>
        <p>Ecco i tuoi progressi, le statistiche e la tua attività recente.</p>
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container">
        <div class="profile-layout">

            <aside class="profile-side">
                <div class="profile-avatar"><?= htmlspecialchars($iniziali) ?></div>
                <h2><?= htmlspecialchars($user['nome'] . ' ' . $user['cognome']) ?></h2>
                <div class="email">@<?= htmlspecialchars($user['username']) ?> · <?= htmlspecialchars($user['email']) ?></div>

                <span class="badge badge-primary mt-2">
                    Livello <?= htmlspecialchars($user['livello']) ?>
                </span>

                <div class="profile-stats-mini">
                    <div class="stat-mini"><strong><?= (int)$user['xp'] ?></strong><span>XP</span></div>
                    <div class="stat-mini"><strong><?= $completate ?></strong><span>Lezioni</span></div>
                    <div class="stat-mini"><strong><?= count($puntiPerGioco) ?></strong><span>Giochi</span></div>
                </div>

                <a href="esercitati.php" class="btn btn-primary btn-block mt-4">
                    <?= hl_icon('camera', 18) ?> Esercitati ora
                </a>
                <a href="logout.php" class="btn btn-secondary btn-block mt-2">
                    <?= hl_icon('logout', 16) ?> Esci
                </a>
            </aside>

            <div>
                <!-- Panoramica -->
                <div class="profile-section">
                    <h3><?= hl_icon('sparkles', 22) ?> Panoramica</h3>
                    <div class="flex-between mb-3">
                        <span class="muted">Lezioni completate</span>
                        <strong><?= $completate ?> / <?= $totLezioni ?></strong>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-bar-fill" style="width: <?= $percGenerale ?>%"></div>
                    </div>
                    <p class="text-sm muted mt-2"><?= $percGenerale ?>% del percorso completato</p>
                </div>

                <!-- Punteggi -->
                <div class="profile-section">
                    <h3><?= hl_icon('trophy', 22) ?> Punteggi giochi</h3>
                    <?php if (!$puntiPerGioco): ?>
                        <p class="muted">Non hai ancora giocato. Vai alla sezione <a href="gioca.php">Gioca</a>!</p>
                    <?php else: ?>
                        <div class="grid grid-2">
                            <?php foreach ($puntiPerGioco as $g): ?>
                                <div class="card card-body">
                                    <div class="flex-between">
                                        <strong><?= htmlspecialchars(ucfirst($g['gioco'])) ?></strong>
                                        <span class="badge badge-accent"><?= (int)$g['max_score'] ?> pt</span>
                                    </div>
                                    <p class="text-sm muted mt-2"><?= (int)$g['partite'] ?> partite giocate</p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Lezioni in corso -->
                <div class="profile-section">
                    <h3><?= hl_icon('book', 22) ?> Lezioni</h3>
                    <?php if (!$progressi): ?>
                        <p class="muted">Non hai ancora iniziato nessuna lezione.
                            <a href="impara.php">Inizia ora</a>.</p>
                    <?php else: ?>
                        <?php foreach ($progressi as $p): ?>
                            <div class="flex" style="gap:1rem; align-items:center; padding: var(--s-3) 0; border-bottom:1px solid var(--border);">
                                <?= render_sign_visual($p['titolo'] ?? '?', ['color' => 'violet', 'label' => false]) ?>
                                <div style="flex:1;">
                                    <strong><?= htmlspecialchars($p['titolo']) ?></strong>
                                    <div class="progress-bar mt-2">
                                        <div class="progress-bar-fill" style="width: <?= (int)$p['percentuale'] ?>%"></div>
                                    </div>
                                </div>
                                <span class="badge <?= $p['completata'] ? 'badge-success' : 'badge-primary' ?>">
                                    <?= $p['completata'] ? 'Completata' : ((int)$p['percentuale']) . '%' ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
