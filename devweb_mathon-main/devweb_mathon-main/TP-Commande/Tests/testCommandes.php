<?php
require_once __DIR__ . "/../autoload.php";

function afficheTout(CommandesDAO $commandes): void {
    echo "------- Toutes les commandes :\n";
    foreach ($commandes->getAll() as $c)
        $c->display();
}

$commandes = new CommandesDAO(MaBD::getInstance());
afficheTout($commandes);

echo "------- Nouvelle commande :\n";
$now = new DateTime();
$date = $now->format('Y-m-d');
$nouveau = new Commande(array('numero_cde' => DAO::UNKNOWN_ID, 'date_cde' => $date, 'code_frs' => "CSNO",
                              'lignes' => array()));
echo "\t==> ", $nouveau, "\n";

echo "------- Enregistrement de $nouveau : \n";
$commandes->insert($nouveau);
echo "\t==> ", $commandes->getOne($nouveau->numero_cde), "\n";

echo "------- Ajout d'une ligne à la nouvelle commande :\n";
$lignes = new LignesCommandeDAO(MaBD::getInstance());
$nouvLigne = new LigneCommande(array('numero_cde' => $nouveau->numero_cde, 'ref_pdt' => "SAUSS",
                                     'pu_cde' => 4.50, 'quantite' => 4));
// Ajout à l'objet en mémoire
$nouveau->addLigne($nouvLigne);
// Insertion dans la base
$lignes->insert($nouvLigne);
$c = $commandes->getOne($nouveau->numero_cde);
echo "\t==> ", $c->display();

echo "------- Modification de $nouveau\n";
$nouveau->date_cde = "2013-07-31";
$commandes->update($nouveau);
echo "\t==> ", $commandes->getOne($nouveau->numero_cde), "\n";

echo "------- Effacement de $nouveau\n";
$commandes->delete($nouveau);

afficheTout($commandes);

