<?php
// Classe pour l'accès à la table commande
// Une commande présente peu d'intérêt sans la liste des lignes associées, on charge donc les lignes de commandes
// dans un champ supplémentaire 'lignes'
class CommandesDAO extends DAO
{

    // Récupération d'une commande dont on donne le numéro
    // On ajoute un champs 'lignes' contenant les lignes de commandes (tableau d'objets LigneCommande)
    public function getOne(string|int|array $num): Commande
    {
        if ($num == DAO::UNKNOWN_ID) {
            $stmt = $this->pdo->query("SELECT MAX(numero_cde) as id FROM  commande");
            $key = $stmt->fetch(PDO::FETCH_ASSOC);
            $num = $key['id'];
        }
        $stmt = $this->pdo->prepare("SELECT * FROM commande WHERE numero_cde=?");
        $stmt->execute(array($num));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return new Commande($row);
    }

    // Récupération de toutes les commandes de la table
    // On ajoute un champs 'lignes' contenant les lignes de commandes (tableau d'objets LigneCommande)
    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM commande ORDER BY numero_cde");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row)
            $res[] = new Commande($row);
        return $res;
    }

    // Ajout d'une commande dans la base, utilisation du numéro auto incrémenté
    public function insert(object $obj): int
    {
        if ($obj->numero_cde == DAO::UNKNOWN_ID) {
            $stmt = $this->pdo->query("SELECT MAX(numero_cde) as id FROM commande");
            $key = $stmt->fetch(PDO::FETCH_ASSOC);
            $obj->numero_cde = $key['id'] + 1;
        }
        $stmt =  $this->pdo->prepare("INSERT INTO commande (numero_cde, date_cde, code_frs) VALUES (?,?,?)");
        $res = $stmt->execute(array($obj->numero_cde, $obj->date_cde, $obj->code_frs));
        return $res;
    }

    // Mise à jour de l'objet dans la base
    public function update(object $obj): int
    {
        $stmt = $this->pdo->prepare("UPDATE commande SET date_cde=? and code_frs=? where numero_cde=?");
        $res = $stmt->execute(array($obj->date_cde, $obj->code_frs->numero_cde));
        return $res;
    }

    // Effacement d'une commande : effacer d'abord les lignes de commande, puis la commande elle-même
    public function delete(object $obj): int
    {
        $stmt =  $this->pdo->prepare("DELETE FROM commande where numero_cde=?");
        $res = $stmt->execute(array($obj->numero_cde));
        return $res;
    }
}
