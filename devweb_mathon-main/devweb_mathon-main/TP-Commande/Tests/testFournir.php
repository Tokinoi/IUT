<?php
require_once __DIR__ . "/../autoload.php";

function afficheTout(FournirDAO $fournit): void {
    echo "------- Toutes les associations Produit<->Fournisseur :\n";
    foreach ($fournit->getAll() as $p)
        echo $p, "\n";
    echo "---------------------------\n";
}

$nouveau = new Fournir(array('code_frs' => "AUCH", 'ref_pdt' => "PATE", 'pu_achat' => 3.5));
echo $nouveau, "\n";
$fournit = new FournirDAO(MaBD::getInstance());
echo $fournit->getOne(array('code_frs' => "CROUF", 'ref_pdt' => "PAIN")), "\n";
echo $fournit->getOne(array('code_frs' => "AUCH", 'ref_pdt' => "CHOC")), "\n";

afficheTout($fournit);

echo "Enregistrement de ";
$fournit->insert($nouveau);
echo $nouveau, "\n";

afficheTout($fournit);

echo "Modification de $nouveau\n";
$nouveau->pu_achat = 5.0;
$fournit->update($nouveau);
echo "\t==> $nouveau\n";

afficheTout($fournit);


echo "Effacement de $nouveau\n";
$fournit->delete($nouveau);

afficheTout($fournit);

