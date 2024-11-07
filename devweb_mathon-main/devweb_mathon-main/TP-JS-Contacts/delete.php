<?php
require_once "autoload.php";

// On doit recevoir le id du contact
// On donne la possibilité de le passer sur la ligne de commande pour les tests :
//      php create.php leId
$id = $argv[1] ?? $_POST['id'] ?? "";

if (empty($id)) {
    echo "ERREUR : il faut fournir l'identifiant du contact";
} else {
    // Charger le contact
    $contacts = new ContactsDAO(MaBD::getInstance());
    $supp = $contacts->getOne($id);
    if ($supp == null) {
        echo "ERREUR : aucun contact n'a cet identifiant ($id)";
    } else {
        // Effacer le contact de la base
        $res = $contacts->delete($supp);
        // Afficher le feedback
        if ($res === 0)
            echo "ERREUR : ", $supp, " n'a pas été effacé";
        else
            // On renvoie le contact pour pouvoir afficher un feedback pertinent
            echo json_encode($supp);
    }
}
