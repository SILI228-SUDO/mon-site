<?php
require 'db.php';

$result = $pdo->query("
    SELECT v.id, p.nom AS produit_nom, v.quantite, v.prix_total, v.date_vente 
    FROM ventes v
    JOIN produits p ON v.produit_id = p.id
    ORDER BY v.date_vente DESC
");

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Rapport de Ventes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: center; }
        th { background-color: #eee; }
        .btn-print { display: block; margin: 20px auto; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; text-align: center; }
    </style>
</head>
<body>
    <h1>Rapport de Ventes - Boutique</h1>
    <a href='#' class='btn-print' onclick='window.print()'>Imprimer ou Enregistrer en PDF</a>
    <table>
        <tr>
            <th>ID</th>
            <th>Produit</th>
            <th>Quantité</th>
            <th>Prix Total</th>
            <th>Date Vente</th>
        </tr>";

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['produit_nom']}</td>
        <td>{$row['quantite']}</td>
        <td>{$row['prix_total']} FCFA</td>
        <td>{$row['date_vente']}</td>
    </tr>";
}

echo "</table>
</body>
</html>";
?>
