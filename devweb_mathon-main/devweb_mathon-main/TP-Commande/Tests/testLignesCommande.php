<?php
require_once __DIR__ . "/../autoload.php";

function afficheTout(array $lesLigneCommandes): void {
    echo "------- Toutes les lignes de commande :\n";
    foreach ($lesLigneCommandes as $p)
        echo $p, "\n";
}

$lignesCommande = new LignesCommandeDAO(MaBD::getInstance());
afficheTout($lignesCommande->getAll());

echo "------- Nouvelle ligne pour la commande 3 :\n";
$nouveau = new LigneCommande(array('numero_cde' => 3, 'ref_pdt' => "PATE", 'pu_cde' => 3.10, 'quantite' => 2));
echo "\t==> ", $nouveau, "\n";

echo "------- La ligne 'CHOC' de la commande 1 :\n";
echo "\t==> ", $lignesCommande->getOne(array(1, 'CHOC')), "\n";

echo "------- Enregistrement de la nouvelle ligne : \n";
$lignesCommande->insert($nouveau);
echo "\t==> ", $lignesCommande->getOne(array(3, 'PATE')), "\n";

echo "------- Modification de $nouveau\n";
$nouveau->quantite = 10;
$lignesCommande->update($nouveau);
echo "\t==> ", $lignesCommande->getOne(array(3, 'PATE')), "\n";

echo "------- Effacement de $nouveau\n";
$lignesCommande->delete($nouveau);

afficheTout($lignesCommande->getAll());

echo "------- Toutes les lignes de la commande 3 (avec le libellé du produit) :\n";
afficheTout($lignesCommande->getAllCommande(3));

