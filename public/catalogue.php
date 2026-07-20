<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/jaquettes.php';

$pdo = connexionBDD();
$recherche = trim($_GET['recherche'] ?? '');
$categorie_id = (int) ($_GET['categorie_id'] ?? 0);
$disponible = $_GET['disponible'] ?? '';
$annee_min = trim($_GET['annee_min'] ?? '');
$annee_max = trim($_GET['annee_max'] ?? '');
$ordre = $_GET['ordre'] ?? 'titre';
$page = (int) ($_GET['page'] ?? 1);
$par_page = 10;

if ($page < 1) {
    $page = 1;
}

$depart = ($page - 1) * $par_page;
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

$sql_base = " FROM livres
        LEFT JOIN categories ON livres.categorie_id = categories.id";

if (count($conditions) > 0) {
    $sql_base .= " WHERE " . implode(" AND ", $conditions);
}

$sql_count = "SELECT COUNT(*)" . $sql_base;
$requete_count = $pdo->prepare($sql_count);
$requete_count->execute($parametres);
$compteurs = $pdo->prepare("SELECT COUNT(*) AS total, SUM(CASE WHEN livres.disponible = 1 THEN 1 ELSE 0 END) AS disponibles, SUM(CASE WHEN livres.disponible = 0 THEN 1 ELSE 0 END) AS indisponibles" . $sql_base);
$compteurs->execute($parametres);
$statistiques = $compteurs->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'disponibles' => 0, 'indisponibles' => 0];

$total = (int) $statistiques['total'];
$total_disponibles = (int) $statistiques['disponibles'];
$total_indisponibles = (int) $statistiques['indisponibles'];
$total_pages = max(1, (int) ceil($total / $par_page));

if ($page > $total_pages) {
    $page = $total_pages;
    $depart = ($page - 1) * $par_page;
}

$ordre_sql = $ordre === 'recent' ? 'livres.annee_publication DESC, livres.titre' : ($ordre === 'auteur' ? 'livres.auteur, livres.titre' : 'livres.titre');
$sql = "SELECT livres.*, categories.nom AS categorie" . $sql_base . " ORDER BY " . $ordre_sql . " LIMIT :depart, :par_page";

$requete = $pdo->prepare($sql);

foreach ($parametres as $cle => $valeur) {
    $requete->bindValue(':' . $cle, $valeur);
}

$requete->bindValue('depart', $depart, PDO::PARAM_INT);
$requete->bindValue('par_page', $par_page, PDO::PARAM_INT);
$requete->execute();
$livres = $requete->fetchAll(PDO::FETCH_ASSOC);
$livres_affiches = count($livres);

$filtres_actifs = [];
if ($recherche !== '') {
    $filtres_actifs[] = 'Recherche: ' . $recherche;
}
if ($categorie_id > 0) {
    foreach ($categories as $categorie) {
        if ((int) $categorie['id'] === $categorie_id) {
            $filtres_actifs[] = 'Categorie: ' . $categorie['nom'];
            break;
        }
    }
}
if ($disponible === '1') {
    $filtres_actifs[] = 'Disponibles uniquement';
} elseif ($disponible === '0') {
    $filtres_actifs[] = 'Indisponibles uniquement';
}
if ($annee_min !== '') {
    $filtres_actifs[] = 'Depuis ' . $annee_min;
}
if ($annee_max !== '') {
    $filtres_actifs[] = 'Jusqu a ' . $annee_max;
}
if ($ordre === 'auteur') {
    $filtres_actifs[] = 'Tri auteur';
} elseif ($ordre === 'recent') {
    $filtres_actifs[] = 'Tri recent';
}

$pages_a_afficher = [];
for ($i = 1; $i <= $total_pages; $i++) {
    if ($i === 1 || $i === $total_pages || abs($i - $page) <= 1) {
        $pages_a_afficher[] = $i;
    }
}

$requete_blame = $pdo->prepare("SELECT livres.*, categories.nom AS categorie
        FROM livres
        LEFT JOIN categories ON livres.categorie_id = categories.id
        WHERE livres.titre = :titre
        LIMIT 1");
$requete_blame->execute(['titre' => 'Blame!']);
$blame = $requete_blame->fetch(PDO::FETCH_ASSOC);
$titre_page = 'Catalogue';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <h2>Catalogue</h2>
    <p class="catalogue-intro">Une collection dense de mangas cultes, sombres et contemporains. Les filtres se mettent a jour instantanement et la pagination reste legere meme avec une grosse base.</p>

    <div class="catalogue-metrics" aria-label="Statistiques du catalogue">
        <article class="catalogue-metric">
            <strong><?= $total ?></strong>
            <span>mangas trouves</span>
        </article>
        <article class="catalogue-metric">
            <strong><?= $total_disponibles ?></strong>
            <span>disponibles</span>
        </article>
        <article class="catalogue-metric">
            <strong><?= $total_indisponibles ?></strong>
            <span>indisponibles</span>
        </article>
        <article class="catalogue-metric">
            <strong><?= $livres_affiches ?></strong>
            <span>sur cette page</span>
        </article>
    </div>

    <?php if ($blame): ?>
        <?php $jaquette_blame = jaquetteLivre($blame['titre'], $blame['couverture'] ?? '', $blame['auteur'] ?? ''); ?>
        <div class="recommandation-blame">
            <?php if ($jaquette_blame !== ''): ?>
                <img src="<?= htmlspecialchars($jaquette_blame) ?>" alt="Jaquette de Blame!">
            <?php endif; ?>
            <div>
                <p class="etiquette">Recommande</p>
                <h3>Blame!</h3>
                <p>Le manga mis en avant de la bibliotheque : sombre, immense, cyberpunk, et franchement style.</p>
                <a class="bouton" href="livre.php?id=<?= (int) $blame['id'] ?>">Voir Blame!</a>
            </div>
        </div>
    <?php endif; ?>

    <form method="get" class="formulaire-recherche" data-catalogue-form>
        <label for="recherche">Recherche instantanee</label>
        <div class="ligne-formulaire">
            <input type="text" id="recherche" name="recherche" value="<?= htmlspecialchars($recherche) ?>" placeholder="Titre, auteur, ambiance..." autocomplete="off" data-catalogue-search>
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
            <div>
                <label for="ordre">Trier</label>
                <select id="ordre" name="ordre">
                    <option value="titre" <?= $ordre === 'titre' ? 'selected' : '' ?>>Titre</option>
                    <option value="auteur" <?= $ordre === 'auteur' ? 'selected' : '' ?>>Auteur</option>
                    <option value="recent" <?= $ordre === 'recent' ? 'selected' : '' ?>>Plus recent</option>
                </select>
            </div>
        </div>
        <div class="outils-catalogue">
            <button type="button" class="bouton bouton-secondaire" data-surprise>Surprise</button>
            <button type="button" class="bouton bouton-secondaire" data-filtre-favoris>Mes favoris</button>
            <a class="bouton bouton-secondaire" href="catalogue.php">Reinitialiser</a>
        </div>
    </form>

    <div class="catalogue-filtres-actifs" aria-label="Filtres actifs">
        <?php if (count($filtres_actifs) > 0): ?>
            <?php foreach ($filtres_actifs as $filtre): ?>
                <span><?= htmlspecialchars($filtre) ?></span>
            <?php endforeach; ?>
        <?php else: ?>
            <span>Tous les mangas</span>
        <?php endif; ?>
    </div>

    <?php if ($recherche !== ''): ?>
        <p>Resultats pour : <?= htmlspecialchars($recherche) ?></p>
    <?php endif; ?>

    <div class="catalogue-entete">
        <p data-catalogue-page-count><?= $livres_affiches ?> manga(s) visibles sur cette page.</p>
        <p class="aide-js">Appuie sur / pour chercher rapidement</p>
    </div>
    <p class="aucun-resultat" hidden>Aucun manga ne correspond a ta recherche.</p>

    <?php if (count($livres) === 0): ?>
        <p>Aucun livre trouve.</p>
    <?php else: ?>
        <div class="catalogue-grille">
            <?php foreach ($livres as $livre): ?>
                <?php $jaquette = jaquetteLivre($livre['titre'], $livre['couverture'] ?? '', $livre['auteur'] ?? ''); ?>
                <article class="carte-livre <?= $livre['titre'] === 'Blame!' ? 'carte-blame' : '' ?>" data-id="<?= (int) $livre['id'] ?>" data-titre="<?= htmlspecialchars($livre['titre']) ?>" data-auteur="<?= htmlspecialchars($livre['auteur']) ?>" data-categorie="<?= htmlspecialchars($livre['categorie'] ?? '') ?>" data-url="livre.php?id=<?= (int) $livre['id'] ?>">
                    <a class="carte-image" href="livre.php?id=<?= (int) $livre['id'] ?>">
                        <div class="jaquette">
                            <?php if ($jaquette !== ''): ?>
                                <img src="<?= htmlspecialchars($jaquette) ?>" alt="Jaquette de <?= htmlspecialchars($livre['titre']) ?>" onerror="this.remove();">
                            <?php endif; ?>
                        </div>
                    </a>

                    <div class="carte-contenu">
                        <?php if ($livre['titre'] === 'Blame!'): ?>
                            <span class="etiquette">Recommande</span>
                        <?php endif; ?>
                        <div class="progression-mini" data-progression-mini><span></span></div>
                        <h3><?= htmlspecialchars($livre['titre']) ?></h3>
                        <p><?= htmlspecialchars($livre['auteur']) ?></p>
                        <p><?= htmlspecialchars($livre['categorie'] ?? 'Non classe') ?></p>
                        <?php if ($livre['disponible']): ?>
                            <span class="disponible">Disponible</span>
                        <?php else: ?>
                            <span class="indisponible">Indisponible</span>
                        <?php endif; ?>
                    </div>

                    <div class="carte-actions">
                        <a class="bouton" href="livre.php?id=<?= (int) $livre['id'] ?>">Voir</a>
                        <?php if (utilisateurConnecte()): ?>
                            <a href="modifier_livre.php?id=<?= (int) $livre['id'] ?>">Modifier</a>
                            <a href="supprimer_livre.php?id=<?= (int) $livre['id'] ?>">Supprimer</a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="pagination">
            <?php
            $params = $_GET;
            unset($params['page']);
            $base_pagination = http_build_query($params);
            $prefixe = $base_pagination === '' ? '?' : '?' . $base_pagination . '&';
            ?>
            <?php if ($page > 1): ?>
                <a href="<?= $prefixe ?>page=<?= $page - 1 ?>">Precedent</a>
            <?php endif; ?>

            <?php $page_precedente = 0; ?>
            <?php foreach ($pages_a_afficher as $numero_page): ?>
                <?php if ($page_precedente !== 0 && $numero_page > $page_precedente + 1): ?>
                    <span class="pagination-ellipsis">...</span>
                <?php endif; ?>
                <a class="<?= $numero_page === $page ? 'page-active' : '' ?>" href="<?= $prefixe ?>page=<?= $numero_page ?>"><?= $numero_page ?></a>
                <?php $page_precedente = $numero_page; ?>
            <?php endforeach; ?>

            <?php if ($page < $total_pages): ?>
                <a href="<?= $prefixe ?>page=<?= $page + 1 ?>">Suivant</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
