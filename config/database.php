<?php

function connexionBDD()
{
    $host = 'localhost';
    $dbname = 'bibliotheque_tp5';
    $user = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die('Erreur de connexion a la base de donnees.');
    }
}
