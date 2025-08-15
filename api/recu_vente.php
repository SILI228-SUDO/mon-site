<?php
session_start();
if (!isset($_SESSION["utilisateur"])) {
    header("Location: login.php");
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "gestion_boutique");

$type = $_GET["type"] ?? "";
$montant_total = $_GET["montant"] ?? 0;

// Si type=multiple => afficher dernier ticket de vente globale
$produits_vendus = [];
$total = 0;

if ($type == "multiple") {
    $date_today = date('Y-m-d');
    $ventes = $mysqli->query("
        SELECT v.*, p.nom 
        FROM ventes v 
        JOIN produits p ON v.produit_id = p.id 
        WHERE DATE(v.date_vente) = '$date_today'
        ORDER BY v.id DESC
        LIMIT 10
    ");

    while ($row = $ventes->fetch_assoc()) {
        $produits_vendus[] = $row;
        $total += $row['prix_total'];
    }
} else {
    // Cas d'un reçu individuel par id
    $id = intval($_GET["id"] ?? 0);
    $vente = $mysqli->query("
        SELECT v.*, p.nom 
        FROM ventes v 
        JOIN produits p ON v.produit_id = p.id 
        WHERE v.id = $id
    ")->fetch_assoc();

    if ($vente) {
        $produits_vendus[] = $vente;
        $total = $vente['prix_total'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reçu de Vente</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        table { margin: auto; border-collapse: collapse; width: 60%; }
        th, td { border: 1px solid #000; padding: 8px; }
        button { padding: 10px 20px; margin: 20px; }
    </style>
</head>
<body>
    <h2>🧾 Reçu de Vente</h2>
    <table>
        <tr>
            <th>Produit</th>
            <th>Quantité</th>
            <th>Sous-Total</th>
        </tr>
        <?php foreach ($produits_vendus as $vente): ?>
            <tr>
                <td><?= htmlspecialchars($vente["nom"]) ?></td>
                <td><?= $vente["quantite"] ?></td>
                <td><?= number_format($vente["prix_total"], 2) ?> F</td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th colspan="2">Total</th>
            <th><?= number_format($total, 2) ?> F</th>
        </tr>
    </table>
    <p>Date : <?= date('d-m-Y H:i') ?></p>

    <button onclick="window.print()">🖨️ Imprimer le reçu</button>

    <script>
        window.onafterprint = function() {
            window.location.href = 'ventes.php';
        };
        setTimeout(function() {
            window.location.href = 'ventes.php';
        }, 10000);
    </script>
</body>
</html>
