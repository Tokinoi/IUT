<?php
class Fournir extends TableObject {
    // Pour ne retourner que les champs de la table et pas les libellés fournisseur et produit
    // ajoutés à la création de l'objet (voir FournirDAO.php)
    public function getFields(): array {
        return [
            'code_frs' => $this->code_frs,
            'ref_pdt' => $this->ref_pdt,
            'pu_achat' => $this->pu_achat,
        ];
    }
}

