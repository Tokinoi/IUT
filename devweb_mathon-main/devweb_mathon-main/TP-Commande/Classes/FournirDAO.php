<?php
/* Classe pour l'accès à la table fournir
 * Association : on charge donc les champs de la table mais aussi les libellés des fournisseurs
 * et produits
 */
class FournirDAO extends DAO {

    // Récupération d'un 'fournir' dont on donne la clé array(code_frs, ref_pdt)
    public function getOne(string|int|array $key): Fournir {
        $stmt = $this->pdo->prepare("SELECT fournir.code_frs, nom, fournir.ref_pdt, libelle, pu_achat"
            . " FROM fournir, fournisseur, produit "
            . " WHERE fournir.code_frs=fournisseur.code_frs AND fournir.ref_pdt=produit.ref_pdt"
            . " AND fournir.code_frs=:code_frs AND fournir.ref_pdt=:ref_pdt");
        $stmt->execute($key);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return new Fournir($row);
    }

    // Récupération de tous les objets dans une table
    public function getAll(): array {
        $res = array();
        $stmt = $this->pdo->query("SELECT fournir.code_frs, nom, fournir.ref_pdt, libelle, pu_achat"
            . " FROM fournir, fournisseur, produit "
            . " WHERE fournir.code_frs=fournisseur.code_frs AND fournir.ref_pdt=produit.ref_pdt"
            . " ORDER BY nom");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row)
            $res[] = new Fournir($row);
        return $res;
    }

    // Ajout de l'objet dans la base
    public function insert(object $obj): int {
        $stmt =  $this->pdo->prepare("INSERT INTO fournir (code_frs, ref_pdt, pu_achat)"
                                     . " VALUES (:code_frs, :ref_pdt, :pu_achat)");
       $res = $stmt->execute($obj->getFields());
       return $res;
    }

    // Mise à jour de l'objet dans la base
    public function update(object $obj): int {
        $stmt = $this->pdo->prepare("UPDATE fournir SET pu_achat=:pu_achat"
                                    . " WHERE code_frs=:code_frs AND ref_pdt=:ref_pdt");
       $res = $stmt->execute($obj->getFields());
       return $res;
    }

    // Effacement de l'objet $obj (DELETE)
    public function delete(object $obj): int {
        $stmt = $this->pdo->prepare("DELETE FROM fournir WHERE code_frs=? AND ref_pdt=?");
        $res = $stmt->execute(array($obj->code_frs, $obj->ref_pdt));
        return $res;
    }
}

