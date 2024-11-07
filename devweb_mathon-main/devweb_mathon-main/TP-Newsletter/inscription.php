<?php
require_once "autoload.php";
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
    </div>
    </div>

    <h1>Connexion</h1>
    <p></p>
    <p class="binome">Réalisée par : Alonzo MATHON - Groupe 2G</p>

    <form action="" method="post">
        <p class="login">
        <p>login</p>
        <input type="text" name="login" id="login" value="">
        <p>mots de passe</p>
        <input type="password" name="mdp" id="mdp" value="">
        <p>e-mail</p>
        <input type="text" name="mail" id="mail" require>
        <input type="image" name="submit[]" src="img/plus.png" alt="Ajout" />
        </p>
    </form>
</body>

</html>