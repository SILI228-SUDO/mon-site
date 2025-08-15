<?php
$conn = new mysqli("localhost", "root", "", "gestion_boutique");
if ($conn->connect_error) { die("Connexion échouée: " . $conn->connect_error); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $categorie = $_POST['categorie'];
    $prix_achat = $_POST['prix_achat'];
    $prix_vente = $_POST['prix_vente'];
    $stock = $_POST['stock'];

    $conn->query("INSERT INTO produits (nom, categorie, prix_achat, prix_vente, stock) VALUES ('$nom','$categorie','$prix_achat','$prix_vente','$stock')");
    header("Location: produits.php");
    exit;
} else {
    die("Accès non autorisé.");
}
$conn->close();
?>
