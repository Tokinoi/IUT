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
        if(isset($_POST['supp'])){
            echo "<pre>"; 
            print_r($_POST); 
            echo "</pre>";
        }
        if(isset($_POST['modif'])){
            echo "<pre>"; 
            print_r($_POST); 
            echo "</pre>";
        }
        if(isset($_POST['plus'])){
            echo "<pre>"; 
            print_r($_POST); 
            echo "</pre>";
        }
        foreach($dao->getAll() as $people){
            echo $people->toForm();
        }
        echo '<form action="" method="post">
        <p class="form">
            <input type="hidden" name="id" id="id" value="' . DAO::UNKNOWN_ID . '">
            <input type="text" name="name" id="nom" value="" required>
            <input type="text" name="surname" id="prénom" value="" required>
            <input type="tel" name="tel" id="tél" value="" required>
            <input type="image" name="plus[]" src="img/plus.png" alt="Ajout"/>
        </p>
        </form>';
        ?>
        </table>
    </body>
</html>
