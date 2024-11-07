<?php
// Classe pour l'accès à la table fournisseur
class FournisseursDAO extends DAO {

    // Récupération d'un fournisseur dont on donne l'identifiant
    public function getOne(string|int|array $ref): Fournisseur {
        $stmt = $this->pdo->prepare("SELECT * FROM fournisseur WHERE code_frs=?");
        $stmt->execute(array($ref));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return new Fournisseur($row);
    }

    // Récupération de tous les objets dans une table
    public function getAll(): array {
        $res = array();
        $stmt = $this->pdo->query("SELECT * FROM fournisseur ORDER BY code_frs");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row)
            $res[] = new Fournisseur($row);
        return $res;
    }

    // Ajout de l'objet dans la base
    public function insert(object $obj): int {
       $stmt =  $this->pdo->prepare("INSERT INTO fournisseur (code_frs, nom) VALUES (:code_frs, :nom)");
       $res = $stmt->execute($obj->getFields());
       return $res;
    }

    // Mise à jour de l'objet dans la base
    public function update(object $obj): int {
       $stmt = $this->pdo->prepare("UPDATE fournisseur SET nom=:nom WHERE code_frs=:code_frs");
       $res = $stmt->execute($obj->getFields());
       return $res;
    }

    // Effacement de l'objet $obj (DELETE)
    public function delete(object $obj): int {
        $stmt = $this->pdo->prepare("DELETE FROM fournisseur WHERE code_frs=?");
        $res = $stmt->execute(array($obj->code_frs));
        return $res;
    }
}

