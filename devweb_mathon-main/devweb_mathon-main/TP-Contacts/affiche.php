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
        <p class="message">Message de classe 'message'</p>
        <p class="erreur">Message de classe 'erreur'</p>
        <table>
        <?php
        foreach($dao->getAll() as $people){
            echo $people->toTableRow();
        }
        ?>
        </table>
    </body>
</html>
