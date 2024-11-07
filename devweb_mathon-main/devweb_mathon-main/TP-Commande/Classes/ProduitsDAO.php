<?php
// Classe pour l'accès à la table produit
class ProduitsDAO extends DAO {

    // Récupération d'un produit dont on donne l'identifiant
    public function getOne(string|int|array $ref): Produit {
        $stmt = $this->pdo->prepare("SELECT * FROM produit WHERE ref_pdt=?");
        $stmt->execute(array($ref));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return new Produit($row);
    }

    // Récupération de tous les produits dans la table
    public function getAll(): array {
        $res = array();
        $stmt = $this->pdo->query("SELECT * FROM produit ORDER BY ref_pdt");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row)
            $res[] = new Produit($row);
        return $res;
    }

    // Récupération de tous les produits d'un fournisseur donné
    // NB : on ajoute au produit le prix unitaire (pu_achat) récupéré dans la table 'fournir'.
    public function getAllFournisseur(string $code_frs): array {

        $res = array();
        $stmt = $this->pdo->prepare("SELECT * FROM produit JOIN fournir USING(ref_pdt) WHERE code_frs = ? ORDER BY ref_pdt");
        $arr = array($code_frs);
        $stmt->execute($arr);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
            $res[] = new Produit($row);
        }
        return $res;
    }

    // Ajout de l'objet dans la base
    public function insert(object $obj): int {
       $stmt =  $this->pdo->prepare("INSERT INTO produit (ref_pdt, libelle) VALUES (:ref_pdt, :libelle)");
       $res = $stmt->execute($obj->getFields());
       return $res;
    }

    // Mise à jour de l'objet dans la base
    public function update(object $obj): int {
       $stmt = $this->pdo->prepare("UPDATE produit SET libelle=:libelle WHERE ref_pdt=:ref_pdt");
       $res = $stmt->execute($obj->getFields());
       return $res;
    }

    // Effacement de l'objet $obj (DELETE)
    public function delete(object $obj): int {
        $stmt = $this->pdo->prepare("DELETE FROM produit WHERE ref_pdt=?");
        $res = $stmt->execute(array($obj->ref_pdt));
        return $res;
    }

}

