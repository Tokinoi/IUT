<?php
require_once "autoload.php";
// Contrôleur

session_start();
if ($_SESSION['role'] != 'ROLE_ADMIN'){
    header("Location: connexion.php");
}

//Controleur
$message = "Bonjour";
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" type="text/css" href="newsletter.css" />
    <title>Liste des inscrits</title>
</head>

<body>
    <div id="bandeau">
        <div id="titre">
            <img src="LogoIUT-V-300x300.png" alt="Logo UGA IUT" />
            &nbsp;&nbsp;&nbsp;&nbsp;Newsletter
        </div>
        <form method="post">
        <?php
        echo $_SESSION['nom'] . " " . $_SESSION['prenom'];
        ?>
        <input type="submit" name="Logout[]" value="Déconnection">
        </form>
    </div>
    </div>
    <h1>Gestion des inscrits</h1>
    <p></p>
    <p class="binome">Réalisée par : Alonzo MATHON - Groupe 2G</p>
    <pre>

    </pre>
</body>

</html>