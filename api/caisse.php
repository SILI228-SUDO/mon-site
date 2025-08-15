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

// Historique des opérations
$operations = $mysqli->query("SELECT * FROM caisse ORDER BY date_operation DESC");

// Solde du jour
$today = date('Y-m-d');
$total_entrees_jour = $mysqli->query("SELECT SUM(montant) AS total FROM caisse WHERE type='entree' AND DATE(date_operation)='$today'")->fetch_assoc()["total"] ?? 0;
$total_sorties_jour = $mysqli->query("SELECT SUM(montant) AS total FROM caisse WHERE type='sortie' AND DATE(date_operation)='$today'")->fetch_assoc()["total"] ?? 0;
$solde_jour = $total_entrees_jour - $total_sorties_jour;

// Solde de la semaine
$week_start = date('Y-m-d', strtotime('monday this week'));
$week_end = date('Y-m-d', strtotime('sunday this week'));
$total_entrees_semaine = $mysqli->query("SELECT SUM(montant) AS total FROM caisse WHERE type='entree' AND DATE(date_operation) BETWEEN '$week_start' AND '$week_end'")->fetch_assoc()["total"] ?? 0;
$total_sorties_semaine = $mysqli->query("SELECT SUM(montant) AS total FROM caisse WHERE type='sortie' AND DATE(date_operation) BETWEEN '$week_start' AND '$week_end'")->fetch_assoc()["total"] ?? 0;
$solde_semaine = $total_entrees_semaine - $total_sorties_semaine;

// Solde du mois
$month = date('m');
$year = date('Y');
$total_entrees_mois = $mysqli->query("SELECT SUM(montant) AS total FROM caisse WHERE type='entree' AND MONTH(date_operation)='$month' AND YEAR(date_operation)='$year'")->fetch_assoc()["total"] ?? 0;
$total_sorties_mois = $mysqli->query("SELECT SUM(montant) AS total FROM caisse WHERE type='sortie' AND MONTH(date_operation)='$month' AND YEAR(date_operation)='$year'")->fetch_assoc()["total"] ?? 0;
$solde_mois = $total_entrees_mois - $total_sorties_mois;

// Solde global
$total_entrees = $mysqli->query("SELECT SUM(montant) AS total FROM caisse WHERE type='entree'")->fetch_assoc()["total"] ?? 0;
$total_sorties = $mysqli->query("SELECT SUM(montant) AS total FROM caisse WHERE type='sortie'")->fetch_assoc()["total"] ?? 0;
$solde_global = $total_entrees - $total_sorties;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gestion de la Caisse</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="container">
    <h2>Gestion de la Caisse</h2>

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

    <h3>🪙 Solde Actuel</h3>
    <ul>
        <li>Solde du jour (<?= date('d-m-Y') ?>) : <strong><?= number_format($solde_jour, 2) ?> F</strong></li>
        <li>Solde de la semaine (<?= $week_start ?> au <?= $week_end ?>) : <strong><?= number_format($solde_semaine, 2) ?> F</strong></li>
        <li>Solde du mois (<?= date('F Y') ?>) : <strong><?= number_format($solde_mois, 2) ?> F</strong></li>
        <li>Solde global : <strong><?= number_format($solde_global, 2) ?> F</strong></li>
    </ul>

    <h3>Historique des opérations</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th>Montant</th>
            <th>Nom Produit</th>
            <th>Type</th>
            <th>Motif</th>
            <th>Date</th>
        </tr>
        <?php while($row = $operations->fetch_assoc()): ?>
            <?php
                // Extraire le nom du produit du motif si disponible
                $nom_produit = "-";
                if (strpos($row["motif"], "Vente:") !== false) {
                    $nom_produit = trim(str_replace("Vente:", "", $row["motif"]));
                }
            ?>
            <tr>
                <td><?= number_format($row["montant"], 2) ?> F</td>
                <td><?= htmlspecialchars($nom_produit) ?></td>
                <td><?= htmlspecialchars($row["type"]) ?></td>
                <td><?= htmlspecialchars($row["motif"]) ?></td>
                <td><?= $row["date_operation"] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
