<?php
session_start();
if (!isset($_SESSION["utilisateur"])) {
    header("Location: login.php");
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "gestion_boutique");

// Récupération des produits
$produits = $mysqli->query("SELECT * FROM produits ORDER BY nom ASC");

// Si export PDF demandé
if (isset($_GET["export"]) && $_GET["export"] == "pdf") {
    require_once("fpdf/fpdf.php");

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont("Arial", "B", 16);
    $pdf->Cell(0, 10, "Rapport de Stock - Boutique", 0, 1, "C");
    $pdf->Ln(5);

    $pdf->SetFont("Arial", "B", 12);
    $pdf->Cell(60, 10, "Produit", 1);
    $pdf->Cell(40, 10, "Categorie", 1);
    $pdf->Cell(30, 10, "Stock", 1);
    $pdf->Cell(30, 10, "Seuil Alerte", 1);
    $pdf->Ln();

    $produits->data_seek(0);
    $pdf->SetFont("Arial", "", 12);

    while ($row = $produits->fetch_assoc()) {
        $pdf->Cell(60, 10, $row["nom"], 1);
        $pdf->Cell(40, 10, $row["categorie"], 1);
        $pdf->Cell(30, 10, $row["stock"], 1);
        $seuil_alerte = ($row["stock"] <= 5) ? "⚠️ Faible" : "OK";
        $pdf->Cell(30, 10, $seuil_alerte, 1);
        $pdf->Ln();
    }

    $pdf->Output("D", "rapport_stock.pdf");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rapport de Stock</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="container">
    <h2>Rapport de Stock</h2>

    <a href="dashboard.php">Dashboard</a> |
    <a href="ventes.php">Ventes</a> |
    <a href="approvisionnements.php">Approvisionnements</a> |
    <a href="caisse.php">Caisse</a> |
    <a href="rapport_stock.php?export=pdf">📄 Exporter en PDF</a> |
    <a href="logout.php">Se déconnecter</a>

    <table border="1" cellpadding="5">
        <tr>
            <th>Produit</th>
            <th>Catégorie</th>
            <th>Stock</th>
            <th>Seuil d'Alerte</th>
        </tr>
        <?php while($row = $produits->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row["nom"]) ?></td>
                <td><?= htmlspecialchars($row["categorie"]) ?></td>
                <td><?= $row["stock"] ?></td>
                <td><?= ($row["stock"] <= 5) ? "<span style='color:red;'>⚠️ Faible</span>" : "OK" ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
