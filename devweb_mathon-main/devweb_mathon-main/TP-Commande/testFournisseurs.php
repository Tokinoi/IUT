<?php
require_once __DIR__ . "/autoload.php";

function afficheTout(FournisseursDAO $fournisseurs): void {
    echo "------- Tous les fournisseurs :\n";
    foreach ($fournisseurs->getAll() as $f)
        echo $f, "\n";
}

$fournisseurs = new FournisseursDAO(MaBD::getInstance());
afficheTout($fournisseurs);

echo "------- Ajout d'un fournisseur :\n";
$nouveau = new Fournisseur(array('code_frs' => "INTER", 'nom' => "Intermarché"));
echo "Nouveau fournisseur : ", $nouveau, "\n";

echo "------- Récupération du fournisseur de code 'CSNO' :\n";
echo "\t==>", $fournisseurs->getOne('CSNO'), "\n";

echo "------- Enregistrement de $nouveau :\n";
$fournisseurs->insert($nouveau);
echo "\t==>", $fournisseurs->getOne('INTER'), "\n";

echo "------- Modification de $nouveau :\n";
$nouveau->nom = "Mousquetaires";
$fournisseurs->update($nouveau);
echo "\t==>", $fournisseurs->getOne('INTER'), "\n";

echo "------- Effacement de $nouveau\n";
$fournisseurs->delete($nouveau);

afficheTout($fournisseurs);

