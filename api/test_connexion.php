<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$mysqli = new mysqli("localhost", "root", "", "gestion_boutique");

if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
} else {
    echo "Connexion réussie";
}
?>
