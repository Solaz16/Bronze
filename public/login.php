<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$pdo = connexionBDD();
$erreur = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    $sql = "SELECT * FROM utilisateurs WHERE email = :email AND actif = 1";
    $requete = $pdo->prepare($sql);
    $requete->execute(['email' => $email]);
    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
        session_regenerate_id(true);
        $_SESSION['utilisateur_id'] = $utilisateur['id'];
        $_SESSION['utilisateur_nom'] = $utilisateur['nom'];
        $_SESSION['utilisateur_email'] = $utilisateur['email'];
        header('Location: index.php');
        exit;
    } else {
        $erreur = 'Email ou mot de passe incorrect.';
    }
}

$titre_page = 'Connexion';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <h2>Connexion</h2>

    <?php if ($erreur !== ''): ?>
        <p class="erreurs"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <form method="post" class="formulaire">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>">

        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe">

        <button type="submit">Se connecter</button>
    </form>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
