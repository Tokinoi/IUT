<?php
// Représentation d'un contact
class Contact extends TableObject {
    static public $tableName = "Contacts";
    static public $keyFieldsNames = array('id');
    public $hasAutoIncrementedKey = true;

    public function __toString() {
        return $this->nom . " " . $this->prénom . " - " . $this->tél;
    }
}
?>
