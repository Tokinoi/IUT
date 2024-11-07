<?php
require_once "autoload.php";

// On doit recevoir nom, prénom, tél
// On donne la possibilité de les passer sur la ligne de commande pour les tests :
//      php create.php leNom lePrénom leTél
$data['id'] = DAO::UNKNOWN_ID;
$data['nom'] = $argv[1] ?? $_POST['nom'] ?? "";
$data['prénom'] = $argv[2] ?? $_POST['prénom'] ?? "";
$data['tél'] = $argv[3] ?? $_POST['tél'] ?? "";

// Pour insérer il faut au moins un nom ou un prénom
if (empty($data['nom']) && empty($data['prénom'])) {
    echo "ERREUR : il faut au moins un nom ou un prénom";
} else {
    // Créer un nouveau Contact avec les infos reçues
    $nouveau = new Contact($data); 
    // Ajouter le contact à la BD
    $contacts = new ContactsDAO(MaBD::getInstance());
    $res = $contacts->insert($nouveau);
    if ($res === 0)
        echo "ERREUR : ", $nouveau, " n'a pas été ajouté";
    else
        // On renvoie le contact créé pour pouvoir afficher un feedback pertinent
        echo json_encode($nouveau);
}
