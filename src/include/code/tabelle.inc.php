<?php


function tabelle($modus, $beginn, $ende=NULL){
global $g_pdo;

$ort = "";
$heim = "";
$endzeit = "";
if ($modus == "Heim"){
  $ort = "AND (heim = 1)";
}

if ($modus == "Auswaerts"){
  $ort = "AND (heim = 0)";
}

if ($modus == "Hinrunde"){
  $heim = "AND (spieltag <= 17)";
}

if ($ende !== NULL){
  $endzeit = "AND (spieltag <= $ende)";
}

$sql = "SELECT sum(tore) as tore, sum(gegentore) as gegentore, sum(punkte) as punkte,
               sum(sieg) as sieg, sum(niederlage) as niederlage, sum(unentschieden) as unentschieden,
               team_name, Teams.team_nr
        FROM `Tabelle`, Teams 
        WHERE ((Teams.team_nr = Tabelle.team_nr)  AND (spieltag > $beginn) $ort $heim $endzeit)
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
    $nummer[$team_nr] = $team_nr;

  }


if (!isset($punkte)){ 
  ## Falls die Rückrunde noch nicht gestartet ist, sind die arrays nicht vorhanden..
  ## Deshalb Abbruch, damit array_multisort nicht fehlschlägt
  return;
}

array_multisort($punkte, SORT_DESC, $diff, SORT_DESC, $tore, SORT_DESC, $gegentore, SORT_ASC, $team_name, $niederlage, $sieg, $unentschieden, $nummer);

return (array($punkte, $tore, $gegentore, $diff, $team_name, $sieg, $unentschieden, $niederlage, $modus, $nummer));


}


function wm_tabelle($gruppe){
   global $g_pdo;

   $sql = "SELECT sum(tore) as tore, sum(gegentore) as gegentore, sum(punkte) as punkte,
                  sum(sieg) as sieg, sum(niederlage) as niederlage, sum(unentschieden) as unentschieden,
                  team_name, Teams.team_nr AS team_nr, gruppe, position
           FROM `Tabelle`, Teams 
           WHERE ((Tabelle.team_nr = Teams.team_nr) AND gruppe = '$gruppe')
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
      $team_nr_a[$team_nr] = $team_nr;
      $position[$team_nr] = $row['position'];
   }
   


   array_multisort($punkte, SORT_DESC, $position, SORT_ASC, $diff, SORT_DESC, $tore, SORT_DESC, $gegentore, $team_name, $niederlage, $sieg, $unentschieden, $team_nr_a);
    
    
    //tie breaking.. 
    // hier ist die tabelle sortiert hier muss noch gecheckt werden was bei punktgleichheit ist.
    
    
    //tie_breaking(array($punkte, $tore, $gegentore, $diff, $team_name, $sieg, $unentschieden, $niederlage, $gruppe, $team_nr_a));
    // ok hab eingesehen, der scheiß ist zu schwer...
    
    
   return (array($punkte, $tore, $gegentore, $diff, $team_name, $sieg, $unentschieden, $niederlage, $gruppe, $team_nr_a));
}



function tie_breaking($args){

    list($punkte, $tore, $gegentore, $diff, $team_name, $sieg, $unentschieden, $niederlage, $gruppe, $team_nr_a) = $args;

    $equal = array(0 => array());
    $alt_punkt = $punkte[0];
    $i = 0;
    for ($key = 0; $key < count($punkte); $key+= 1){
        $poi = $punkte[$key];
        echo "punkte: $poi, key: $key<br>";
        
        //Abteilung hässlich....
        if ($poi == $alt_punkt){
            array_push($equal[$i], $key);
        } else {
            $i += 1;
            array_push($equal, array());
            array_push($equal[$i], $key);
        }
        $alt_punkt = $poi;
    }

    foreach ($equal as $key => $teams){       
        if (count($teams) > 1){
            echo "schlüssel: $key, $teams <br>";
            direkter_vergleich($teams, $team_nr_a);
        }
    }
    
    print_r($equal);



    return array($punkte, $tore, $gegentore, $diff, $team_name, $sieg, $unentschieden, $niederlage, $gruppe, $team_nr_a);

}

function direkter_vergleich($teams, $team_nr_a){

echo "punktgleiche Teams: ";
    foreach ($teams as $key){
        echo "$key (".$team_nr_a[$key]."), ";
        
    }
echo "<br>";
}


function print_tabelle($args, $id, $show){

list($punkte, $tore, $gegentore, $diff, $team_name, $sieg, $unentschieden, $niederlage, $modus) = $args;

echo "<div class=\"container\" id=\"$id\" style=\"display: $show;\">";

if (!isset($punkte)){
  echo "<b>Die Tabelle ist noch nicht vorhanden</b></div>";
  return;
}

if ($modus == "Heim") {
  echo "<b>Heimtabelle</b>";
}
 elseif ($modus == "Auswaerts"){
  echo "<b>Auswärtstabelle</b>";
}
 elseif ($modus == "Tendenz"){
  echo "<b>Tendenz</b>";
}
 elseif ($modus == "Hinrunde"){
  echo "<b>Hinrunde</b>";
}
 elseif ($modus == "Rückrunde"){
  echo "<b>R&uuml;ckrunde</b>";
}
 elseif ($modus == "") {
  echo "<b>Tabelle</b>";
}

echo "<div class=\"table-responsive\">";
echo "<table class=\"table table-sm  table-hover text-center center\">";
echo "<tr bgcolor=\"#B6B6B4\"><th>Pl</th><th>Team</th><th>Sp</th><th>S</th><th>U</th><th>N</th><th>Tore</th><th class=\"d-none d-sm-table-cell\">Diff</th><th>Pkt</th></tr>";

$a = 1;

foreach ($team_name AS $i => $team){

  if ($i < 4) {
    $color = "class=\"table-success\"";
  }
  if ($i == 4) {
    $color = "class=\"table-info\"";
  }
  if ($i == 5) {
    $color = "class=\"table-primary\"";
  }
  if ($i == 6) {
   $color = "";
   }
  if ($i > 6) {
    $color = "";
  }
  if ($i == 15) {
    $color = "class=\"table-warning\"";
  }
  if ($i > 15) {
    $color = "class=\"table-danger\"";
  }

  $spiele = $sieg[$i] + $unentschieden[$i] + $niederlage[$i];

   echo " 
<tr $color> 
<th>$a</th> 
<th nowrap>$team</th>
<th>$spiele</th> 
<th>$sieg[$i]</th> 
<th>$unentschieden[$i]</th> 
<th>$niederlage[$i]</th> 
<th>$tore[$i]:$gegentore[$i]</th> 
<th class=\"d-none d-sm-table-cell\">$diff[$i]</th> 
<th>$punkte[$i]</th> 
</tr>";
$a++;
}


echo "</table></div></div>";

}


function print_wm_tabelle($args){
    list($punkte, $tore, $gegentore, $diff, $team_name, $sieg, $unentschieden, $niederlage, $gruppe) = $args;

    //echo "<div class=\"container-fluid bg-warning\">";
    //echo "<b>Gruppe $gruppe</b>";

    echo "<div class=\"table-responsive\">";
    echo "<table class=\"table table-sm  table-hover text-center center\">";
    echo "<tr class=\"thead-dark\"><th>Pl</th><th>Team</th><th>Sp</th><th>S</th><th>U</th><th>N</th><th>Tore</th><th>Diff</th><th>Pkt</th></tr>";

    $a = 1;

    foreach ($team_name AS $i => $team){

        if ($i < 2) {
            $color = " class = \"table-success\"";
        } else {
            $color = "";
        }

        $spiele = $sieg[$i] + $unentschieden[$i] + $niederlage[$i];

        echo " 
            <tr$color> 
            <td>$a</td> 
            <td>$team</td>
            <td>$spiele</td> 
            <td>$sieg[$i]</td> 
            <td>$unentschieden[$i]</td> 
            <td>$niederlage[$i]</td> 
            <td>$tore[$i]:$gegentore[$i]</td> 
            <td>$diff[$i]</td>
            <td>$punkte[$i]</td> 
            </tr>";
    
        $a++;
    }

    echo "</table><br>";


    echo "</div>";
}



function tabelle_verlauf(){
    global $g_pdo;
    $sql = "SELECT DISTINCT team_nr, team_name FROM `Teams`, Spieltage WHERE team_nr = team1 ORDER BY `Teams`.`team_name` ASC";
    
    foreach ($g_pdo->query($sql) as $row) {
        $team_nr    = $row['team_nr'];
        $team_name  = $row['team_name'];
        $names[$team_nr] = $team_name;
        $logos[$team_nr] = "<img src=\"images/Vereine/$team_nr.gif\" width=\"30px\">";
        $nums[$team_nr] = $team_nr;
    }

    $today = akt_spieltag();
    $sql = "SELECT team_nr, platz, spieltag FROM `Tabelle` WHERE spieltag > 0 AND spieltag <= $today ORDER BY `team_nr` ASC";
    
    foreach ($g_pdo->query($sql) as $row) {
        $team_nr    = $row['team_nr'];
        $spieltag    = $row['spieltag'];
        $platz[$spieltag][$team_nr] = $row['platz'];
    }
    return array($nums, $logos, $platz);
}

function print_tabelle_verlauf($args, $id, $show){
    list($nums, $logos, $platz) = $args;
    echo "<div class=\"container\" id=\"$id\" style=\"display: $show;\">";

    echo "<b>Tabellenverlauf</b>";

    echo "
    <div class=\"table-responsive\">
    
    <table data-toggle=table data-order=1 data-sort-order=\"asc\"
           class=\"table table-sm   table-hover text-center center text-nowrap\" align=\"center\">
        
    <thead style=\"position: sticky;top: 100;\" class=\"thead-dark data-sticky-header\">
    <tr>
        <th data-fixed-columns=\"true\"></th>";
    
    foreach ($platz as $index => $abcd){
        echo "<th data-field=$index  data-sort-order=asc data-sortable=true>$index</th>";
    }
    echo "<th></th>";
    echo "</tr></thead>";

    foreach ($nums as $index1 => $i){
        echo "<td >".$logos[$i]."</td>";
        
        foreach ($platz as $index => $abcd){
            if ($platz[$index][$i] == 1){
                $color = "class=\"table-success\"";
            } elseif (($platz[$index][$i] == 2) || ($platz[$index][$i] == 3) || ($platz[$index][$i] == 4)){
                $color = "class=\"table-primary\"";
            } elseif ($platz[$index][$i] == 5){
                $color = "class=\"table-info\"";
            } elseif ($platz[$index][$i] == 6){
                $color = "class=\"table-secondary\"";
            } elseif ($platz[$index][$i] == 16){
                $color = "class=\"table-warning\"";
            } elseif (($platz[$index][$i] == 17) || ($platz[$index][$i] == 18)){
                $color = "class=\"table-danger\"";
            } else {
                $color = "";                
            }
            
            echo "<td $color>".$platz[$index][$i]."</td>";
        }
        
        echo "<td>".$logos[$i]."</td>";
        echo "</tr>";
    }
    
    echo "</table></div></div>";
}

?>
