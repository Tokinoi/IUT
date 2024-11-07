<?php
/** Classe pour l'accès aux données d'une base : constructeur d'objets
 * Hypothèses :
 *    Tous les objets manipulés ont la même structure :
 *       - un tableau associatif 'fields' contenant les champs de la table avec leur nom (comme le résultat
 *         d'un fetch sur la table)
 *       - un constructeur recevant un tel tableau en paramètre
 *       - un getter et un setter pour chaque champ
 *       - une méthode getFieldsNamesWithoutKey retournant la liste des noms des champs sans la clé (pour INSERT)
 *       - un attribut statique keyFieldsNames contenant la liste des noms des champs de la clé (pour INSERT)
 *       - une méthode getKeyFieldsValues retournant la liste des valeurs des champs de la clé (pour
 *         INSERT et UPDATE)
 * Le champ class doit être redéfini dans les classes filles, puisque le seul intérêt de faire des classes
 * filles est d'ajouter des méthodes qui seront en principe propre à une table donnée.
 * NB : un PreparedStatement ne fonctionne pas avec des noms de champ contenant des caractères UTF-8, d'où
 *      l'utilisation exclusive de variables positionnelles.
 */

class DAO {
    const UNKNOWN_ID = -1; // Identifiant non déterminé (pour les clés autoincrémentées)
    protected $pdo; // Objet pdo pour l'accès à la table
    protected $table; // Nom de la table dans la base, obtenue à partir de la classe des objets

    // À redéfinir obligatoirement dans les sous-classes
    protected $class; // Nom de la classe dont on veut produire des instances

    // Liste des noms de champ de la clé et clause WHERE avec ces champs
    protected $keyNames;
    protected $keyWhereClause;

    // Construction de la clause WHERE pour la clé primaire (pour getOne, delete et update)
    protected function buildKeyWhereClause() {
        $clause = " WHERE " . $this->keyNames[0] . "=?";
        for ($i = 1; $i < count($this->keyNames); $i++)
            $clause = $clause . " AND " . $this->keyNames[$i] . "=?";
        return $clause;
    }

    // Le constructeur reçoit l'objet PDO contenant la connexion et le nom de la classe des objets à créer
    // Si la classe n'est pas fournie, on suppose que l'attribut class a été défini (cas d'une classe fille)
    public function __construct(PDO $connector, $className = null) {
        $this->pdo = $connector;
        if ($className !== null) {
            $this->class = $className;
        }
        // NB : le :: ne fonctionne qu'avec une variable et pour PHP >= 5.3
        $class = $this->class;
        // Le nom de la table de la BD fournissant les objets
        $this->table = $class::$tableName;
        $this->keyNames = $class::$keyFieldsNames;
        $this->keyWhereClause = $this->buildKeyWhereClause();
    } 

    // Requêtes préparées pour getOne, delete, insert, update. On ne les prépare qu'une fois.
    private $stmtGetOne = null;
    private $stmtDelete = null;
    private $stmtInsert = null;
    private $stmtUpdate = null;

    // Construction de la requête permettant d'obtenir un objet de la base. Placé dans une fonction séparée
    // pour qu'on puisse la redéfinir dans les sous-classes
    protected function buildStmtGetOne() {
        $req = "SELECT * FROM $this->table" . $this->keyWhereClause;
        return $this->pdo->prepare($req);
    }

    // Récupération d'un objet dont on donne la clé (tableau des valeurs composant la clé ou simplement la 
    // valeur si la clé n'est pas composée)
    // Retourne NULL si l'objet n'est pas dans la base.
    public function getOne($key) {
        if ($this->stmtGetOne == null) {
            // Construction de la requête
            $this->stmtGetOne = $this->buildStmtGetOne();
        }
        if (is_array($key))
            $this->stmtGetOne->execute($key);
        else
            $this->stmtGetOne->execute(array($key));

        $row = $this->stmtGetOne->fetch(PDO::FETCH_ASSOC);
        return ($row !== false)?new $this->class($row):null;
    }

    // Construction d'un tableau d'objets à partir d'un statement supposé contenir le résultat d'une requête,
    // donc après un query, ou un execute sur une requête préparée
    // Pas d'intérêt réel ici, mais très pratique dans les classes dérivées, ou on écrit fréquemment des getAll
    // améliorés qui vont utiliser cette fonction ou la rédéfinir
    protected function toObjectArray($stmt) {
        $res = array();
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row)
            $res[] = new $this->class($row);
        return $res;
    }

    // Récupération de tous les objets dans une table (peut être vide)
    // $complementRequete contient une chaîne ajoutée à la requête, par exemple :
    //      ORDER BY champ;
    //      WHERE truc=? and machin=?, [chose, bidule];
    public function getAll($complementRequete = "", $paramComplement = []) {
        $stmt = $this->pdo->prepare("SELECT * FROM $this->table $complementRequete");
        $stmt->execute($paramComplement);
        return $this->toObjectArray($stmt);
    }

    // Construction d'un Iterator pour récupérer ligne par ligne et sous forme d'objet les
    // lignes de la table.
    // $complementRequete contient une chaîne ajoutée à la requête, par exemple :
    //      ORDER BY champ;
    //      WHERE champ = 'valeur';
    public function getIterator($complementRequete = "") {
        $stmt = $this->pdo->prepare("SELECT * FROM $this->table $complementRequete");
        return new DAOIterator($stmt, $this->class);
    }

    // Insertion de l'objet
    public function insert($obj) {
        if ($this->stmtInsert == null) {
            $this->stmtInsert = $this->newInsertStatement($obj);
        }
        if ($obj->hasAutoIncrementedKey) {
            $res = $this->stmtInsert->execute($obj->getFieldsValuesWithoutKey());
            // Autoincrémentée : un seul champ dans la clé
            $id = $this->keyNames[0];
            $obj->$id = $this->pdo->lastInsertId();
        } else {
            $fields = array_merge($obj->getKeyFieldsValues(), $obj->getFieldsValuesWithoutKey());
            $res = $this->stmtInsert->execute($fields);
        }
        return $res;
    }

    // Mise à jour de l'objet, impossible s'il n'y a pas au moins un champ en plus de la clé
    public function update($obj) {
        if (count($obj->getFieldsNamesWithoutKey()) == 0)
            throw new Exception("Invalid field list in $this->class update (empty)");

        if ($this->stmtUpdate == null) {
            $this->stmtUpdate = $this->newUpdateStatement($obj);
        }
        $fields = array_merge($obj->getFieldsValuesWithoutKey(), $obj->getKeyFieldsValues());
        return $this->stmtUpdate->execute($fields);
    }

    // Effacement de l'objet $obj (DELETE)
    public function delete($obj) {
        if ($this->stmtDelete == null) {
            // Construction de la requête
            $req = "DELETE FROM $this->table" . $this->keyWhereClause;
            $this->stmtDelete = $this->pdo->prepare($req);
        }
        return $this->stmtDelete->execute($obj->getKeyFieldsValues());
    }

    // Construction du PreparedStatement pour l'insertion, 2 cas :
    //    - clé autoincrémentée : pas insérée
    //    - sinon on met tous les champs
    private function newInsertStatement($obj) {
        $fieldList = "";
        $valueList = "";
        if (! $obj->hasAutoIncrementedKey) { // on met aussi la clé
            foreach ($this->keyNames as $col) {
                $fieldList = $fieldList . "$col, ";
                $valueList = $valueList . "?, ";
            }
        }
        foreach ($obj->getFieldsNamesWithoutKey() as $col) {
            $fieldList = $fieldList . "$col, ";
            $valueList = $valueList . "?, ";
        }
        // On enlève la dernière virgule dans les listes
        $fieldList = substr($fieldList, 0, -2);
        $valueList = substr($valueList, 0, -2);
        $req = "INSERT INTO $this->table ($fieldList) VALUES ($valueList)";
        return $this->pdo->prepare($req); 
    }

    // Construction du PreparedStatement pour la mise à jour, tous les champs sauf la clé
    private function newUpdateStatement($obj) {
        $fieldList = "";
        foreach ($obj->getFieldsNamesWithoutKey() as $col) {
            $fieldList = $fieldList . "$col = ?, ";
        }
        // On enlève la dernière virgule dans la liste
        $fieldList = substr($fieldList, 0, -2);
        $req = "UPDATE $this->table SET $fieldList" . $this->keyWhereClause;
        return $this->pdo->prepare($req); 
    }
}


/*
 * Classe pour itérer ligne par ligne sur les résultats d'un requête.
 */

class DAOIterator Implements Iterator {
    private $index; // Rang dans la liste itérée
    private $stmt;  // PreparedStatement à utiliser 
    private $className;  // Nom de la classe des objets à produire
    private $courant; // Élément courant de l'itération

    // Reçoit un PDOStatement, initialisé par prepare, et le nom de la classe des objets à produire
    public function __construct(PDOStatement $stmt, $className) {
        $this->stmt = $stmt;
        $this->className = $className;
        $this->init();
    }

    private function init() {
        $this->stmt->execute();
        $this->index = -1;
        $this->next();
    }

    public function current() { return $this->courant; }

    public function key() { return $this->index; }

    public function next() {
        $row = $this->stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            $this->courant = null;
            $this->index = -1;
        } else {
            $this->courant = new $this->className($row);
            $this->index = $this->index + 1;
        }
    }

    public function rewind() {
        $this->stmt->closeCursor();
        $this->init();
    }

    public function valid() { return ($this->courant !== null); }
}
?>
