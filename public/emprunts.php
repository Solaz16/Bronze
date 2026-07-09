<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

pageProtegee();

$pdo = connexionBDD();

$sql = "SELECT emprunts.*, livres.titre, utilisateurs.nom, DATEDIFF(CURDATE(), emprunts.date_retour_prevue) AS jours_retard
        FROM emprunts
        INNER JOIN livres ON emprunts.livre_id = livres.id
        INNER JOIN utilisateurs ON emprunts.utilisateur_id = utilisateurs.id
        WHERE emprunts.statut = 'en_cours'
        ORDER BY emprunts.date_retour_prevue";
$emprunts = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$titre_page = 'Emprunts';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <h2>Emprunts en cours</h2>
    <p><a class="bouton" href="ajouter_emprunt.php">Nouvel emprunt</a></p>

    <?php if (count($emprunts) === 0): ?>
        <p>Aucun emprunt en cours.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Livre</th>
                    <th>Utilisateur</th>
                    <th>Date emprunt</th>
                    <th>Retour prevu</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($emprunts as $emprunt): ?>
                    <tr class="<?= (int) $emprunt['jours_retard'] > 0 ? 'ligne-retard' : '' ?>">
                        <td><?= htmlspecialchars($emprunt['titre']) ?></td>
                        <td><?= htmlspecialchars($emprunt['nom']) ?></td>
                        <td><?= htmlspecialchars($emprunt['date_emprunt']) ?></td>
                        <td><?= htmlspecialchars($emprunt['date_retour_prevue']) ?></td>
                        <td>
                            <?php if ((int) $emprunt['jours_retard'] > 0): ?>
                                <span class="indisponible">En retard de <?= (int) $emprunt['jours_retard'] ?> jours</span>
                            <?php else: ?>
                                <span class="disponible">En cours</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
