<?php

function programm($team_nr, $start, $ende) {
   global $g_pdo;

   if ($start <= 17){
   $sql = "
   SELECT spieltag, team1, team2, t1.team_name as name1, t2.team_name as name2, datum1, datum2 
   FROM `Spieltage`, Teams t1, Teams t2 
   WHERE ((team1 = '$team_nr' OR team2 = '$team_nr') AND (spieltag >= $start) AND (spieltag <= $ende) 
   AND (t1.team_nr = team1) AND (t2.team_nr = team2))";

   foreach ($g_pdo->query($sql) as $row) {
      $spieltag = $row['spieltag'];
      $team_nr1[$spieltag] = $row['team1'];
      $team_nr2[$spieltag] = $row['team2'];
      $datum[$spieltag] = $row['datum1'];

      if ($team_nr1[$spieltag] == $team_nr) {
         $team_name[$spieltag] = $row['name2'];
      } else {
         $team_name[$spieltag] = $row['name1'];
      }

   }
   }


   if ($ende > 17){

      $ende = $ende - 17;
      $start = $start-17;

      $sql = "
      SELECT spieltag, team1, team2, t1.team_name as name1, t2.team_name as name2, datum1, datum2 
      FROM `Spieltage`, Teams t1, Teams t2 
      WHERE ((team1 = '$team_nr' OR team2 = '$team_nr') AND (spieltag <= $ende) AND (spieltag >= $start) 
      AND (t1.team_nr = team1) AND (t2.team_nr = team2))";

      foreach ($g_pdo->query($sql) as $row) {
         $spieltag = $row['spieltag'] + 17;
         $team_nr1[$spieltag] = $row['team2'];
         $team_nr2[$spieltag] = $row['team1'];
         $datum[$spieltag] = $row['datum2'];
  
         if ($team_nr1[$spieltag] == $team_nr) {
            $team_name[$spieltag] = $row['name1'];
         } else {
            $team_name[$spieltag] = $row['name2'];
         }

      }

   }



$sql = "
SELECT Ergebnisse.spieltag, tore1, tore2 
FROM `Spieltage`, Ergebnisse 
WHERE (((Spieltage.spieltag = Ergebnisse.spieltag - 17) OR (Spieltage.spieltag = Ergebnisse.spieltag))
AND (Spieltage.sp_nr = Ergebnisse.sp_nr) AND (team1 = $team_nr OR team2 = $team_nr))";

foreach ($g_pdo->query($sql) as $row) {
$spieltag = $row['spieltag'];
$tore1[$spieltag] = $row['tore1'];
$tore2[$spieltag] = $row['tore2'];
}



return array($spieltag, $team_nr1, $team_nr2, $team_name, $datum, $tore1, $tore2);
}




?>
