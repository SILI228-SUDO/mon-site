<?php
$conn = new mysqli("localhost", "root", "", "gestion_boutique");
if ($conn->connect_error) { die("Connexion échouée: " . $conn->connect_error); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $nom = $_POST['nom'];
    $categorie = $_POST['categorie'];
    $prix_achat = $_POST['prix_achat'];
    $prix_vente = $_POST['prix_vente'];
    $stock = $_POST['stock'];

    $sql = "UPDATE produits SET nom='$nom', categorie='$categorie', prix_achat='$prix_achat', prix_vente='$prix_vente', stock='$stock' WHERE id=$id";
    $conn->query($sql);
    header("Location: produits.php");
    exit;
} else {
    die("Accès non autorisé.");
}
$conn->close();
?>
