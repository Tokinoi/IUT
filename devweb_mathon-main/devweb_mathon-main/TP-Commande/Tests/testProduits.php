<?php
require_once __DIR__ . "/../autoload.php";

function afficheTout(array $lesProduits): void {
    echo "------- Tous les produits :\n";
    foreach ($lesProduits as $p)
        echo $p, "\n";
}

$produits = new ProduitsDAO(MaBD::getInstance());
afficheTout($produits->getAll());

echo "------- Nouveau produit :\n";
$nouveau = new Produit(array('ref_pdt' => "CLIN", 'libelle' => "Gros rouge"));
echo "\t==> ", $nouveau, "\n";

echo "------- Le produit de code 'SAUSS' :\n";
echo "\t==> ", $produits->getOne('SAUSS'), "\n";

echo "------- Enregistrement du nouveau produit : \n";
$produits->insert($nouveau);
echo "\t==> ", $produits->getOne('CLIN'), "\n";

echo "------- Modification de $nouveau\n";
$nouveau->libelle = "Clinton rouge";
$produits->update($nouveau);
echo "\t==> ", $produits->getOne('CLIN'), "\n";

echo "------- Effacement de $nouveau\n";
$produits->delete($nouveau);

afficheTout($produits->getAll());

echo "------- Tous les produits du fournisseur 'CSNO'\n";
afficheTout($produits->getAllFournisseur('CSNO'));

