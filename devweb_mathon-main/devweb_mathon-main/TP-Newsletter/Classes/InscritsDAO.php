<?php
// Classe pour l'accès à la table Inscrits
class InscritsDAO extends DAO {
    protected $class = "Inscrit";

    // Récupération d'un inscrit dont on donne le code de validation, retourne null si code invalide
    public function getOneFromCode(string $validation): ?Inscrit {
        $stmt = $this->pdo->prepare("SELECT * FROM $this->table WHERE validation=?");
        $stmt->execute([ $validation ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row)
           return new $this->class($row);
        return null;
    }

    // Authentification, retourne l'objet Inscrit ou null
    public function checkAuthentification(string $login, string $mdp): ?Inscrit {
        $stmt = $this->pdo->prepare("SELECT * FROM $this->table WHERE login=? AND mdp=?");
        $stmt->execute(array($login, $mdp));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row)
           return new $this->class($row);
        return null;
    }

    public function getAllInscrit(){
        $stmt = $this->pdo->prepare('SELECT * FROM Inscrits where validation = ""');
        $stmt->execute();
        return $this->toObjectArray($stmt);
        }
    
}
