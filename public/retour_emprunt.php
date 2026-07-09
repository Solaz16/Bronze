<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

pageProtegee();

$pdo = connexionBDD();
$id = (int) ($_GET['id'] ?? 0);

$requete = $pdo->prepare("SELECT * FROM emprunts WHERE id = :id AND statut = 'en_cours'");
$requete->execute(['id' => $id]);
$emprunt = $requete->fetch(PDO::FETCH_ASSOC);

if ($emprunt) {
    $aujourdhui = new DateTime();
    $retour_prevu = new DateTime($emprunt['date_retour_prevue']);
    $jours_retard = $aujourdhui > $retour_prevu ? (int) $retour_prevu->diff($aujourdhui)->days : 0;
    $statut = $jours_retard > 0 ? 'en_retard' : 'termine';

    $pdo->beginTransaction();

    $requete = $pdo->prepare("UPDATE emprunts
            SET date_retour_effective = CURDATE(), statut = :statut
            WHERE id = :id");
    $requete->execute([
        'statut' => $statut,
        'id' => $id
    ]);

    $requete = $pdo->prepare("UPDATE livres SET disponible = 1 WHERE id = :id");
    $requete->execute(['id' => $emprunt['livre_id']]);

    $requete = $pdo->prepare("UPDATE reservations SET statut = 'disponible' WHERE livre_id = :livre_id AND statut = 'en_attente'");
    $requete->execute(['livre_id' => $emprunt['livre_id']]);

    $pdo->commit();
}

header('Location: emprunts.php');
exit;
