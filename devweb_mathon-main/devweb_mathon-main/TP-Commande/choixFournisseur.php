<?php
// Autochargement des classes
require_once __DIR__ . "/autoload.php";

$dao =  new FournisseursDAO(MaBD::getInstance());

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <link rel="stylesheet" type="text/css" href="commande.css"/>
    <title>Choix du fournisseur</title>
</head>
<body>
   <h1>Choix du fournisseur pour l'ajout d'une commande</h1>
   <form action="ajoutCommande.php" method="POST">
    <select name="fournisseur" onchange="submit()">
            <option value="">--Choisir un fournisseur--</option>
            <?php 
            foreach ($dao->getAll() as $fournisseur) {
                $fournisseur->getOption();
            }
            ?>
    </select>
   </form>
</body> 
</html>
