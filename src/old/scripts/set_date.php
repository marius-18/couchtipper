<div class="content">
<br>

<?php

require_once('src/functions/main.inc.php');
require_once('src/functions/template.inc.php');
require_once('src/functions/datenbank.inc.php');

if (!allow_date()){
echo "Dieser Bereich ist nur f&uuml;r Administratoren!<br>
Frage beim Administrator nach, um Rechte zum &Auml;ndern von Ergebnissen zu bekommen.";
exit;

}



?>


<?php

if ($_GET['spt'] != ""){
   echo "<b>&Auml;nderung</b><br>";
   $spieltag = $_GET['spt'];
} else {
   $spieltag = $_POST['Spieltag'];
}

$tag = $_POST['Tag'];
$monat = $_POST['Monat'];
$jahr = $_POST['Jahr'];


$wochentage = array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag");

$tag_ein = strtotime("$jahr-$monat-$tag");


$datum = strtotime("$jahr-$monat-$tag, 00:00:59"); 
$datum_string = date("d.m.Y - H:i",$datum);


if (($spieltag != "") && ($tag != "") && ($monat != "")){
   $sql = "INSERT INTO `Datum`(`spieltag`, `datum`) VALUES ('$spieltag','$datum')";
   $g_pdo->query($sql);

   $sql = "UPDATE `Datum` SET `datum` = '$datum' WHERE `spieltag` = '$spieltag'";
   $g_pdo->query($sql);


}


$sql = "SELECT `spieltag`,`datum` FROM `Datum`";


foreach ($g_pdo->query($sql) as $row) {
   $a = $row['spieltag'];
   $spieltag_times[$a] = $row['datum'];
}



if ($_GET['spt'] != ""){
   $akt_spieltag = $_GET['spt'];
} else{
   $akt_spieltag = count($spieltag_times)+1;
}


echo "<form method = \"POST\" action = \"index.php?index=9\"><input type=\"hidden\" name=\"Spieltag\" value =\"$akt_spieltag\"/>";


echo "Spieltag: <b>$akt_spieltag</b><br>  ";




echo "Tag:<select name=\"Tag\">";

for ($i=1; $i<32; $i++){
   if ($i==$tag){
      $sel=" selected";
   } else {
      $sel="";
   }
   echo "<option value=\"$i\"$sel>$i</option>
   ";
}

echo "</select>
";



echo "Monat:<select name=\"Monat\">";

for ($i=1; $i<13; $i++){
   if ($i==$monat){
      $sel=" selected";
   } else {
      $sel="";
   }
   echo "<option value=\"$i\"$sel>$i</option>
   ";
}

echo "</select>
";



$this_year = date("Y");
$next_year = date("Y")+1;



if ($this_year == $jahr){
$sel1 = " selected";
}


if ($next_year == $jahr){
$sel2 = " selected";
}

echo "Jahr:<select name=\"Jahr\">
<option value=\"$this_year\"$sel1>$this_year</option>
<option value=\"$next_year\"$sel2>$next_year</option>
</select>
";



echo "<br><br><input type=\"Submit\" value=\"Enter\"></form>
";





echo "<table border=\"1\" align=\"center\">
<tr><td>Spt</td><td>Timestamp</td><td>Datum</td><td>Tag</td><td></td></tr>";


for ($i=1; $i<count($spieltag_times)+1; $i++){
   $date = date("d.m. H:i",$spieltag_times[$i]);
   $wochentag=$wochentage[date("w",$spieltag_times[$i])];

   echo "
<tr>
   <td>$i</td>
   <td>$spieltag_times[$i]</td>
   <td>$date</td>
   <td>$wochentag[0]$wochentag[1]</td>
   <td><a href=\"index.php?index=9&spt=$i\"><img src=\"images/edit.png\" width=\"20\" height=\"20\"></a></td>
</tr>
   ";

}

echo "</table>";



?>
<br>

</div>
<br>
