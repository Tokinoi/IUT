<?php
class Produit extends TableObject {

    // Affichage d'un produit dans une option de select avec 'selected="selected"' si l'objet
    // correspond à $selected
    public function viewSelectOption($selected): void {
        // TODO: À COMPLÉTER
        echo '<option value="id">libellé</option>', "\n";
    }
}

