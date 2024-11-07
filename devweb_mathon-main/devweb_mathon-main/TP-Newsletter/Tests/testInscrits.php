<?php

require_once __DIR__ . "/../autoload.php";

function afficheTout(InscritsDAO $inscrits): void {
    echo "\n------- Tous les inscrits :\n";
    foreach ($inscrits->getAll() as $c)
        echo "\t", $c, "\n";
    echo "---------------------------\n";
}

$inscrits = new InscritsDAO(MaBD::getInstance());
afficheTout($inscrits);

echo "\n------- Création d'un nouvel inscrit :\n";
$moi = new Inscrit([ 'login' => "lovelace", 'nom' => "LOVELACE", 'prénom' => "Ada", 'mdp' => "lovelace",
    'courriel' => "Ada.Lovelace@univ-grenoble-alpes.fr",
    'validation' => "", 'rôle' => "ROLE_UTILISATEUR" ]);
echo $moi, "\n";
echo "Enregistrement de $moi->login";
$inscrits->insert($moi);
echo $moi, "\n";

echo "\n------- Récupération d'un inscrit dans la base :\n";
$login = "genthial";
$inscrit = $inscrits->getOne($login);
if ($inscrit != null) {
    echo "Lu dans la base : \n\t$inscrit\n";
} else {
    echo "Pas d'inscrit avec cet identifiant ($login)\n";
}

echo "\n------- Modification d'un inscrit (ajout d'un code de validation):\n";
echo "Modification de $moi->login\n";
$moi->validation = uniqid();
$inscrits->update($moi);
echo "\t==> $moi\n";

echo "\n------- Suppression d'un inscrit :\n";
echo "Effacement de $moi\n";
$inscrits->delete($moi);

afficheTout($inscrits);

