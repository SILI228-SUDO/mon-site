<?php
$conn = new mysqli("localhost", "root", "", "gestion_boutique");
if ($conn->connect_error) { die("Connexion échouée: " . $conn->connect_error); }
if (!isset($_GET['id'])) { die("ID manquant."); }
$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM produits WHERE id = $id");
$row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Produit</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <h2>Modifier le produit</h2>
    <form action="modifier_traitement.php" method="POST">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <input type="text" name="nom" value="<?= htmlspecialchars($row['nom']) ?>" required><br>
        <input type="text" name="categorie" value="<?= htmlspecialchars($row['categorie']) ?>" required><br>
        <input type="number" name="prix_achat" value="<?= $row['prix_achat'] ?>" required><br>
        <input type="number" name="prix_vente" value="<?= $row['prix_vente'] ?>" required><br>
        <input type="number" name="stock" value="<?= $row['stock'] ?>" required><br>
        <button type="submit">Enregistrer les modifications</button>
    </form>
</body>
</html>
<?php $conn->close(); ?>
