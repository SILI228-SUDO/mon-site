<?php
session_start();
if (!isset($_SESSION["utilisateur"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["role"] != "admin") {
    echo "<script>alert('Accès refusé. Page réservée aux administrateurs.'); window.location.href='dashboard.php';</script>";
    exit;
}


$conn = new mysqli("localhost", "root", "", "gestion_boutique");
if ($conn->connect_error) { die("Connexion échouée: " . $conn->connect_error); }
$sql = "SELECT * FROM produits";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Produits</title>

    <link rel="stylesheet" href="styles.css">

</head>
<body>
    
<div class="container">
        <h3>Bienvenue, <?= htmlspecialchars($_SESSION["utilisateur"]) ?> (<?= htmlspecialchars($_SESSION["role"]) ?>)</h3>
        <nav>
            <ul>
                <?php if ($_SESSION["role"] == "admin"): ?>
                    <li><a href="produits.php">Produits</a></li>
                    <li><a href="ventes.php">Ventes</a></li>
                    <li><a href="fournisseurs.php">Fournisseurs</a></li>
                    <li><a href="approvisionnements.php">Approvisionnements</a></li>
                    <li><a href="caisse.php">Caisse</a></li>
                    <li><a href="utilisateurs.php">Utilisateurs</a></li>
                <?php else: ?>
                    <li><a href="ventes.php">Enregistrer une vente</a></li>
                    <li><a href="produits_vendeur.php">Produits</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </div>


    <table>
        <tr>
            <th>Nom</th><th>Catégorie</th><th>Prix Achat</th><th>Prix Vente</th><th>Stock</th><th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['nom']) ?></td>
            <td><?= htmlspecialchars($row['categorie']) ?></td>
            <td><?= $row['prix_achat'] ?> F</td>
            <td><?= $row['prix_vente'] ?> F</td>
            <td><?= $row['stock'] ?></td>
            <td>
                <a href="modifier.php?id=<?= $row['id'] ?>">Modifier</a> |
                <a href="supprimer.php?id=<?= $row['id'] ?>" onclick="return confirm('Confirmer suppression ?');">Supprimer</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
<?php $conn->close(); ?>
