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


ini_set('display_errors', 1);
error_reporting(E_ALL);

$mysqli = new mysqli("localhost", "root", "", "gestion_boutique");

// Enregistrement d'un approvisionnement
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $produit_id = $_POST["produit_id"];
    $fournisseur_id = $_POST["fournisseur_id"];
    $quantite = $_POST["quantite"];

    // Enregistrer l'approvisionnement
    $stmt = $mysqli->prepare("INSERT INTO approvisionnements (produit_id, fournisseur_id, quantite) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $produit_id, $fournisseur_id, $quantite);
    $stmt->execute();

    // Mettre à jour le stock
    $mysqli->query("UPDATE produits SET stock = stock + $quantite WHERE id = $produit_id");

    echo "<script>alert('Approvisionnement enregistré avec succès'); window.location.href='approvisionnements.php';</script>";
    exit;
}

// Récupérer produits et fournisseurs pour les formulaires
$produits = $mysqli->query("SELECT * FROM produits");
$fournisseurs = $mysqli->query("SELECT * FROM fournisseurs");

// Historique des approvisionnements
$historique = $mysqli->query("
    SELECT a.*, p.nom AS nom_produit, f.nom AS nom_fournisseur 
    FROM approvisionnements a
    JOIN produits p ON a.produit_id = p.id
    JOIN fournisseurs f ON a.fournisseur_id = f.id
    ORDER BY a.date_approvisionnement DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approvisionnements</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="container">
    <h2>Approvisionnements</h2>
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


    <h3>Nouvel Approvisionnement</h3>
    <form method="POST">
        <label>Produit :</label>
       
        <select name="produit_id" required>
            <?php while($p = $produits->fetch_assoc()): ?>
                <option value="<?= $p['id'] ?>"><?= $p['nom'] ?> (Stock: <?= $p['stock'] ?>)</option>
            <?php endwhile; ?>
        </select>

        <label>Fournisseur :</label>
        <select name="fournisseur_id" required>
            <?php while($f = $fournisseurs->fetch_assoc()): ?>
                <option value="<?= $f['id'] ?>"><?= $f['nom'] ?></option>
            <?php endwhile; ?>
        </select>

        <input type="number" name="quantite" placeholder="Quantité" min="1" required>
        <button type="submit">Approvisionner</button>
    </form>

    <h3>Historique des Approvisionnements</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th>Produit</th>
            <th>Fournisseur</th>
            <th>Quantité</th>
            <th>Date</th>
        </tr>
        <?php while($row = $historique->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row["nom_produit"]) ?></td>
                <td><?= htmlspecialchars($row["nom_fournisseur"]) ?></td>
                <td><?= $row["quantite"] ?></td>
                <td><?= $row["date_approvisionnement"] ?></td>
                
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
