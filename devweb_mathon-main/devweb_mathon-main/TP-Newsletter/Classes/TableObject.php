<?php 
/** Représentation d'un objet extrait d'une table de base de données
 * Les champs sont supposés créés correctement et définitivement par le constructeur (dans un tableau).
 * La liste des champs composant la clé primaire est donnée dans keyFieldsNames qui peut (doit) être redéfini dans
 * les classes filles.
 * Pour obtenir la valeur de la variable statique keyFieldsNames pour la classe dans laquelle est exécutée une
 * méthode, il faut utiliser static::, car avec self:: on otient la valeur pour la classe dans laquelle la fonction
 * est définie (TableObject) (voir late static binding dans la doc PHP).
 * Utilisation :
 *  - Pour chaque table MaTable de votre BD, définir une classe comme suit :
 *      class MaTable extends TableObject {
 *          static public $keyFieldsNames = array('clé_primaire_autoincrémentée');
 *          public $hasAutoIncrementedKey = true;
 *      }
 *      ou 
 *      class MaTable extends TableObject {
 *          static public $tableName = "Nom de la table dans la BD";
 *          static public $keyFieldsNames = array(liste_des_champs_de_la_clé_primaire);
 *          public $hasAutoIncrementedKey = false;
 *      }
 * On implémente JsonSerializable pour ne sérialiser que les champs de données si on souhaite produire du JSON.
 */
class TableObject implements JsonSerializable {

    // Statiques, car nécessaires avant de créer une instance (voir DAO::__construct)
    static public $tableName; // Nom de la table dans la base de données
    // Champs composant la clé primaire : tableau avec les noms des champs 
    static public $keyFieldsNames = array('id'); // par défaut un seul champ

    // La clé est-elle autoincrémentée ? (ne peut s'appliquer qu'à une clé unique)
    public $hasAutoIncrementedKey = true;

    // Liste des champs et leur valeur
    protected $fields = array();
    public function getAllFields(): array { return $this->fields; }
    public function jsonSerialize(): array { return $this->fields; }

    // Liste des valeurs des champs de la clé
    public function getKeyFieldsValues(): array {
        $res = array();
        foreach ($this->fields as $name => $value) 
            if (in_array($name, static::$keyFieldsNames)) $res[] = $value;
        return $res;
    }

    // Liste des valeurs des champs, sans les champs de la clé
    public function getFieldsValuesWithoutKey(): array {
        $res = array();
        foreach ($this->fields as $name => $value) 
            if (! in_array($name, static::$keyFieldsNames)) $res[] = $value;
        return $res;
    }

    // Liste des noms de champs, sans la clé, créée un bonne fois par le constructeur
    private $fieldsNamesWithoutKey = array();
    public function getFieldsNamesWithoutKey(): array { return $this->fieldsNamesWithoutKey; }

    // Copie des champs reçus (typiquement par un fetch sur la base)
    public function __construct(array $f) {
        $this->fields = $f;
        // Création de la liste des noms de champ sans la clé
        foreach ($this->fields as $name => $value) 
            if (! in_array($name, static::$keyFieldsNames))
                $this->fieldsNamesWithoutKey[] = $name;
    }

    // Affichage par défaut : champs séparés par un " | ", type CSV, en plus joli ?
    public function __tostring() { return implode(" | ", $this->fields); }

    // ------- setter et getter -------
    // Utilisation de array_key_exists car isset retourne false pour les valeurs NULL
    public function __get(string $field): mixed {
        if (array_key_exists($field, $this->fields))
            return $this->fields[$field];
        throw new Exception("Invalid field name $field in ". get_class($this));
    }

    public function __set(string $field, mixed $value): void {
        if (array_key_exists($field, $this->fields))
            $this->fields[$field] = $value;
        else 
            throw new Exception("Invalid field name $field in ". get_class($this));
    }

    public function __isset(string $field): bool {
        return array_key_exists($field, $this->fields);
    }
}

