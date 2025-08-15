<?php
$conn = new mysqli("localhost", "root", "", "gestion_boutique");
if ($conn->connect_error) { die("Connexion échouée: " . $conn->connect_error); }
if (!isset($_GET['id'])) { die("ID manquant."); }
$id = intval($_GET['id']);

$conn->query("DELETE FROM produits WHERE id = $id");
header("Location: produits.php");
exit;
$conn->close();
?>
