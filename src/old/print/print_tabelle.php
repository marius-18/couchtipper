<?php



function print_tabelle($args, $id, $show){

list($punkte, $tore, $gegentore, $diff, $team_name, $sieg, $unentschieden, $niederlage, $modus) = $args;

/*

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
*/

echo "<table class=\"table table-sm table-responsive-sm\" border=\"0\" align=\"center\" id=\"$id\" style=\"display: $show;\">";
echo "<tr bgcolor=\"#B6B6B4\"><th>Pl</th><th>Team</th><th>Sp</th><th>S</th><th>U</th><th>N</th><th>Tore</th><th>Diff</th><th>Pkt</th></tr>";

$a = 1;

foreach ($team_name AS $i => $team){

  if ($i < 3) {
    $color = " bgcolor=\"green\"";
  }
  if ($i == 3) {
    $color = " bgcolor=\"green\"";
  }
  if ($i > 3) {
    $color = " bgcolor=\"#F88017\"";
  }
  if ($i==6) {
   $color=" bgcolor=\"#FFFFFF\"";
   }
  if ($i > 6) {
    $color = "";
  }
  if ($i == 15) {
    $color = " bgcolor=\"E77471\"";
  }
  if ($i > 15) {
    $color = " bgcolor=\"red\"";
  }

  $spiele = $sieg[$i] + $unentschieden[$i] + $niederlage[$i];

   echo " 
<tr$color> 
<th>$a</th> 
<th>$team</th>
<th>$spiele</th> 
<th>$sieg[$i]</th> 
<th>$unentschieden[$i]</th> 
<th>$niederlage[$i]</th> 
<th>$tore[$i]:$gegentore[$i]</th> 
<th>$diff[$i]</th> 
<th>$punkte[$i]</th> 
</tr>";
$a++;
}


echo "</table>";

}


?>
