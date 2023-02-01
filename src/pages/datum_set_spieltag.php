<div class="container-fluid">
<br>

<?php
//require_once('src/functions/main.inc.php');     //braucht man das nicht oder wie ?!
//require_once('src/functions/template.inc.php');
//require_once('src/functions/datenbank.inc.php');

if (!allow_date()){
    echo "<div class=\"alert alert-danger\"> Dieser Bereich ist <strong>nur f&uuml;r Administratoren</strong>!<br>
    Frage beim Administrator nach, um Rechte zum &Auml;ndern von Spielzeiten zu bekommen.</div>";
    exit;

}



?>


<?php

if (isset($_GET['spt']) && ($_GET['spt'] != "")){
    echo "<b>&Auml;nderung</b><br>";
    $spieltag = $_GET['spt'];
} else {
    if (isset($_POST['Spieltag'])){
        $spieltag = $_POST['Spieltag'];
    }
}

if (isset($_POST['Tag'])){
    $tag = $_POST['Tag'];
}
if (isset($_POST['Monat'])){
    $monat = $_POST['Monat'];
}
if (isset($_POST['Jahr'])){
    $jahr = $_POST['Jahr'];
}

$wochentage = array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag");



if (isset($spieltag) && isset($tag) && isset($monat)){
    $datum = strtotime("$jahr-$monat-$tag, 00:00:59"); 
    $datum_string = date("d.m.Y - H:i",$datum);
    
    $sql = "INSERT INTO `Datum`(`spieltag`, `datum`) VALUES ('$spieltag','$datum') ON DUPLICATE KEY UPDATE `datum` = '$datum'";
    $g_pdo->query($sql);

    #$sql = "UPDATE `Datum` SET `datum` = '$datum' WHERE `spieltag` = '$spieltag'";
    #$g_pdo->query($sql);


}


$sql = "SELECT `spieltag`,`datum` FROM `Datum`";


foreach ($g_pdo->query($sql) as $row) {
    $a = $row['spieltag'];
    $spieltag_times[$a] = $row['datum'];
}



if (isset($_GET['spt']) && ($_GET['spt'] != "")){
    $akt_spieltag = $_GET['spt'];
} else{
    $akt_spieltag = count($spieltag_times)+1;
}


echo "<form method = \"POST\" action = \"index.php?index=8\"><input type=\"hidden\" name=\"Spieltag\" value =\"$akt_spieltag\"/>";


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


$sel1 = "";
$sel2 = "";
if (isset($jahr) && ($this_year == $jahr)){
    $sel1 = " selected";
}

if (isset($jahr) && ($next_year == $jahr)){
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
    $date = date("d.m.y",$spieltag_times[$i]); ## H:i
    $wochentag=$wochentage[date("w",$spieltag_times[$i])];

    echo "
        <tr>
            <td>$i</td>
            <td>$spieltag_times[$i]</td>
            <td>$date</td>
            <td>$wochentag[0]$wochentag[1]</td>
            <td><a href=\"index.php?index=8&spt=$i\"><img src=\"images/edit.png\" width=\"20\" height=\"20\"></a></td>
        </tr>
        ";

}

echo "</table>";



?>
<br>

</div>
<br>
