<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$mysqli = new mysqli("localhost", "root", "", "gestion_boutique");

if ($mysqli->connect_error) {
    die("Erreur connexion BDD: " . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT * FROM fournisseurs");

echo "<h3>Test Affichage Fournisseurs</h3>";
while ($row = $result->fetch_assoc()) {
    echo "Nom: " . htmlspecialchars($row["nom"]) . " | Contact: " . htmlspecialchars($row["contact"]) . " | Adresse: " . htmlspecialchars($row["adresse"]) . "<br>";
}
?>
