<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "gestion_boutique");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_utilisateur = $_POST["nom_utilisateur"];
    $mot_de_passe = md5($_POST["mot_de_passe"]);

    $stmt = $mysqli->prepare("SELECT * FROM utilisateurs WHERE nom_utilisateur = ? AND mot_de_passe = ?");
    $stmt->bind_param("ss", $nom_utilisateur, $mot_de_passe);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION["utilisateur"] = $user["nom_utilisateur"];
        $_SESSION["role"] = $user["role"];
        header("Location: dashboard.php");
        exit;
    } else {
        echo "<script>alert('Identifiants incorrects');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Connexion</h2>
        <form method="POST">
            <input type="text" name="nom_utilisateur" placeholder="Nom d'utilisateur" required><br>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required><br>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>
