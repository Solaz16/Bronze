<?php
require_once __DIR__ . '/../config/database.php';

$pdo = connexionBDD();
$id = (int) ($_GET['id'] ?? 0);

$sql = "SELECT livres.*, categories.nom AS categorie
        FROM livres
        LEFT JOIN categories ON livres.categorie_id = categories.id
        WHERE livres.id = :id";
$requete = $pdo->prepare($sql);
$requete->execute(['id' => $id]);
$livre = $requete->fetch(PDO::FETCH_ASSOC);

$titre_page = 'Detail du livre';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <?php if (!$livre): ?>
        <h2>Livre introuvable</h2>
        <p>Le livre demande n'existe pas.</p>
        <a href="catalogue.php">Retour au catalogue</a>
    <?php else: ?>
        <h2><?= htmlspecialchars($livre['titre']) ?></h2>
        <p><strong>Auteur :</strong> <?= htmlspecialchars($livre['auteur']) ?></p>
        <p><strong>ISBN :</strong> <?= htmlspecialchars($livre['isbn']) ?></p>
        <p><strong>Annee :</strong> <?= htmlspecialchars($livre['annee_publication']) ?></p>
        <p><strong>Categorie :</strong> <?= htmlspecialchars($livre['categorie'] ?? 'Non classe') ?></p>
        <p><strong>Disponibilite :</strong>
            <?php if ($livre['disponible']): ?>
                <span class="disponible">Disponible</span>
            <?php else: ?>
                <span class="indisponible">Indisponible</span>
            <?php endif; ?>
        </p>
        <h3>Resume</h3>
        <p><?= nl2br(htmlspecialchars($livre['resume'])) ?></p>
        <?php if (utilisateurConnecte()): ?>
            <p>
                <a class="bouton" href="modifier_livre.php?id=<?= (int) $livre['id'] ?>">Modifier</a>
                <a class="bouton bouton-danger" href="supprimer_livre.php?id=<?= (int) $livre['id'] ?>">Supprimer</a>
            </p>
        <?php endif; ?>
        <a href="catalogue.php">Retour au catalogue</a>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
