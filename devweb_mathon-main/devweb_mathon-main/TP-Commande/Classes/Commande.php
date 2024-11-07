<?php
class Commande extends TableObject {

    // Ajout d'une ligne de commande dans le champ 'lignes' de la commande
    public function addLigne(LigneCommande $l): void {
        $this->lignes[] = $l;
    }

    public function __construct(array $f) { 
        $f['lignes']=array();
        $this->fields = $f; 
    }

    // Redéfinition de __tostring pour ne pas afficher les lignes de la commande
    public function __tostring() {
        return "Commande $this->numero_cde du $this->date_cde chez $this->code_frs";
    }

    // Affichage de la commande avec toutes les lignes de commande
    public function display(): void {
        echo $this;
        echo "<tr>";
        foreach($this->lignes as $row){
            $row.viewTableRow();
        }
        echo "</tr>";
        echo "\n";
    }

    // Calcul du prix total de la commande
    public function prixTotal(): float {
        $res =0;
        foreach($this->ligne as $row){
            $res += $row->pu_cde * $row->quantite;
        }
        return $res;
    }

    // Affichage en HTML dans un H2 (avec nom du fournisseur, et date au format JJ/MM/AAAA)
    public function viewH2(): void {
        $fournisseurDao = new FournisseurDAO();
        $fournisseur = $fournisseurDao.getOne($this->code_frs);
        $dt = DateTime::createFromFormat('Y-m-d', $this->code_frs);
        $date = $dt->format('d-m-Y');
        echo '<h2>Commande '. $this->numero_cde . ' du '.$date.'chez Fournisseur</h2>';
    }
    
    }

