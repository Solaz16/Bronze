<?php
require_once __DIR__ . '/../config/database.php';

$pdo = connexionBDD();
$recherche = trim($_GET['recherche'] ?? '');

if ($recherche !== '') {
    $sql = "SELECT livres.*, categories.nom AS categorie
            FROM livres
            LEFT JOIN categories ON livres.categorie_id = categories.id
            WHERE LOWER(livres.titre) LIKE LOWER(:recherche)
            OR LOWER(livres.auteur) LIKE LOWER(:recherche)
            ORDER BY livres.titre";
    $requete = $pdo->prepare($sql);
    $requete->execute(['recherche' => '%' . $recherche . '%']);
} else {
    $sql = "SELECT livres.*, categories.nom AS categorie
            FROM livres
            LEFT JOIN categories ON livres.categorie_id = categories.id
            ORDER BY livres.titre";
    $requete = $pdo->query($sql);
}

$livres = $requete->fetchAll(PDO::FETCH_ASSOC);
$titre_page = 'Catalogue';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <h2>Catalogue</h2>

    <form method="get" class="formulaire-recherche">
        <label for="recherche">Rechercher par titre ou auteur</label>
        <div>
            <input type="text" id="recherche" name="recherche" value="<?= htmlspecialchars($recherche) ?>">
            <button type="submit">Rechercher</button>
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
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Categorie</th>
                    <th>Disponibilite</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livres as $livre): ?>
                    <tr>
                        <td><?= htmlspecialchars($livre['titre']) ?></td>
                        <td><?= htmlspecialchars($livre['auteur']) ?></td>
                        <td><?= htmlspecialchars($livre['categorie'] ?? 'Non classe') ?></td>
                        <td>
                            <?php if ($livre['disponible']): ?>
                                <span class="disponible">Disponible</span>
                            <?php else: ?>
                                <span class="indisponible">Indisponible</span>
                            <?php endif; ?>
                        </td>
                        <td><a href="livre.php?id=<?= (int) $livre['id'] ?>">Voir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
