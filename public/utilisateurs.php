<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

pageProtegee();

$pdo = connexionBDD();
$erreurs = [];
$message = '';
$nom = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if ($nom === '') {
        $erreurs[] = 'Le nom est obligatoire.';
    }

    if ($email === '') {
        $erreurs[] = "L'email est obligatoire.";
    }

    if ($mot_de_passe === '') {
        $erreurs[] = 'Le mot de passe est obligatoire.';
    }

    if (count($erreurs) === 0) {
        try {
            $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe, actif)
                    VALUES (:nom, :email, :mot_de_passe, 1)";
            $requete = $pdo->prepare($sql);
            $requete->execute([
                'nom' => $nom,
                'email' => $email,
                'mot_de_passe' => password_hash($mot_de_passe, PASSWORD_DEFAULT)
            ]);
            $message = "L'utilisateur a bien ete ajoute.";
            $nom = '';
            $email = '';
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erreurs[] = 'Cet email existe deja.';
            } else {
                $erreurs[] = "Impossible d'ajouter l'utilisateur.";
            }
        }
    }
}

$utilisateurs = $pdo->query("SELECT * FROM utilisateurs ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

$titre_page = 'Utilisateurs';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <h2>Utilisateurs</h2>

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

    <h3>Ajouter un utilisateur</h3>
    <form method="post" class="formulaire">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>">

        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe">

        <button type="submit">Ajouter</button>
    </form>

    <h3>Liste des utilisateurs</h3>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utilisateurs as $utilisateur): ?>
                <tr>
                    <td data-label="Nom"><?= htmlspecialchars($utilisateur['nom']) ?></td>
                    <td data-label="Email"><?= htmlspecialchars($utilisateur['email']) ?></td>
                    <td data-label="Statut">
                        <?php if ($utilisateur['actif']): ?>
                            <span class="disponible">Actif</span>
                        <?php else: ?>
                            <span class="indisponible">Desactive</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Actions">
                        <a href="modifier_utilisateur.php?id=<?= (int) $utilisateur['id'] ?>">Modifier</a>
                        <?php if ($utilisateur['actif']): ?>
                            <a href="desactiver_utilisateur.php?id=<?= (int) $utilisateur['id'] ?>">Desactiver</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
