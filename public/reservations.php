<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

pageProtegee();

$pdo = connexionBDD();

$sql = "SELECT reservations.*, livres.titre, utilisateurs.nom
        FROM reservations
        INNER JOIN livres ON reservations.livre_id = livres.id
        INNER JOIN utilisateurs ON reservations.utilisateur_id = utilisateurs.id
        ORDER BY reservations.date_reservation DESC";
$reservations = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$titre_page = 'Reservations';
include __DIR__ . '/../templates/header.php';
?>

<section class="bloc">
    <h2>Reservations</h2>

    <?php if (count($reservations) === 0): ?>
        <p>Aucune reservation.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Livre</th>
                    <th>Utilisateur</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation): ?>
                    <tr>
                        <td data-label="Livre"><?= htmlspecialchars($reservation['titre']) ?></td>
                        <td data-label="Utilisateur"><?= htmlspecialchars($reservation['nom']) ?></td>
                        <td data-label="Date"><?= htmlspecialchars($reservation['date_reservation']) ?></td>
                        <td data-label="Statut">
                            <?php if ($reservation['statut'] === 'disponible'): ?>
                                <span class="disponible">Disponible maintenant</span>
                            <?php elseif ($reservation['statut'] === 'annulee'): ?>
                                <span class="indisponible">Annulee</span>
                            <?php else: ?>
                                <span class="badge-attente">En attente</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Action">
                            <?php if ($reservation['statut'] === 'en_attente'): ?>
                                <a href="annuler_reservation.php?id=<?= (int) $reservation['id'] ?>">Annuler</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../templates/footer.php'; ?>
