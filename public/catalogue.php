<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/jaquettes.php';

$pdo = connexionBDD();
$recherche = trim($_GET['recherche'] ?? '');
$categorie_id = (int) ($_GET['categorie_id'] ?? 0);
$disponible = $_GET['disponible'] ?? '';
$annee_min = trim($_GET['annee_min'] ?? '');
$annee_max = trim($_GET['annee_max'] ?? '');
$categories = $pdo->query("SELECT id, nom FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

$conditions = [];
$parametres = [];

if ($recherche !== '') {
    $conditions[] = "(LOWER(livres.titre) LIKE LOWER(:recherche) OR LOWER(livres.auteur) LIKE LOWER(:recherche))";
    $parametres['recherche'] = '%' . $recherche . '%';
}

if ($categorie_id > 0) {
    $conditions[] = "livres.categorie_id = :categorie_id";
    $parametres['categorie_id'] = $categorie_id;
}

if ($disponible === '1' || $disponible === '0') {
    $conditions[] = "livres.disponible = :disponible";
    $parametres['disponible'] = (int) $disponible;
}

if ($annee_min !== '' && ctype_digit($annee_min)) {
    $conditions[] = "livres.annee_publication >= :annee_min";
    $parametres['annee_min'] = (int) $annee_min;
}

if ($annee_max !== '' && ctype_digit($annee_max)) {
    $conditions[] = "livres.annee_publication <= :annee_max";
    $parametres['annee_max'] = (int) $annee_max;
}

$sql = "SELECT livres.*, categories.nom AS categorie
        FROM livres
        LEFT JOIN categories ON livres.categorie_id = categories.id";

if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY livres.titre";

$requete = $pdo->prepare($sql);
$requete->execute($parametres);
$livres = $requete->fetchAll(PDO::FETCH_ASSOC);
$titre_page = 'Catalogue';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <h2>Catalogue</h2>

    <form method="get" class="formulaire-recherche">
        <label for="recherche">Rechercher par titre ou auteur</label>
        <div class="ligne-formulaire">
            <input type="text" id="recherche" name="recherche" value="<?= htmlspecialchars($recherche) ?>">
            <button type="submit">Rechercher</button>
        </div>

        <div class="grille-formulaire">
            <div>
                <label for="categorie_id">Categorie</label>
                <select id="categorie_id" name="categorie_id">
                    <option value="0">Toutes</option>
                    <?php foreach ($categories as $categorie): ?>
                        <option value="<?= (int) $categorie['id'] ?>" <?= $categorie_id === (int) $categorie['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($categorie['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="disponible">Disponibilite</label>
                <select id="disponible" name="disponible">
                    <option value="">Tous</option>
                    <option value="1" <?= $disponible === '1' ? 'selected' : '' ?>>Disponibles</option>
                    <option value="0" <?= $disponible === '0' ? 'selected' : '' ?>>Indisponibles</option>
                </select>
            </div>

            <div>
                <label for="annee_min">Annee min</label>
                <input type="number" id="annee_min" name="annee_min" value="<?= htmlspecialchars($annee_min) ?>">
            </div>

            <div>
                <label for="annee_max">Annee max</label>
                <input type="number" id="annee_max" name="annee_max" value="<?= htmlspecialchars($annee_max) ?>">
            </div>
        </div>
    </form>

    <?php if ($recherche !== ''): ?>
        <p>Resultats pour : <?= htmlspecialchars($recherche) ?></p>
    <?php endif; ?>

    <?php if (count($livres) === 0): ?>
        <p>Aucun livre trouve.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Jaquette</th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Categorie</th>
                    <th>Disponibilite</th>
                    <th>Detail</th>
                    <?php if (utilisateurConnecte()): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livres as $livre): ?>
                    <?php $jaquette = jaquetteLivre($livre['titre']); ?>
                    <tr>
                        <td data-label="Jaquette">
                            <div class="jaquette">
                                <?php if ($jaquette !== ''): ?>
                                    <img src="<?= htmlspecialchars($jaquette) ?>" alt="Jaquette de <?= htmlspecialchars($livre['titre']) ?>" onerror="this.remove();">
                                <?php endif; ?>
                                <span>Pas d'image</span>
                            </div>
                        </td>
                        <td data-label="Titre"><?= htmlspecialchars($livre['titre']) ?></td>
                        <td data-label="Auteur"><?= htmlspecialchars($livre['auteur']) ?></td>
                        <td data-label="Categorie"><?= htmlspecialchars($livre['categorie'] ?? 'Non classe') ?></td>
                        <td data-label="Disponibilite">
                            <?php if ($livre['disponible']): ?>
                                <span class="disponible">Disponible</span>
                            <?php else: ?>
                                <span class="indisponible">Indisponible</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Detail"><a href="livre.php?id=<?= (int) $livre['id'] ?>">Voir</a></td>
                        <?php if (utilisateurConnecte()): ?>
                            <td data-label="Actions">
                                <a href="modifier_livre.php?id=<?= (int) $livre['id'] ?>">Modifier</a>
                                <a href="supprimer_livre.php?id=<?= (int) $livre['id'] ?>">Supprimer</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
