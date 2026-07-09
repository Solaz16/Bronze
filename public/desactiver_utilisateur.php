<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

pageProtegee();

$pdo = connexionBDD();
$id = (int) ($_GET['id'] ?? 0);

$requete = $pdo->prepare("UPDATE utilisateurs SET actif = 0 WHERE id = :id");
$requete->execute(['id' => $id]);

header('Location: utilisateurs.php');
exit;
