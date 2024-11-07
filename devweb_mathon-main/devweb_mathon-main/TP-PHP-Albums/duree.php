<?php
// Gestion des durées pour les albums. Une durée est un tableau associatif au format :
//    [ 'h' => heures, 'm' => minutes, 's' => secondes ]

// Reçoit une chaîne de type hh:mm:ss ou mm:ss et retourne une durée :
//  tableau [ 'h' => hh, 'm' => mm, 's' => ss]
// Indication : utiliser la fonction explode pour décomposer $strDuration
function fromString(string $strDuration): array {
    $res = [ 'h' => 0, 'm' => 0, 's' => 0 ];
    // TODO :
    $arr = explode(":", $strDuration);
    // On test si $strDuration est de 5 pour vérifier si l'on à les heures. 
    if (strlen($strDuration) == 5){
        $res['m'] = $arr[0];
        $res['s'] = $arr[1];
    }
    else{
        $res['h'] = $arr[0];
        $res['m'] = $arr[1];
        $res['s'] = $arr[2];
    }
    return $res;
}

// Convertit une durée en chaîne
// Indication : utiliser sprintf pour afficher les 0 des valeurs < 10, 2 => "02"
function toString(array $duration): string {
    // TODO :
    $strDuration = "";
    if ($duration['h']!=0)
        $strDuration = sprintf("%02s",$duration["h"]. ":" );
    $strDuration .= sprintf("%02s",$duration["m"]). ":" . sprintf("%02s",$duration["s"]); 

    
    return $strDuration;
}

// Addition de deux durées, en n'utilisant que des si-alors-sinon, des additions
// et des soustractions (pas de multiplication, ni de division, ni de modulo).
function add(array $d1, array $d2): array {
    $res = [ 'h' => 0, 'm' => 0, 's' => 0 ];
    // TODO
    $res['h']=$d1['h']+$d2['h'];
    $res['m']=$d1['m']+$d2['m'];
    $res['s']=$d1['s']+$d2['s'];
    if($res['s']>= 60){
        $res['s']-=60;
        $res['m']+=1;
    }
    if($res['m']>=60){
        $res['m']-=60;
        $res['h']+=1;
    }
    return $res;
}
