<?php
require_once "autoload.php";

// On doit recevoir id, nom, prénom, tél
// On donne la possibilité de les passer sur la ligne de commande pour les tests :
//      php create.php leId leNom lePrénom leTél
$data = [];
$data['id'] = $argv[1] ?? $_POST['id'] ?? "";
$data['nom'] = $argv[2] ?? $_POST['nom'] ?? "";
$data['prénom'] = $argv[3] ?? $_POST['prénom'] ?? "";
$data['tél'] = $argv[4] ?? $_POST['tél'] ?? "";

// Il faut au moins le id
if (empty($data['id'])) {
    echo "ERREUR : il faut fournir l'identifiant du contact";
} else if (empty($data['nom']) && empty($data['prénom'])) {
    echo "ERREUR : il faut au moins un nom ou un prénom";
} else {
    $contacts = new ContactsDAO(MaBD::getInstance());
    $maj = $contacts->getOne($data['id']);
    if ($maj == null) {
        echo "ERREUR : aucun contact n'a cet identifiant (", $data['id'], ")";
    } else {
        $maj->nom = $data['nom'];
        $maj->prénom = $data['prénom'];
        $maj->tél = $data['tél'];
        $res = $contacts->update($maj);
        // Afficher le feedback
        if ($res === 0)
            echo "ERREUR : ", $maj, " n'a pas été mis à jour";
        else
            // On renvoie le contact pour pouvoir afficher un feedback pertinent
            echo json_encode($maj);
    }
}
