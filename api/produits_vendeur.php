<?php
// Connexion à la base
$conn = new mysqli("localhost", "root", "", "gestion_boutique");
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Lire les produits
$sql = "SELECT * FROM produits";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Produits</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="container">
        <nav>
            <ul>
                    <li><a href="ventes.php">Enregistrer une vente</a></li>
                    <li><a href="produits_vendeur.php">Produits</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </div>

    <table>
        <tr>
            <th>Nom</th>
            <th>Catégorie</th>
            <th>Prix de vente</th>
            <th>Stock</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nom']) ?></td>
                    <td><?= htmlspecialchars($row['categorie']) ?></td>
                    <td><?= $row['prix_vente'] ?></td>
                    <td><?= $row['stock'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">Aucun produit trouvé.</td></tr>
        <?php endif; ?>

    </table>

</body>
</html>

<?php $conn->close(); ?>
