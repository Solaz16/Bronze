<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

pageProtegee();

$pdo = connexionBDD();
$erreurs = [];
$message = '';
$livre_id = 0;
$utilisateur_id = 0;

$livres = $pdo->query("SELECT id, titre FROM livres WHERE disponible = 1 ORDER BY titre")->fetchAll(PDO::FETCH_ASSOC);
$utilisateurs = $pdo->query("SELECT id, nom FROM utilisateurs WHERE actif = 1 ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $livre_id = (int) ($_POST['livre_id'] ?? 0);
    $utilisateur_id = (int) ($_POST['utilisateur_id'] ?? 0);

    if ($livre_id <= 0) {
        $erreurs[] = 'Le livre est obligatoire.';
    }

    if ($utilisateur_id <= 0) {
        $erreurs[] = "L'utilisateur est obligatoire.";
    }

    if (count($erreurs) === 0) {
        $requete = $pdo->prepare("SELECT disponible FROM livres WHERE id = :id");
        $requete->execute(['id' => $livre_id]);
        $livre = $requete->fetch(PDO::FETCH_ASSOC);

        if (!$livre || (int) $livre['disponible'] !== 1) {
            $erreurs[] = "Ce livre n'est pas disponible.";
        } else {
            try {
                $pdo->beginTransaction();

                $sql = "INSERT INTO emprunts (livre_id, utilisateur_id, date_emprunt, date_retour_prevue, statut)
                        VALUES (:livre_id, :utilisateur_id, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'en_cours')";
                $requete = $pdo->prepare($sql);
                $requete->execute([
                    'livre_id' => $livre_id,
                    'utilisateur_id' => $utilisateur_id
                ]);

                $requete = $pdo->prepare("UPDATE livres SET disponible = 0 WHERE id = :id");
                $requete->execute(['id' => $livre_id]);

                $pdo->commit();
                $message = "L'emprunt a bien ete cree.";
                $livre_id = 0;
                $utilisateur_id = 0;
                $livres = $pdo->query("SELECT id, titre FROM livres WHERE disponible = 1 ORDER BY titre")->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $pdo->rollBack();
                $erreurs[] = "Impossible de creer l'emprunt.";
            }
        }
    }
}

$titre_page = 'Nouvel emprunt';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <h2>Nouvel emprunt</h2>

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
        <label for="livre_id">Livre disponible</label>
        <select id="livre_id" name="livre_id">
            <option value="0">Choisir un livre</option>
            <?php foreach ($livres as $livre): ?>
                <option value="<?= (int) $livre['id'] ?>" <?= $livre_id === (int) $livre['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($livre['titre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="utilisateur_id">Utilisateur</label>
        <select id="utilisateur_id" name="utilisateur_id">
            <option value="0">Choisir un utilisateur</option>
            <?php foreach ($utilisateurs as $utilisateur): ?>
                <option value="<?= (int) $utilisateur['id'] ?>" <?= $utilisateur_id === (int) $utilisateur['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($utilisateur['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Creer l'emprunt</button>
        <a href="emprunts.php">Retour</a>
    </form>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
