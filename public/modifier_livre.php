<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

pageProtegee();

$pdo = connexionBDD();
$id = (int) ($_GET['id'] ?? 0);
$erreurs = [];
$message = '';
$categories = $pdo->query("SELECT id, nom FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

$requete = $pdo->prepare("SELECT * FROM livres WHERE id = :id");
$requete->execute(['id' => $id]);
$livre = $requete->fetch(PDO::FETCH_ASSOC);

if (!$livre) {
    $titre_page = 'Livre introuvable';
    include __DIR__ . '/../templates/header.php';
    ?>
    <section class="bloc">
        <h2>Livre introuvable</h2>
        <p>Le livre demande n'existe pas.</p>
        <a href="catalogue.php">Retour au catalogue</a>
    </section>
    <?php
    include __DIR__ . '/../templates/footer.php';
    exit;
}

$titre = $livre['titre'];
$auteur = $livre['auteur'];
$isbn = $livre['isbn'];
$annee = $livre['annee_publication'];
$resume = $livre['resume'];
$categorie_id = $livre['categorie_id'];
$disponible = $livre['disponible'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $auteur = trim($_POST['auteur'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    $annee = trim($_POST['annee_publication'] ?? '');
    $resume = trim($_POST['resume'] ?? '');
    $categorie_id = (int) ($_POST['categorie_id'] ?? 0);
    $disponible = (int) ($_POST['disponible'] ?? 0);

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

    if (count($erreurs) === 0) {
        try {
            $sql = "UPDATE livres
                    SET titre = :titre, auteur = :auteur, isbn = :isbn, annee_publication = :annee_publication,
                        resume = :resume, categorie_id = :categorie_id, disponible = :disponible
                    WHERE id = :id";
            $requete = $pdo->prepare($sql);
            $requete->execute([
                'titre' => $titre,
                'auteur' => $auteur,
                'isbn' => $isbn,
                'annee_publication' => $annee === '' ? null : (int) $annee,
                'resume' => $resume,
                'categorie_id' => $categorie_id,
                'disponible' => $disponible,
                'id' => $id
            ]);
            $message = 'Le livre a bien ete modifie.';
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erreurs[] = 'Cet ISBN existe deja.';
            } else {
                $erreurs[] = 'Impossible de modifier le livre.';
            }
        }
    }
}

$titre_page = 'Modifier un livre';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <h2>Modifier un livre</h2>

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
            <?php foreach ($categories as $categorie): ?>
                <option value="<?= (int) $categorie['id'] ?>" <?= (int) $categorie_id === (int) $categorie['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($categorie['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="disponible">Disponibilite</label>
        <select id="disponible" name="disponible">
            <option value="1" <?= (int) $disponible === 1 ? 'selected' : '' ?>>Disponible</option>
            <option value="0" <?= (int) $disponible === 0 ? 'selected' : '' ?>>Indisponible</option>
        </select>

        <label for="resume">Resume</label>
        <textarea id="resume" name="resume" rows="5"><?= htmlspecialchars($resume) ?></textarea>

        <button type="submit">Enregistrer</button>
    </form>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
