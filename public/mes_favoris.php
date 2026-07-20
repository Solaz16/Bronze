<?php
$titre_page = 'Mes favoris';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc favoris-page">
    <div class="titre-page-ligne">
        <div>
            <p class="etiquette">Ma collection</p>
            <h2>Mes favoris</h2>
            <p>Retrouve les mangas que tu as mis de cote.</p>
        </div>
        <a class="bouton bouton-secondaire" href="catalogue.php">Catalogue</a>
    </div>
    <div class="catalogue-grille" data-favoris-grille></div>
    <p class="favoris-vide" data-favoris-vide>Tu n'as pas encore de favori. Une etoile dans le catalogue suffit.</p>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
