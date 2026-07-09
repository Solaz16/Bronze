<nav>
    <a href="index.php">Accueil</a>
    <a href="catalogue.php">Catalogue</a>
    <?php if (utilisateurConnecte()): ?>
        <a href="ajouter.php">Ajouter un livre</a>
        <a href="utilisateurs.php">Utilisateurs</a>
        <a href="emprunts.php">Emprunts</a>
        <span>Connecte : <?= htmlspecialchars(nomUtilisateur()) ?></span>
        <a href="logout.php">Deconnexion</a>
    <?php else: ?>
        <a href="login.php">Connexion</a>
    <?php endif; ?>
</nav>
