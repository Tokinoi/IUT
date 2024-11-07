<?php
/* Classe abstraite pour l'accès aux données d'une base
 */
abstract class DAO {
    const UNKNOWN_ID = -1; // Identifiant non déterminé
    protected $pdo; // Objet pdo pour l'accès à la table

    // Le constructeur reçoit l'objet PDO contenant la connexion
    public function __construct(PDO $connector) { $this->pdo = $connector; }

    // Récupération d'un objet dont on donne l'identifiant
    abstract public function getOne(string|int|array $id): object;

    // Récupération de tous les objets dans une table
    abstract public function getAll(): array;

    // Ajout de l'objet dans la base
    abstract public function insert(object $obj): int;

    // Mise à jour de l'objet dans la base
    abstract public function update(object $obj): int;

    // Effacement de l'objet $obj (DELETE)
    abstract public function delete(object $obj): int;
}
