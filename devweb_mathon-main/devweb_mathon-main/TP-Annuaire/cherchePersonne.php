<?php

include_once "annuaire.php";
    echo '<link href="annuaire.css" rel="stylesheet">';
    echo "<h1>";
    echo "Annuaire interne de l'IUT de Valence";
    echo "</h1>";    
    echo "<table>";
/*  //Partie 3

    $entete = ['Nom' => "NOM",'Prenom' => "PRENOM",'Poste' => "POSTE", 'Bureau' => "Bureaux", 'Fonction' => "Fonction" ];
    $achercher = "JUBAN";
    $indice = cherche($annuaireInterne,$achercher);
    if ($indice != -1){
        echo "<tr>";
        uneLigneHTML($entete);
        echo "</tr>";
        echo "<tr>";
        uneLigneHTML($annuaireInterne[$indice]);
        echo "</tr>";
        echo "</table>";
    }
    else
        echo "Personne non trouvé";

    function cherche(array $tabAnnuaire, string $nom)
    {
        $indice = -1; // Je commence à -1 car le premier élément regarder et le 0
        foreach ($tabAnnuaire as $personne){
            $indice+=1;
            if ($personne['nom']== $nom ){
                return $indice;
            } 
        }
        return -1;
    }

*/
    //Partie 6

    $entete = ['Nom' => "NOM",'Prenom' => "PRENOM",'Poste' => "POSTE", 'Bureau' => "Bureaux", 'Fonction' => "Fonction" ];
    $achercher = "GE";
    $indice = cherche_plus($annuaireInterne,$achercher);
    if ($indice != NULL){
        echo "<tr>";
        uneLigneHTML($entete);
        echo "</tr>";
        foreach($indice as $personne){
            echo "<tr>";
            uneLigneHTML($personne);
            echo "</tr>";
        }
        echo "</table>";
    }
    else
        echo "Personne non trouvé";

    function cherche_plus(array $tabAnnuaire, string $nom)
    {
        $valid = [];
        //Oui j'ai copié coller.
        //On ajoute une ligne personne pour initialisé le tableau
        foreach ($tabAnnuaire as $personne){
            if (strpos($personne['nom'], $nom) === 0 ){
                array_push($valid,$personne);
            }
        }

        return $valid; 
        // ON retire la première valeur ajouter pour initialisé le tableau
    }