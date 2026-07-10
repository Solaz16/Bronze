<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

pageProtegee();

$pdo = connexionBDD();
$erreurs = [];
$message = '';

$categories = $pdo->query("SELECT id, nom FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

$titre = '';
$auteur = '';
$isbn = '';
$annee = '';
$resume = '';
$categorie_id = '';
$couverture = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $auteur = trim($_POST['auteur'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    $annee = trim($_POST['annee_publication'] ?? '');
    $resume = trim($_POST['resume'] ?? '');
    $categorie_id = (int) ($_POST['categorie_id'] ?? 0);

    if ($titre === '') {
        $erreurs[] = 'Le titre est obligatoire.';
    }

    if ($auteur === '') {
        $erreurs[] = "L'auteur est obligatoire.";
    }

    if ($isbn === '') {
        $erreurs[] = "L'ISBN est obligatoire.";
    }

    if ($categorie_id <= 0) {
        $erreurs[] = 'La categorie est obligatoire.';
    }

    if ($annee !== '' && (!ctype_digit($annee) || (int) $annee < 0)) {
        $erreurs[] = "L'annee doit etre un nombre valide.";
    }

    if (isset($_FILES['couverture']) && $_FILES['couverture']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['couverture']['error'] !== UPLOAD_ERR_OK) {
            $erreurs[] = "La couverture n'a pas pu etre envoyee.";
        } elseif ($_FILES['couverture']['size'] > 2000000) {
            $erreurs[] = 'La couverture est trop lourde.';
        } else {
            $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $type = mime_content_type($_FILES['couverture']['tmp_name']);

            if (!isset($types[$type])) {
                $erreurs[] = 'La couverture doit etre en jpg, png ou webp.';
            } else {
                $dossier = __DIR__ . '/uploads/couvertures';

                if (!is_dir($dossier)) {
                    mkdir($dossier, 0777, true);
                }

                $nom_fichier = uniqid('couverture_', true) . '.' . $types[$type];
                $destination = $dossier . '/' . $nom_fichier;

                if (move_uploaded_file($_FILES['couverture']['tmp_name'], $destination)) {
                    $couverture = 'uploads/couvertures/' . $nom_fichier;
                } else {
                    $erreurs[] = "La couverture n'a pas pu etre enregistree.";
                }
            }
        }
    }

    if (count($erreurs) === 0) {
        try {
            $sql = "INSERT INTO livres (titre, auteur, isbn, annee_publication, resume, categorie_id, disponible, couverture)
                    VALUES (:titre, :auteur, :isbn, :annee_publication, :resume, :categorie_id, 1, :couverture)";
            $requete = $pdo->prepare($sql);
            $requete->execute([
                'titre' => $titre,
                'auteur' => $auteur,
                'isbn' => $isbn,
                'annee_publication' => $annee === '' ? null : (int) $annee,
                'resume' => $resume,
                'categorie_id' => $categorie_id,
                'couverture' => $couverture
            ]);

            $message = 'Le livre a bien ete ajoute.';
            $titre = '';
            $auteur = '';
            $isbn = '';
            $annee = '';
            $resume = '';
            $categorie_id = '';
            $couverture = '';
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erreurs[] = 'Cet ISBN existe deja.';
            } else {
                $erreurs[] = "Impossible d'ajouter le livre.";
            }
        }
    }
}

$titre_page = 'Ajouter un livre';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <h2>Ajouter un livre</h2>

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

    <form method="post" class="formulaire" enctype="multipart/form-data">
        <label for="titre">Titre</label>
        <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($titre) ?>">

        <label for="auteur">Auteur</label>
        <input type="text" id="auteur" name="auteur" value="<?= htmlspecialchars($auteur) ?>">

        <label for="isbn">ISBN</label>
        <input type="text" id="isbn" name="isbn" value="<?= htmlspecialchars($isbn) ?>">

        <label for="annee_publication">Annee de publication</label>
        <input type="number" id="annee_publication" name="annee_publication" value="<?= htmlspecialchars($annee) ?>">

        <label for="categorie_id">Categorie</label>
        <select id="categorie_id" name="categorie_id">
            <option value="">Choisir une categorie</option>
            <?php foreach ($categories as $categorie): ?>
                <option value="<?= (int) $categorie['id'] ?>" <?= (int) $categorie_id === (int) $categorie['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($categorie['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="resume">Resume</label>
        <textarea id="resume" name="resume" rows="5"><?= htmlspecialchars($resume) ?></textarea>
        <button class="bouton-synopsis" type="button" data-synopsis>Remplir le resume</button>

        <label for="couverture">Couverture</label>
        <input type="file" id="couverture" name="couverture" accept="image/jpeg,image/png,image/webp">

        <button type="submit">Ajouter</button>
    </form>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
