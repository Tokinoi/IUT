<?php
// Classe pour l'accès à la table Contacts
// Avec illustration des différents types de requêtes PDO
class ContactsDAO extends DAO {

    // Récupération d'un objet Contact dont on donne l'identifiant
    public function getOne(int $id): Contact {
        // Ici on utilise une requête simple
        $stmt = $this->pdo->query("SELECT * FROM Contacts WHERE id='$id'");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return new Contact($row['id'], $row['nom'], $row['prénom'], $row['tél']);
    }

    // Récupération de tous les objets dans une table
    public function getAll(): array {
        $res = array();
        $stmt = $this->pdo->query("SELECT * FROM Contacts ORDER BY nom");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row)
            $res[] = new Contact($row['id'], $row['nom'], $row['prénom'], $row['tél']);
        return $res;
    }

    // Sauvegarde de l'objet $obj :
    //     $obj->id == UNKNOWN_ID ==> INSERT
    //     $obj->id != UNKNOWN_ID ==> UPDATE
    public function save(object $obj): int {
        if ($obj->id == DAO::UNKNOWN_ID) {
            // Requête préparée avec des paramètres anonymes (attention à l'ordre !)
            $stmt =  $this->pdo->prepare("INSERT INTO Contacts (nom, prénom, tél)"
                                        . " VALUES (?, ?, ?)");
            $res = $stmt->execute([$obj->nom, $obj->prénom, $obj->tél]);
            $obj->id = $this->pdo->lastInsertId();
        } else {
            // Requête préparée avec des paramètres nommés (on ne peut pas utiliser de caractères
            // accentués dans les noms de paramètre).
            $stmt = $this->pdo->prepare("UPDATE Contacts set nom=:nom, prénom=:prenom, "
                                        . " tél=:tel WHERE id=:id");
            $res = $stmt->execute(['id' => $obj->id, 'nom' => $obj->nom, 'prenom' => $obj->prénom, 'tel' => $obj->tél]);
        }
        return $res;
    }

    // Effacement de l'objet $obj (DELETE)
    public function delete(object $obj): int {
        // Requête préparée avec un bindParam
        // $stmt->bindParam(1, $id)
        $stmt = $this->pdo->prepare("DELETE FROM Contacts WHERE id = ?");
        $stmt->bindParam(1, $obj->id);
        return $stmt->execute();
    }
}
