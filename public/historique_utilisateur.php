<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

pageProtegee();

$pdo = connexionBDD();
$id = (int) ($_GET['id'] ?? 0);

$requete = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
$requete->execute(['id' => $id]);
$utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

if (!$utilisateur) {
    $titre_page = 'Utilisateur introuvable';
    include __DIR__ . '/../templates/header.php';
    ?>
    <section class="bloc">
        <h2>Utilisateur introuvable</h2>
        <p>Cet utilisateur n'existe pas.</p>
    </section>
    <?php
    include __DIR__ . '/../templates/footer.php';
    exit;
}

$requete = $pdo->prepare("SELECT emprunts.*, livres.titre
        FROM emprunts
        INNER JOIN livres ON emprunts.livre_id = livres.id
        WHERE emprunts.utilisateur_id = :id
        ORDER BY emprunts.date_emprunt DESC");
$requete->execute(['id' => $id]);
$emprunts = $requete->fetchAll(PDO::FETCH_ASSOC);

$total = count($emprunts);
$titre_page = 'Historique utilisateur';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <h2>Historique de <?= htmlspecialchars($utilisateur['nom']) ?></h2>
    <p>Total emprunts : <?= $total ?></p>

    <?php if ($total === 0): ?>
        <p>Aucun emprunt pour cet utilisateur.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Livre</th>
                    <th>Date emprunt</th>
                    <th>Retour prevu</th>
                    <th>Retour effectif</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($emprunts as $emprunt): ?>
                    <tr>
                        <td data-label="Livre"><?= htmlspecialchars($emprunt['titre']) ?></td>
                        <td data-label="Date emprunt"><?= htmlspecialchars($emprunt['date_emprunt']) ?></td>
                        <td data-label="Retour prevu"><?= htmlspecialchars($emprunt['date_retour_prevue']) ?></td>
                        <td data-label="Retour effectif"><?= htmlspecialchars($emprunt['date_retour_effective'] ?? '-') ?></td>
                        <td data-label="Statut"><?= htmlspecialchars($emprunt['statut']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
