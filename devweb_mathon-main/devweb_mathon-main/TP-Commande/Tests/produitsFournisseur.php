<?php
require_once __DIR__ . "/../autoload.php";

// Affichage de tous les produits d'un fournisseur donné sur la ligne de commande

if (count($argv) != 2) {
    echo "\nUsage :\n\tphp produitsFournisseur.php code_frs\n\n";
    exit(1);
}

$code_frs = $argv[1];

$produits = new ProduitsDAO(MaBD::getInstance());
$lesProduitsDuFournisseur = $produits->getAllFournisseur($code_frs);

foreach ($lesProduitsDuFournisseur as $p) {
    echo $p->ref_pdt, " ", $p->libelle, " ", $p->pu_achat, "\n";
}

