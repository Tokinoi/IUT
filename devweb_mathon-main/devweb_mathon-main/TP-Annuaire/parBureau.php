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
    $annuairetrier=tri($annuaireInterne);
    foreach ($annuairetrier  as $personne){
        echo "<tr>";
        uneLigneHTML($personne);
        echo "</tr>";
    }
    echo "</table>";

    function tri(array $annuraire){
        //Fonctionne sauf pour "E011" -> Fonctionnait mais avec " E011"
        for ($j=1;$j<=count($annuraire)-1;$j++){
            $X = $annuraire[$j];
            $i=$j-1;
            while($i>=0 and $X['bureau'] < $annuraire[$i]['bureau']){
                $annuraire[$i+1]=$annuraire[$i];
                $i-=1;
            }
            $annuraire[$i+1] = $X;
        }
        return($annuraire);

    }

    