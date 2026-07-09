<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

pageProtegee();

$pdo = connexionBDD();
$id = (int) ($_GET['id'] ?? 0);
$message = '';
$erreur = '';

$requete = $pdo->prepare("SELECT * FROM livres WHERE id = :id");
$requete->execute(['id' => $id]);
$livre = $requete->fetch(PDO::FETCH_ASSOC);

if ($livre && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $requete = $pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE livre_id = :id");
    $requete->execute(['id' => $id]);
    $nombre = (int) $requete->fetchColumn();

    if ($nombre > 0) {
        $erreur = 'Ce livre ne peut pas etre supprime car il a des emprunts.';
    } else {
        $requete = $pdo->prepare("DELETE FROM livres WHERE id = :id");
        $requete->execute(['id' => $id]);
        $message = 'Le livre a ete supprime.';
        $livre = false;
    }
}

$titre_page = 'Supprimer un livre';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <h2>Supprimer un livre</h2>

    <?php if ($message !== ''): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
        <a href="catalogue.php">Retour au catalogue</a>
    <?php elseif (!$livre): ?>
        <p>Le livre demande n'existe pas.</p>
        <a href="catalogue.php">Retour au catalogue</a>
    <?php else: ?>
        <?php if ($erreur !== ''): ?>
            <p class="erreurs"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <p>Voulez-vous supprimer le livre suivant ?</p>
        <p><strong><?= htmlspecialchars($livre['titre']) ?></strong> de <?= htmlspecialchars($livre['auteur']) ?></p>

        <form method="post">
            <button type="submit" class="bouton-danger">Supprimer</button>
            <a href="catalogue.php">Annuler</a>
        </form>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
