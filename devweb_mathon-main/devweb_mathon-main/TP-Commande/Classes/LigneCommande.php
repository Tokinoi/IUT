<?php
class LigneCommande extends TableObject {

    // Affichage dans une ligne d'une table HTML
    // Suppose que la ligne de commande contient le libellé du produit
    function viewTableRow(): void {
        echo '<tr>';
        echo '<td>'; 
        echo $this->numero_cde;
        echo '</td>';
        echo '<td>'; 
        echo $this->ref_pdt;
        echo '</td>';
        echo '<td>'; 
        echo $this->pu_cde;
        echo '</td>';
        echo '<td>'; 
        echo $this->quantite;
        echo '</td>';
        echo '</tr>';
    }

}

