<?php
class Inscrit extends TableObject
{
    static public $tableName = "Inscrits";
    static public $keyFieldsNames = ['login'];
    public $hasAutoIncrementedKey = false;

    static function tableHeader(): void
    {
        // TODO : à compléter
        echo "<tr>";
        echo "<th> login </th>";
        echo "<th> nom </th>";
        echo "<th> prénom </th>";
        echo "<th> mail </th>";
        echo "</tr>";
    }
    public function toTableRow(): void
    {
        // TODO : à compléter
        echo "<tr>";
        echo "<td>". $this->login. " </td>";
        echo "<td>". $this->nom. " </td>";
        echo "<td>". $this->prénom. " </td>";
        echo "<td>". $this->courriel. " </td>";
        echo "</tr>";
    }

    public function getRole(): string
    {
        return $this->rôle;
    }
    public function getName(): string
    {
        return $this->nom;

    }
    public function getSurname(): string
    {
        return $this->prénom;
    }
    public function getMail(): string
    {
        return $this->courriel;
    }
}

