<?php
// Autochargement des classes
require_once __DIR__ . "/autoload.php";

// TODO : On s'assure qu'on a bien un fournisseur sélectionné, sinon renvoi vers
// choixFournisseur.php
if (!isset($_POST['fournisseur'])){
    header("Location: choixFournisseur.php");
}

// TODO : On charge les produits du fournisseur sélectionné pour le select
$dao =  new ProduitsDAO(MaBD::getInstance());
$lesProduits = $dao->getAllFournisseur($_POST['fournisseur']);


//$dao =  new ProduitsDAO(MaBD::getInstance());
//$lesProduits = $dao.getAllFournisseur($_GET['fournisseur']);


// TODO : Traitement du formulaire de saisie d'une ligne

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="StyleSheet" type="text/css" href="commande.css"/>
    <title>Création d'une commande</title>
</head>
<body>
<h1>Création d'une commande</h1>
<?php // Pour développement et/ou debug
echo '<pre>';
print_r($lesProduits);
echo '</pre>';
?>

<!-- TODO : affichage du formulaire de saisie d'une ligne -->

<!-- TODO : affichage du récapitulatif de la commande -->
</body>
</html>
