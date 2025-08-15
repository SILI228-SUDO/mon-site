<?php
session_start();
if (!isset($_SESSION["utilisateur"])) {
    header("Location: login.php");
    exit;
}
/////////////////////////////////////////////////////////////////
if ($_SESSION["role"] != "admin") {
    echo "<script>alert('Accès refusé. Page réservée aux administrateurs.'); window.location.href='dashboard.php';</script>";
    exit;
}

////////////////////////////////////////////////////////////////


$mysqli = new mysqli("localhost", "root", "", "gestion_boutique");

// Ajouter un utilisateur
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_utilisateur = $_POST["nom_utilisateur"];
    $mot_de_passe = md5($_POST["mot_de_passe"]);
    $role = $_POST["role"];

    // Vérifier si le nom d'utilisateur existe déjà
    $stmt_check = $mysqli->prepare("SELECT id FROM utilisateurs WHERE nom_utilisateur = ?");
    $stmt_check->bind_param("s", $nom_utilisateur);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<script>alert('Nom d\\'utilisateur déjà existant. Choisissez un autre.');</script>";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO utilisateurs (nom_utilisateur, mot_de_passe, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nom_utilisateur, $mot_de_passe, $role);
        $stmt->execute();
        echo "<script>alert('Utilisateur ajouté avec succès.'); window.location.href='utilisateurs.php';</script>";
        exit;
    }
}

// Supprimer un utilisateur
if (isset($_GET["delete"])) {
    $id = $_GET["delete"];
    $mysqli->query("DELETE FROM utilisateurs WHERE id = $id");
    header("Location: utilisateurs.php");
    exit;
}

// Récupérer les utilisateurs
$utilisateurs = $mysqli->query("SELECT * FROM utilisateurs ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des Utilisateurs</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Gestion des Utilisateurs</h2>

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

        <h3>Ajouter un nouvel utilisateur</h3>
        <form method="POST">
            <input type="text" name="nom_utilisateur" placeholder="Nom d'utilisateur" required><br>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required><br>
            <select name="role" required>
                <option value="admin">Admin</option>
                <option value="vendeur">Vendeur</option>
            </select><br>
            <button type="submit">Ajouter l'utilisateur</button>
        </form>

        <h3>Liste des utilisateurs</h3>
        <table border="1" cellpadding="5" style="width: 100%; margin-top: 10px;">
            <tr>
                <th>ID</th>
                <th>Nom d'utilisateur</th>
                <th>Rôle</th>
                <th>Action</th>
            </tr>
            <?php while($row = $utilisateurs->fetch_assoc()): ?>
                <tr>
                    <td><?= $row["id"] ?></td>
                    <td><?= htmlspecialchars($row["nom_utilisateur"]) ?></td>
                    <td><?= htmlspecialchars($row["role"]) ?></td>
                    <td>
                        <?php if ($row["nom_utilisateur"] != $_SESSION["utilisateur"]): ?>
                            <a href="utilisateurs.php?delete=<?= $row["id"] ?>" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
                        <?php else: ?>
                            (Connecté)
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
