<?php
require_once __DIR__ . '/../config/database.php';

$pdo = connexionBDD();
$total = (int) $pdo->query("SELECT COUNT(*) FROM livres")->fetchColumn();
$disponibles = (int) $pdo->query("SELECT COUNT(*) FROM livres WHERE disponible = 1")->fetchColumn();
$auteurs = (int) $pdo->query("SELECT COUNT(DISTINCT auteur) FROM livres")->fetchColumn();
$categorie_top = $pdo->query("SELECT categories.nom, COUNT(livres.id) AS total FROM categories LEFT JOIN livres ON livres.categorie_id = categories.id GROUP BY categories.id, categories.nom ORDER BY total DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$genres = $pdo->query("SELECT categories.nom, COUNT(livres.id) AS total FROM categories LEFT JOIN livres ON livres.categorie_id = categories.id GROUP BY categories.id, categories.nom ORDER BY total DESC")->fetchAll(PDO::FETCH_ASSOC);

$titre_page = 'Statistiques du catalogue';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <p class="etiquette">Vue du catalogue</p>
    <h2>Statistiques</h2>
    <div class="cartes-stats stats-catalogue">
        <div><strong><?= $total ?></strong><span>Mangas</span></div>
        <div><strong><?= $disponibles ?></strong><span>Disponibles</span></div>
        <div><strong><?= $auteurs ?></strong><span>Auteurs</span></div>
        <div><strong><?= htmlspecialchars($categorie_top['nom'] ?? '-') ?></strong><span>Genre le plus present</span></div>
    </div>
    <h3>Repartition par genre</h3>
    <div class="barres-stats">
        <?php foreach ($genres as $genre): ?>
            <div><span><?= htmlspecialchars($genre['nom']) ?></span><div class="barre-genre"><i style="width: <?= $total > 0 ? ((int) $genre['total'] / $total) * 100 : 0 ?>%"></i></div><strong><?= (int) $genre['total'] ?></strong></div>
        <?php endforeach; ?>
    </div>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
