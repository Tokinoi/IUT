<?php
// Autochargement des classes
require_once "autoload.php";
$db = new MaBD();
$dao = new ContactsDAO($db->getInstance());
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8"/>
        <link rel="stylesheet" type="text/css" href="Contacts.css"/>
        <title>Modèle de page pour le TP Contacts</title>
    </head>
    <body>
        <p class="binome"><img src="img/LogoIUTValence-H.png" alt="Logo IUT"/>Réalisée par : Alonzo MATHON- Groupe 1G</p> 
        <h1 class=titre>Gestion des contacts</h1>
        <table>
        <?php

        function arrayToContact(array $tab):Contact{
            $out=new Contact($tab['id'],$tab['name'],$tab['surname'],$tab['tel']);
            return $out;
        }


        ## Bonjour future moi, tu as essayer d'ajouter des contacts mais ça à bugger quand tu as ajouter 
        ## les ligne $dba -> save ou $dba->delete ! Bonne chance pour ta prochaine manip !
        
        ## Moi du future : Bah c'est pas dba c'est dao ... 

        if(isset($_COOKIE['connect'])){
            if ($_COOKIE['connect'] != "log"){
                setcookie("notconnect","not connect", time()+10,"/");
                header("Location: connexion.php");
            }
        }
        else{
            setcookie("notconnect","not connect", time()+10,"/");
            header("Location: connexion.php");
        }
        if(isset($_POST['supp'])){
            $contact = arrayToContact($_POST);
            $dao->delete($contact);
            $msg =  $_POST['name'] ." ". $_POST['surname'] . " (" . $_POST['tel'] . ") à bien été supprimer";
            echo '<p class="message">'. $msg .'</p>';
        }
        if(isset($_POST['modif'])){
            $contact = arrayToContact($_POST);
            $dao->save($contact);
        }
        if(isset($_POST['plus'])){
            if($_POST['tel'] === ""){
                echo '<p class="erreur">Numéro de téléphone requis</p>';
            }
            else if($_POST['name'] === "" and $_POST['surname'] === ""){
                echo '<p class="erreur">Nom ou Prénom requis</p>';
            }
            else{
            $contact =  arrayToContact($_POST);
            $dao->save($contact);
            $msg = $_POST['name'] ." ". $_POST['surname'] . " (" . $_POST['tel'] . ") à bien été ajouter"; 
            echo '<p class="message">'. $msg .'</p>';
            }
        }
        foreach($dao->getAll() as $people){
            echo $people->toForm();
        }
        echo '<form action="" method="post">
        <p class="form">
            <input type="hidden" name="id" id="id" value="' . DAO::UNKNOWN_ID . '">
            <input type="text" name="name" id="name" value="">
            <input type="text" name="surname" id="surname" value="">
            <input type="tel" name="tel" id="tel" value="">
            <input type="image" name="plus[]" src="img/plus.png" alt="Ajout"/>
            </p>
        </form>';
        ?>
        </table>
    </body>
</html>
