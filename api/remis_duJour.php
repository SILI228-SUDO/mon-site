<?php
session_start();
if (!isset($_SESSION["utilisateur"])) {
    header("Location: login.php");
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "gestion_boutique");

// Nombre total de produits
$total_produits = $mysqli->query("SELECT COUNT(*) AS total FROM produits")->fetch_assoc()["total"];

// Nombre de ventes aujourd'hui
$aujourdhui = date("Y-m-d");
$ventes_aujourdhui = $mysqli->query("SELECT COUNT(*) AS total FROM ventes WHERE DATE(date_vente) = '$aujourdhui'")->fetch_assoc()["total"];

// Total caisse aujourd'hui
$total_caisse_aujourdhui = $mysqli->query("SELECT SUM(montant) AS total FROM caisse WHERE type='entree' AND DATE(date_operation) = '$aujourdhui'")->fetch_assoc()["total"] ?? 0;

// Produits en stock faible
$stock_faible = $mysqli->query("SELECT COUNT(*) AS total FROM produits WHERE stock <= 5")->fetch_assoc()["total"];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Gestion Boutique</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            width: 220px;
            display: inline-block;
            margin: 10px;
            background: #f9f9f9;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .dashboard-card h3 {
            margin: 5px 0;
        }
    </style>
</head>
<body class="container">
    <h2>Dashboard</h2>

    <p>Bienvenue, <strong><?= htmlspecialchars($_SESSION["utilisateur"]) ?> (<?= htmlspecialchars($_SESSION["role"]) ?>)</strong></p>

    <nav>
        <a href="produits.php">Produits</a> |
        <a href="ventes.php">Ventes</a> |
        <a href="fournisseurs.php">Fournisseurs</a> |
        <a href="approvisionnements.php">Approvisionnements</a> |
        <a href="caisse.php">Caisse</a> |
        <a href="rapport_stock.php">Rapport Stock</a> |
        <a href="rapport_ventes.php">Rapport Ventes</a> |
        <?php if ($_SESSION["role"] == "admin"): ?>
            <a href="utilisateurs.php">Utilisateurs</a> |
        <?php endif; ?>
        <a href="logout.php">Déconnexion</a>
    </nav>

    <h3>Résumé du jour (<?= date("d/m/Y") ?>)</h3>

    <div class="dashboard-card">
        <h3><?= $total_produits ?></h3>
        <p>Produits enregistrés</p>
    </div>

    <div class="dashboard-card">
        <h3><?= $ventes_aujourdhui ?></h3>
        <p>Ventes aujourd'hui</p>
    </div>

    <div class="dashboard-card">
        <h3><?= number_format($total_caisse_aujourdhui, 2) ?> F</h3>
        <p>Total caisse aujourd'hui</p>
    </div>

    <div class="dashboard-card">
        <h3><?= $stock_faible ?></h3>
        <p>Produits en stock faible</p>
    </div>

    <p>
        📊 Utilise les liens ci-dessus pour accéder aux fonctionnalités (ventes, stocks, rapports PDF) pendant ta soutenance.
    </p>

</body>
</html>
