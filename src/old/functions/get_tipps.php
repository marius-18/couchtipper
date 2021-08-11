<?php


function get_other_tipps($spieltag, $sp_nr, $modus) {
  global $g_pdo;

  $sql = "SELECT Tipps.user_nr AS user_nr, User.user_name, User.vorname AS vorname, User.nachname AS nachname, tore1, tore2 
          FROM `Tipps`, User 
          WHERE (spieltag = $spieltag && sp_nr = $sp_nr && Tipps.user_nr = User.user_nr)";

  foreach ($g_pdo->query($sql) as $row) {
    $i = $row['user_nr'];
    $tore1 = $row['tore1'];
    $tore2 = $row['tore2'];
    if ((($modus == "Tipps") && (get_usernr() != $i)) || ( $modus == "Spieltag")){
      $tipp[$i] = $tore1." : ". $tore2;
      $user_nr[$i] = $i;
      $user_name[$i] = $row['user_name'];   
      $vorname[$i] = $row['vorname'];
      $nachname[$i] = $row['nachname'];
    }
    
  }


  if (!check_game_date($spieltag,$sp_nr)){
#return 0;
    return array($user_nr, $user_name, $tipp, $vorname, $nachname);
  }
}




?>
