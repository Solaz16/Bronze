<?php
$titre_page = 'Accueil';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <h2>Accueil</h2>
    <p>Bienvenue sur l'application de gestion de bibliotheque.</p>
    <p>Vous pouvez consulter le catalogue, rechercher un livre, gerer les livres, les utilisateurs et les emprunts.</p>
    <div class="cartes-accueil">
        <a class="carte-accueil" href="catalogue.php">
            <strong>Catalogue</strong>
            <span>Voir les mangas, filtrer et chercher rapidement.</span>
        </a>
        <a class="carte-accueil" href="livre.php?id=1">
            <strong>Blame! recommande</strong>
            <span>Le favori de la bibliotheque est mis en avant.</span>
        </a>
        <?php if (utilisateurConnecte()): ?>
            <a class="carte-accueil" href="dashboard.php">
                <strong>Statistiques</strong>
                <span>Suivre les emprunts, retards et activite.</span>
            </a>
        <?php else: ?>
            <a class="carte-accueil" href="login.php">
                <strong>Connexion</strong>
                <span>Acceder aux fonctions de gestion.</span>
            </a>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
