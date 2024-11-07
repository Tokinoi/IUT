<?php
// Classe de connexion à une base de données
// S'inspire du pattern singleton pour n'ouvrir qu'une seule connexion
// Utilisation :
//    $bd = MaBD::getInstance(); // $bd est un objet PDO
class MaBD
{

   static private $pdo = null; // Le singleton

   // Obenir le singleton, génère une PDOException en cas d'erreur
   static function getInstance(): ?PDO
   {
      if (self::$pdo == null) {
         $dsn = "mysql:host=gigondas;dbname=mathona;charset=utf8";
         self::$pdo = new PDO($dsn, "mathona", "SuperLicornedu26");
      }
      return self::$pdo;
   }
}
