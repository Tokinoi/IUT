<?php

include_once "annuaire.php";
    echo '<link href="annuaire.css" rel="stylesheet">';
    echo "<h1>";
    echo "Annuaire interne de l'IUT de Valence";
    echo "</h1>";    
    echo "<table>";

    $entete = ['Nom' => "NOM",'Prenom' => "PRENOM",'Poste' => "POSTE", 'Bureau' => "Bureaux", 'Fonction' => "Fonction" ];

    echo "<tr>";
    uneLigneHTML($entete);
    echo "</tr>";
    foreach ($annuaireInterne  as $personne){
        echo "<tr>";
        uneLigneHTML($personne);
        echo "</tr>";
    }
    echo "</table>";

    