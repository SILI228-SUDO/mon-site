<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Produit</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <h2>Ajouter un produit</h2>
    <form action="ajouter_traitement.php" method="POST">
        <input type="text" name="nom" placeholder="Nom" required><br>
        <input type="text" name="categorie" placeholder="Catégorie" required><br>
        <input type="number" name="prix_achat" placeholder="Prix Achat" required><br>
        <input type="number" name="prix_vente" placeholder="Prix Vente" required><br>
        <input type="number" name="stock" placeholder="Stock" required><br>
        <button type="submit">Enregistrer</button>
    </form>
</body>
</html>
