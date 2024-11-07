<?php
require_once "autoload.php";
// Contrôleur

if (isset($_POST['login'])) {
    $inscrits = new InscritsDAO(MaBD::getInstance());
    $auth = $inscrits->checkAuthentification($_POST['login'], $_POST['mdp']);
    if (isset($auth)) {
        session_start();
        $_SESSION['nom']=$auth->getName();
        $_SESSION['prenom']=$auth->getSurname();
        $_SESSION['role']=$auth->getRole(); #<--- Ici ça ne marche pas pour user Je sais pas pk 
        $_SESSION['mail']=$auth->getMail();
    }
    else{
        echo "Tu t'es fail en te co";
    }



}
session_start();
if(isset($_SESSION)){
    switch($_SESSION['role']){
        case "ROLE_ADMIN":
            $where = "gestionInscrits.php";
            header("Location: ".$where);
            break;
        case "ROLE_REDACTEUR":
            $where = "envoie.php";
            header("Location: ".$where);
            break;
        case "ROLE_UTILISATEUR":
            $where = "listeInscrits.php";
            header("Location: ".$where);
            break;
        case "Logout":
            $_SESSION = [];
        default:
            break;
        }

        
}

// Contrôleur



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
        <p>Identifiant</p>
        <input type="text" name="login" id="login" value="">
        <p>Mots de passe</p>
        <input type="password" name="mdp" id="mdp" value="">
        <input type="image" name="submit[]" src="img/plus.png" alt="Ajout" />
        </p>
    </form>
</body>

</html>