<?php

/*
// Zuständig für Tipp-Eingabe Seite
*/


function check_game_date($spieltag, $sp_nr) {

//true wenn spiel noch nicht begonnen hat

global $g_pdo;

$teil1 = "1";

 if ($spieltag > 17){
     $teil1 = "2";
     $spieltag = $spieltag - 17;
  }

$sql = "SELECT datum$teil1 AS datum FROM `Spieltage` WHERE ((spieltag = $spieltag) AND (sp_nr = $sp_nr))";


foreach ($g_pdo->query($sql) as $row) {
    $datum = $row['datum'];
}


$timestamp = time();


if ( $timestamp <= $datum ) {
  return true;
} else {
  return false;
}



}



?>
