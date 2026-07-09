<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

pageProtegee();

$pdo = connexionBDD();
$id = (int) ($_GET['id'] ?? 0);

$requete = $pdo->prepare("UPDATE reservations SET statut = 'annulee' WHERE id = :id AND statut = 'en_attente'");
$requete->execute(['id' => $id]);

header('Location: reservations.php');
exit;
