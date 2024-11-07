<?php
// Autochargement des classes
require_once "autoload.php";

function afficheTout(ContactsDAO $contacts): void {
    echo "------- Tous les contacts :\n";
    foreach ($contacts->getAll() as $c)
        echo $c, "\n";
    echo "---------------------------\n";
}

// Création d'un objet Contact avec le constructeur
$moi = new Contact(DAO::UNKNOWN_ID, "GENTHIAL", "Damien", "04 75 99 99 99");
// Affichage (appelle implicitement la méthode __toString de la classe Contact)
echo $moi, "\n";

// Création d'un objet ContactsDAO pour créer des objets Contact par extraction depuis
// la base de données (configuration de la base de données dans Classes/MaBD.php)
$contacts = new ContactsDAO(MaBD::getInstance());

// Extraction de deux contacts
echo $contacts->getOne(1), "\n";
echo $contacts->getOne(2), "\n";

// Affichage de tous les contacts avec la méthode getAll(), voir afficheTout
afficheTout($contacts);

// La méthode save de ContactsDAO utilise le champ id du contact pour déterminer
// si c'est un nouveau (id == DAO::UNKNOWN_ID --> INSERT) ou un contact
// déjà existant (id == X --> UPDATE)
echo "Enregistrement de ";
$contacts->save($moi);
echo $moi, "\n";

afficheTout($contacts);

echo "Modification de $moi\n";
$moi->tél = "04 75 41 88 12";
$contacts->save($moi);
echo "\t==> $moi\n";

afficheTout($contacts);

echo "Effacement de $moi\n";
$contacts->delete($moi);

afficheTout($contacts);
