<?php 
$dsn = 'mysql:host=localhost;dbname=minichat';
$user = "root";

try {
    $pdo = new PDO($dsn, $user, '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} 
catch (PDOException $e) { 
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

?>