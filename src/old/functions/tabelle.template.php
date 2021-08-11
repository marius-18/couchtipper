<?php


function tabelle($modus, $beginn){
global $g_pdo;


if ($modus == "Heim"){
  $ort = "AND (heim = 1)";
}

if ($modus == "Auswaerts"){
  $ort = "AND (heim = 0)";
}

if ($modus == "Hinrunde"){
  $heim = "AND (spieltag <= 17)";
}


$sql = "SELECT sum(tore) as tore, sum(gegentore) as gegentore, sum(punkte) as punkte,
               sum(sieg) as sieg, sum(niederlage) as niederlage, sum(unentschieden) as unentschieden,
               team_name, Teams.team_nr
        FROM `Tabelle`, Teams 
        WHERE ((Teams.team_nr = Tabelle.team_nr)  AND (spieltag > $beginn) $ort $heim)
        GROUP BY Teams.team_nr";



  foreach ($g_pdo->query($sql) as $row) {
    $team_nr = $row['team_nr'];
    $tore[$team_nr] = $row['tore'];
    $gegentore[$team_nr] = $row['gegentore'];
    $diff[$team_nr] = $row['tore'] - $row['gegentore'];
    $punkte[$team_nr] = $row['punkte'];
    $sieg[$team_nr] = $row['sieg'];
    $niederlage[$team_nr] = $row['niederlage'];
    $unentschieden[$team_nr] = $row['unentschieden'];
    $team_name[$team_nr] = $row['team_name'];

  }




array_multisort($punkte, SORT_DESC, $diff, SORT_DESC, $tore, SORT_DESC, $gegentore, SORT_ASC, $team_name, $niederlage, $sieg, $unentschieden);

return (array($punkte, $tore, $gegentore, $diff, $team_name, $sieg, $unentschieden, $niederlage, $modus));


}







?>
