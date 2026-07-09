<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

pageProtegee();

$pdo = connexionBDD();
$id = (int) ($_GET['id'] ?? 0);
$erreurs = [];
$message = '';

$requete = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
$requete->execute(['id' => $id]);
$utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

if (!$utilisateur) {
    $titre_page = 'Utilisateur introuvable';
    include __DIR__ . '/../templates/header.php';
    ?>
    <section class="bloc">
        <h2>Utilisateur introuvable</h2>
        <a href="utilisateurs.php">Retour</a>
    </section>
    <?php
    include __DIR__ . '/../templates/footer.php';
    exit;
}

$nom = $utilisateur['nom'];
$email = $utilisateur['email'];
$actif = $utilisateur['actif'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $actif = (int) ($_POST['actif'] ?? 0);

    if ($nom === '') {
        $erreurs[] = 'Le nom est obligatoire.';
    }

    if ($email === '') {
        $erreurs[] = "L'email est obligatoire.";
    }

    if (count($erreurs) === 0) {
        try {
            if ($mot_de_passe !== '') {
                $sql = "UPDATE utilisateurs
                        SET nom = :nom, email = :email, mot_de_passe = :mot_de_passe, actif = :actif
                        WHERE id = :id";
                $requete = $pdo->prepare($sql);
                $requete->execute([
                    'nom' => $nom,
                    'email' => $email,
                    'mot_de_passe' => password_hash($mot_de_passe, PASSWORD_DEFAULT),
                    'actif' => $actif,
                    'id' => $id
                ]);
            } else {
                $sql = "UPDATE utilisateurs
                        SET nom = :nom, email = :email, actif = :actif
                        WHERE id = :id";
                $requete = $pdo->prepare($sql);
                $requete->execute([
                    'nom' => $nom,
                    'email' => $email,
                    'actif' => $actif,
                    'id' => $id
                ]);
            }

            $message = "L'utilisateur a bien ete modifie.";
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erreurs[] = 'Cet email existe deja.';
            } else {
                $erreurs[] = "Impossible de modifier l'utilisateur.";
            }
        }
    }
}

$titre_page = 'Modifier un utilisateur';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <h2>Modifier un utilisateur</h2>

    <?php if ($message !== ''): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if (count($erreurs) > 0): ?>
        <div class="erreurs">
            <?php foreach ($erreurs as $erreur): ?>
                <p><?= htmlspecialchars($erreur) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="formulaire">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>">

        <label for="mot_de_passe">Nouveau mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe">

        <label for="actif">Statut</label>
        <select id="actif" name="actif">
            <option value="1" <?= (int) $actif === 1 ? 'selected' : '' ?>>Actif</option>
            <option value="0" <?= (int) $actif === 0 ? 'selected' : '' ?>>Desactive</option>
        </select>

        <button type="submit">Enregistrer</button>
        <a href="utilisateurs.php">Retour</a>
    </form>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
