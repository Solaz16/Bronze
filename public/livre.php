<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/jaquettes.php';
require_once __DIR__ . '/../config/auth.php';

$pdo = connexionBDD();
$id = (int) ($_GET['id'] ?? 0);
$message = '';
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reserver' && utilisateurConnecte()) {
    $utilisateur_id = (int) ($_SESSION['utilisateur']['id'] ?? 0);

    $requete = $pdo->prepare("SELECT id, disponible FROM livres WHERE id = :id");
    $requete->execute(['id' => $id]);
    $livre_reservation = $requete->fetch(PDO::FETCH_ASSOC);

    if (!$livre_reservation) {
        $erreurs[] = "Ce livre n'existe pas.";
    } elseif ((int) $livre_reservation['disponible'] === 1) {
        $erreurs[] = 'Ce livre est deja disponible.';
    } else {
        $requete = $pdo->prepare("SELECT id FROM reservations WHERE livre_id = :livre_id AND utilisateur_id = :utilisateur_id AND statut = 'en_attente'");
        $requete->execute([
            'livre_id' => $id,
            'utilisateur_id' => $utilisateur_id
        ]);

        if ($requete->fetch()) {
            $erreurs[] = 'Vous avez deja une reservation active pour ce livre.';
        } else {
            $requete = $pdo->prepare("INSERT INTO reservations (livre_id, utilisateur_id, date_reservation, statut)
                    VALUES (:livre_id, :utilisateur_id, CURDATE(), 'en_attente')");
            $requete->execute([
                'livre_id' => $id,
                'utilisateur_id' => $utilisateur_id
            ]);
            $message = 'Reservation enregistree.';
        }
    }
}

$sql = "SELECT livres.*, categories.nom AS categorie
        FROM livres
        LEFT JOIN categories ON livres.categorie_id = categories.id
        WHERE livres.id = :id";
$requete = $pdo->prepare($sql);
$requete->execute(['id' => $id]);
$livre = $requete->fetch(PDO::FETCH_ASSOC);

$recommandations = [];
if ($livre && $livre['categorie_id']) {
    $requete_recommandations = $pdo->prepare("SELECT id, titre, auteur FROM livres WHERE categorie_id = :categorie_id AND id != :id ORDER BY titre LIMIT 4");
    $requete_recommandations->execute(['categorie_id' => $livre['categorie_id'], 'id' => $id]);
    $recommandations = $requete_recommandations->fetchAll(PDO::FETCH_ASSOC);
}

$titre_page = 'Detail du livre';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc" data-detail-livre data-id="<?= (int) $id ?>" data-titre="<?= htmlspecialchars($livre['titre'] ?? '') ?>">
    <?php if (!$livre): ?>
        <h2>Livre introuvable</h2>
        <p>Le livre demande n'existe pas.</p>
        <a href="catalogue.php">Retour au catalogue</a>
    <?php else: ?>
        <?php $jaquette = jaquetteLivre($livre['titre'], $livre['couverture'] ?? '', $livre['auteur'] ?? ''); ?>
        <h2><?= htmlspecialchars($livre['titre']) ?></h2>
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
        <div class="detail-livre">
            <div class="jaquette grande-jaquette">
                <?php if ($jaquette !== ''): ?>
                    <img src="<?= htmlspecialchars($jaquette) ?>" alt="Jaquette de <?= htmlspecialchars($livre['titre']) ?>" onerror="this.remove();">
                <?php endif; ?>
            </div>
            <div>
        <p><strong>Auteur :</strong> <?= htmlspecialchars($livre['auteur']) ?></p>
        <p><strong>ISBN :</strong> <?= htmlspecialchars($livre['isbn']) ?></p>
        <p><strong>Annee :</strong> <?= htmlspecialchars($livre['annee_publication']) ?></p>
        <p><strong>Categorie :</strong> <?= htmlspecialchars($livre['categorie'] ?? 'Non classe') ?></p>
        <p><strong>Disponibilite :</strong>
            <?php if ($livre['disponible']): ?>
                <span class="disponible">Disponible</span>
            <?php else: ?>
                <span class="indisponible">Indisponible</span>
            <?php endif; ?>
        </p>
        <h3>Resume</h3>
        <p><?= nl2br(htmlspecialchars($livre['resume'])) ?></p>
        <div class="lecture">
            <div class="lecture-entete"><strong>Progression de lecture</strong><span data-progression-texte>0%</span></div>
            <input type="range" min="0" max="100" value="0" data-progression aria-label="Progression de lecture">
        </div>
        <?php if (utilisateurConnecte() && (int) $livre['disponible'] === 0): ?>
            <form method="post" class="formulaire-court">
                <input type="hidden" name="action" value="reserver">
                <button type="submit">Reserver ce livre</button>
            </form>
        <?php endif; ?>
            </div>
        </div>
        <?php if (utilisateurConnecte()): ?>
            <p>
                <a class="bouton" href="modifier_livre.php?id=<?= (int) $livre['id'] ?>">Modifier</a>
                <a class="bouton bouton-danger" href="supprimer_livre.php?id=<?= (int) $livre['id'] ?>">Supprimer</a>
            </p>
        <?php endif; ?>
        <a href="catalogue.php">Retour au catalogue</a>
        <?php if (count($recommandations) > 0): ?>
            <section class="recommandations-liees">
                <h3>Dans le meme univers</h3>
                <div class="liste-recommandations">
                    <?php foreach ($recommandations as $recommandation): ?>
                        <a href="livre.php?id=<?= (int) $recommandation['id'] ?>">
                            <strong><?= htmlspecialchars($recommandation['titre']) ?></strong>
                            <span><?= htmlspecialchars($recommandation['auteur']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
