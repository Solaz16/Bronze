<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

pageProtegee();

$pdo = connexionBDD();

$stats = [
    'livres' => (int) $pdo->query("SELECT COUNT(*) FROM livres")->fetchColumn(),
    'utilisateurs' => (int) $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn(),
    'emprunts' => (int) $pdo->query("SELECT COUNT(*) FROM emprunts")->fetchColumn(),
    'en_cours' => (int) $pdo->query("SELECT COUNT(*) FROM emprunts WHERE statut = 'en_cours'")->fetchColumn(),
    'termines' => (int) $pdo->query("SELECT COUNT(*) FROM emprunts WHERE statut = 'termine'")->fetchColumn(),
    'retards' => (int) $pdo->query("SELECT COUNT(*) FROM emprunts WHERE statut = 'en_cours' AND date_retour_prevue < CURDATE()")->fetchColumn()
];

$top_livres = $pdo->query("SELECT livres.titre, COUNT(emprunts.id) AS total
        FROM emprunts
        INNER JOIN livres ON emprunts.livre_id = livres.id
        GROUP BY livres.id, livres.titre
        ORDER BY total DESC
        LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

$top_categories = $pdo->query("SELECT categories.nom, COUNT(emprunts.id) AS total
        FROM emprunts
        INNER JOIN livres ON emprunts.livre_id = livres.id
        INNER JOIN categories ON livres.categorie_id = categories.id
        GROUP BY categories.id, categories.nom
        ORDER BY total DESC
        LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

$titre_page = 'Statistiques';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <h2>Tableau de bord</h2>

    <div class="cartes-stats">
        <div><strong><?= $stats['livres'] ?></strong><span>Livres</span></div>
        <div><strong><?= $stats['utilisateurs'] ?></strong><span>Utilisateurs</span></div>
        <div><strong><?= $stats['emprunts'] ?></strong><span>Emprunts</span></div>
        <div><strong><?= $stats['en_cours'] ?></strong><span>En cours</span></div>
        <div><strong><?= $stats['termines'] ?></strong><span>Termines</span></div>
        <div><strong><?= $stats['retards'] ?></strong><span>Retards</span></div>
    </div>

    <div class="deux-colonnes">
        <div>
            <h3>Top livres</h3>
            <?php foreach ($top_livres as $livre): ?>
                <p><?= htmlspecialchars($livre['titre']) ?> : <?= (int) $livre['total'] ?> emprunt(s)</p>
            <?php endforeach; ?>
        </div>
        <div>
            <h3>Top categories</h3>
            <?php foreach ($top_categories as $categorie): ?>
                <p><?= htmlspecialchars($categorie['nom']) ?> : <?= (int) $categorie['total'] ?> emprunt(s)</p>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
