<?php
require_once __DIR__ . '/../config/auth.php';

if (!isset($titre_page)) {
    $titre_page = 'Bibliotheque';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titre_page) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Bibliotheque municipale</h1>
        <?php include __DIR__ . '/nav.php'; ?>
    </header>
    <main>
