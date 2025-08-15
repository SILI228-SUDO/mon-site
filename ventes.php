<?php
session_start();
if (!isset($_SESSION["utilisateur"])) {
    header("Location: login.php");
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "gestion_boutique");
$utilisateur_id = $_SESSION["id"] ?? 1; // par sécurité

// Ajouter au panier
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_panier"])) {
    $produit_id = intval($_POST["produit_id"]);
    $quantite = intval($_POST["quantite"]);

    $result = $mysqli->query("SELECT stock FROM produits WHERE id = $produit_id");
    $produit = $result->fetch_assoc();

    if ($produit && $quantite <= $produit['stock']) {
        $stmt = $mysqli->prepare("INSERT INTO panier_temp (produit_id, quantite, utilisateur_id) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $produit_id, $quantite, $utilisateur_id);
        $stmt->execute();
    } else {
        echo "<script>alert('Stock insuffisant');</script>";
    }
}

// Valider la vente
if (isset($_POST["valider_vente"])) {
    $panier = $mysqli->query("
        SELECT pt.*, p.prix_vente, p.stock 
        FROM panier_temp pt 
        JOIN produits p ON pt.produit_id = p.id 
        WHERE pt.utilisateur_id = $utilisateur_id
    ");

    $total_vente = 0;
    while ($item = $panier->fetch_assoc()) {
        $prix_total = $item['prix_vente'] * $item['quantite'];
        $total_vente += $prix_total;

        // Insérer la vente
        $stmt = $mysqli->prepare("INSERT INTO ventes (produit_id, quantite, prix_total) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $item['produit_id'], $item['quantite'], $prix_total);
        $stmt->execute();

        

        // Mise à jour du stock
        $nouveau_stock = $item['stock'] - $item['quantite'];
        $mysqli->query("UPDATE produits SET stock = $nouveau_stock WHERE id = " . $item['produit_id']);

        
    }

    // Insérer dans caisse
    $motif = "Vente multiple";
    $stmt_caisse = $mysqli->prepare("INSERT INTO caisse (montant, type, motif) VALUES (?, 'entree', ?)");
    $stmt_caisse->bind_param("ds", $total_vente, $motif);
    $stmt_caisse->execute();

    // Vider le panier
    $mysqli->query("DELETE FROM panier_temp WHERE utilisateur_id = $utilisateur_id");

    // Redirection vers le reçu global
    header("Location: recu_vente.php?type=multiple&montant=$total_vente");
    exit;
}

// Produits
$produits = $mysqli->query("SELECT * FROM produits");

// Panier actuel
$panier_items = $mysqli->query("
    SELECT pt.id, p.nom, pt.quantite 
    FROM panier_temp pt 
    JOIN produits p ON pt.produit_id = p.id 
    WHERE pt.utilisateur_id = $utilisateur_id
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ventes (Multi-articles)</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Ventes - Multi-articles</h2>
     <div class="container">
        <h2>Bienvenue, <?= htmlspecialchars($_SESSION["utilisateur"]) ?> (<?= htmlspecialchars($_SESSION["role"]) ?>)</h2>
        <nav>
            <div class='table'>
                <ul>
                <?php if ($_SESSION["role"] == "admin"): ?>
                    <a href="produits.php">Gérer les produits</a> |
                    <a href="ventes.php">Gérer les ventes</a> |
                    <a href="fournisseurs.php">Gérer les fournisseurs</a> |
                    <a href="approvisionnements.php">Gérer les approvisionnements</a> |
                    <a href="caisse.php">Gérer la caisse</a> |
                    <a href="utilisateurs.php">Gérer les utilisateurs</a> |
                <?php else: ?>
                    <a href="ventes.php">Enregistrer une vente</a>
                    <a href="produits_vendeur.php">Voir les produits</a>
                <?php endif; ?>

                <a href="logout.php">Se déconnecter</a>
                </ul>
            </div>
        </nav>
    </div>

    <form method="POST">
        <label>Produit :</label>
        <select name="produit_id" required>
            <?php while($row = $produits->fetch_assoc()) { ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nom']) ?> (Stock: <?= $row['stock'] ?>)</option>
            <?php } ?>
        </select>
        <input type="number" name="quantite" placeholder="Quantité" min="1" required>
        <button type="submit" name="add_panier">Ajouter au panier</button>
    </form>

    <h3>Panier en cours</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th>Produit</th>
            <th>Quantité</th>
        </tr>
        <?php while($item = $panier_items->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($item['nom']) ?></td>
                <td><?= $item['quantite'] ?></td>
            </tr>
        <?php } ?>
    </table>

    <form method="POST">
        <button type="submit" name="valider_vente">Valider la vente et générer le reçu</button>
    </form>
</body>
</html>
