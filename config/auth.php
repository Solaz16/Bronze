<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function utilisateurConnecte()
{
    return isset($_SESSION['utilisateur_id']);
}

function pageProtegee()
{
    if (!utilisateurConnecte()) {
        header('Location: login.php');
        exit;
    }
}

function nomUtilisateur()
{
    return $_SESSION['utilisateur_nom'] ?? '';
}
