<?php
$titre_page = 'Accueil';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <h2>Accueil</h2>
    <p>Bienvenue sur l'application de gestion de bibliotheque.</p>
    <p>Vous pouvez consulter le catalogue, rechercher un livre, gerer les livres, les utilisateurs et les emprunts.</p>
    <a class="bouton" href="catalogue.php">Voir le catalogue</a>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
