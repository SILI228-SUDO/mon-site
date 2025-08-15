<?php
session_start();
if (!isset($_SESSION["utilisateur"])) {
    header("Location: login.php");
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "gestion_boutique");
$utilisateur_id = $_SESSION["id"] ?? 1;

// Ajout au panier
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

// Validation de la vente
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

        $stmt = $mysqli->prepare("INSERT INTO ventes (produit_id, quantite, prix_total) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $item['produit_id'], $item['quantite'], $prix_total);
        $stmt->execute();

        $nouveau_stock = $item['stock'] - $item['quantite'];
        $mysqli->query("UPDATE produits SET stock = $nouveau_stock WHERE id = " . $item['produit_id']);
    }

    $motif = "Vente multiple";
    $stmt_caisse = $mysqli->prepare("INSERT INTO caisse (montant, type, motif) VALUES (?, 'entree', ?)");
    $stmt_caisse->bind_param("ds", $total_vente, $motif);
    $stmt_caisse->execute();

    $mysqli->query("DELETE FROM panier_temp WHERE utilisateur_id = $utilisateur_id");

    header("Location: recu_vente.php?type=multiple&montant=$total_vente");
    exit;
}

// Récupération des produits
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
    <title>Ventes - Multi-articles</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f8f8f8;
        }
        nav {
            background-color: blue;
            padding: 10px;
            border-radius: 5px;
        }
        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        nav ul li {
            display: inline;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            background: darkblue;
            padding: 8px 12px;
            border-radius: 4px;
            display: inline-block;
        }
        nav ul li a:hover {
            background: royalblue;
        }
        .container {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
        }
        th {
            background: #eee;
        }
        button {
            background: green;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: darkgreen;
        }
    </style>
</head>
<body>
    <h2>Ventes - Multi-articles</h2>

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

    <div class="container">
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
        <table>
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
    </div>
</body>
</html>
