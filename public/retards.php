<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

pageProtegee();

$pdo = connexionBDD();

$sql = "SELECT emprunts.*, livres.titre, utilisateurs.nom, DATEDIFF(CURDATE(), emprunts.date_retour_prevue) AS jours_retard
        FROM emprunts
        INNER JOIN livres ON emprunts.livre_id = livres.id
        INNER JOIN utilisateurs ON emprunts.utilisateur_id = utilisateurs.id
        WHERE emprunts.statut = 'en_cours' AND emprunts.date_retour_prevue < CURDATE()
        ORDER BY jours_retard DESC";
$retards = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$titre_page = 'Retards';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <h2>Emprunts en retard</h2>

    <?php if (count($retards) === 0): ?>
        <p>Aucun retard.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Livre</th>
                    <th>Utilisateur</th>
                    <th>Retour prevu</th>
                    <th>Retard</th>
                    <th>Penalite</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($retards as $retard): ?>
                    <?php $penalite = (int) $retard['jours_retard'] * 0.5; ?>
                    <tr class="ligne-retard">
                        <td data-label="Livre"><?= htmlspecialchars($retard['titre']) ?></td>
                        <td data-label="Utilisateur"><?= htmlspecialchars($retard['nom']) ?></td>
                        <td data-label="Retour prevu"><?= htmlspecialchars($retard['date_retour_prevue']) ?></td>
                        <td data-label="Retard"><span class="indisponible"><?= (int) $retard['jours_retard'] ?> jours</span></td>
                        <td data-label="Penalite"><?= number_format($penalite, 2, ',', ' ') ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
