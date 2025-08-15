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

// Ajout fournisseur
if (isset($_POST["ajouter"])) {
    $nom = $_POST["nom"];
    $contact = $_POST["contact"];
    $adresse = $_POST["adresse"];
    $stmt = $mysqli->prepare("INSERT INTO fournisseurs (nom, contact, adresse) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nom, $contact, $adresse);
    $stmt->execute();
    header("Location: fournisseurs.php");
    exit;
}

// Modifier fournisseur
if (isset($_POST["modifier"])) {
    $id = $_POST["id"];
    $nom = $_POST["nom"];
    $contact = $_POST["contact"];
    $adresse = $_POST["adresse"];
    $stmt = $mysqli->prepare("UPDATE fournisseurs SET nom=?, contact=?, adresse=? WHERE id=?");
    $stmt->bind_param("sssi", $nom, $contact, $adresse, $id);
    $stmt->execute();
    header("Location: fournisseurs.php");
    exit;
}

// Supprimer fournisseur
if (isset($_GET["supprimer"])) {
    $id = $_GET["supprimer"];
    $stmt = $mysqli->prepare("DELETE FROM fournisseurs WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: fournisseurs.php");
    exit;
}

// Liste des fournisseurs
$fournisseurs = $mysqli->query("SELECT * FROM fournisseurs");

// Si modification
$fournisseur_a_modifier = null;
if (isset($_GET["modifier"])) {
    $id_modifier = $_GET["modifier"];
    $result = $mysqli->query("SELECT * FROM fournisseurs WHERE id=$id_modifier");
    $fournisseur_a_modifier = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des Fournisseurs</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="container">

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

    <h3><?= $fournisseur_a_modifier ? "Modifier un Fournisseur" : "Ajouter un Fournisseur" ?></h3>
    <form method="POST">
        <input type="text" name="nom" placeholder="Nom" required value="<?= $fournisseur_a_modifier['nom'] ?? '' ?>">
        <input type="text" name="contact" placeholder="Contact" required value="<?= $fournisseur_a_modifier['contact'] ?? '' ?>">
        <input type="text" name="adresse" placeholder="Adresse" required value="<?= $fournisseur_a_modifier['adresse'] ?? '' ?>">
        <?php if ($fournisseur_a_modifier): ?>
            <input type="hidden" name="id" value="<?= $fournisseur_a_modifier['id'] ?>">
            <button type="submit" name="modifier">Modifier</button>
        <?php else: ?>
            <button type="submit" name="ajouter">Ajouter</button>
        <?php endif; ?>
    </form>

    <h3>Liste des Fournisseurs</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th>Nom</th>
            <th>Contact</th>
            <th>Adresse</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $fournisseurs->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row["nom"]) ?></td>
                <td><?= htmlspecialchars($row["contact"]) ?></td>
                <td><?= htmlspecialchars($row["adresse"]) ?></td>
                <td>
                    <a href="?modifier=<?= $row["id"] ?>">Modifier</a> |
                    <a href="?supprimer=<?= $row["id"] ?>" onclick="return confirm('Supprimer ce fournisseur ?')">Supprimer</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
