<?php
// Classe pour l'accès à la table ligne_commande
class LignesCommandeDAO extends DAO {

    // Récupération d'une ligne_commande dont on donne la clé (array(numero_cde, ref_pdt))
    public function getOne(string|int|array $key): LigneCommande {
        $stmt = $this->pdo->prepare("SELECT * FROM ligne_commande WHERE numero_cde=? and ref_pdt=?");
        $stmt->execute($key);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return new LigneCommande($row);
    }

    // Récupération de tous les ligne_commandes dans la table
    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM ligne_commande ORDER BY numero_cde");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row)
            $res[] = new LigneCommande($row);
        return $res;
    }

    // Récupération de tous les ligne_commandes d'une commande donnée
    // Ici on ajoute le libellé du produit pour pouvoir l'afficher facilement
    public function getAllCommande(int $numero_cde): array {
        $stmt = $this->pdo->prepare("SELECT * FROM ligne_commande WHERE numero_cde=? ORDER BY numero_cde");
        $all= $stmt->execute(array($numero_cde));
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
            $res[] = new LigneCOmmande($row);
        }
        return $res;
    }

    // Ajout de l'objet dans la base
    public function insert(object $obj): int {
        $stmt =  $this->pdo->prepare("INSERT INTO ligne_commande (numero_cde, ref_pdt, pu_cde, quantite) VALUES (?,?,?,?)");
        $res = $stmt->execute(array($obj->numero_cde,$obj->ref_pdt,$obj->pu_cde,$obj->quantite));
        return $res;
    }

    // Mise à jour de l'objet dans la base
    public function update(object $obj): int {
        $stmt = $this->pdo->prepare("UPDATE ligne_commande SET quantite=? where ref_pdt=? and numero_cde=?");
        $res = $stmt->execute(array($obj->quantite,$obj->ref_pdt,$obj->numero_cde));
        return $res;
    }

    // Effacement de l'objet $obj (DELETE)
    public function delete(object $obj): int {
        $stmt =  $this->pdo->prepare("DELETE FROM ligne_commande where numero_cde=? and ref_pdt=?");
        $res = $stmt->execute(array($obj->numero_cde,$obj->ref_pdt));
        return $res;
    }
}
