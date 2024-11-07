<?php
require_once "autoload.php";

// On donne la possibilité de tester en ligne de commande avec $argv[1]
// Utilisation de l'opérateur ?? de PHP
//      $id = $argv[1] ?? "";
// est équivalent à : 
//      if (isset($argv[1]))
//          $id = $argv[1];
//      else
//          $id = "";
$id = $argv[1] ?? "";
if (empty($id)) // pas d'argument, on prend le paramètre GET
    $id = $_GET['id'] ?? "";

$contacts = new ContactsDAO(MaBD::getInstance());
if (empty($id)) { // ni argument, ni paramètre GET, on affiche tous les contacts
    echo json_encode($contacts->getAll());
} else {
    // Afficher le contact $id au format JSON
    $leContact = $contacts->getOne($id);
    if ($leContact == null)
        echo "ERREUR : aucun contact n'a cet identifiant ($id)";
    else
        echo json_encode($leContact);
}
