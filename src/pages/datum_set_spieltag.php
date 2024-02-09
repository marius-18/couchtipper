<div class="container-fluid">
<br>

<?php

if (!allow_date()){
    echo "<div class=\"alert alert-danger\"> Dieser Bereich ist <strong>nur f&uuml;r Administratoren</strong>!<br>
    Frage beim Administrator nach, um Rechte zum &Auml;ndern von Spielzeiten zu bekommen.</div>";
    exit;

}

?>




<?php

if (isset($_GET['spt']) && ($_GET['spt'] != "")){
    ## Wir ändern eine Eintragung
    echo "<b>&Auml;nderung</b><br>";
    $spieltag = $_GET['spt'];
    $set_tag_get = $_GET['Tag'];
    $set_monat_get = $_GET['Monat'];
    $set_jahr_get = $_GET['Jahr'];
} else {
    if (isset($_POST['Spieltag'])){
        ## TODO: wozu das?
        $spieltag = $_POST['Spieltag'];
    }
    $set_tag_get = "";
    $set_monat_get = "";
    $set_jahr_get = "";
}

if (isset($_POST['Tag'])){
    $set_tag_post = $_POST['Tag'];
}
if (isset($_POST['Monat'])){
    $set_monat_post = $_POST['Monat'];
}
if (isset($_POST['Jahr'])){
    $set_jahr_post = $_POST['Jahr'];
}



## Trage den Spieltag in die Datenbank ein
if (isset($spieltag) && isset($set_tag_post) && isset($set_monat_post)){
    $datum = strtotime("$set_jahr_post-$set_monat_post-$set_tag_post, 00:00:59"); 
    $datum_string = date("d.m.Y - H:i",$datum);
    
    $sql = "INSERT INTO `Datum`(`spieltag`, `datum`) VALUES ('$spieltag','$datum') ON DUPLICATE KEY UPDATE `datum` = '$datum'";
    $g_pdo->query($sql);
}



## Hole die Timestamps der Spieltage aus der DB
$sql = "SELECT `spieltag`,`datum` FROM `Datum`";
foreach ($g_pdo->query($sql) as $row) {
    $a = $row['spieltag'];
    $spieltag_times[$a] = $row['datum'];
}



if (isset($_GET['spt']) && ($_GET['spt'] != "")){
    $akt_spieltag = $_GET['spt'];
} else{
    if (isset($spieltag_times)){
        $akt_spieltag = count($spieltag_times)+1;
    } else {
        $akt_spieltag = 1;
    }
}


echo "<form method = \"POST\" action = \"index.php?index=8\"><input type=\"hidden\" name=\"Spieltag\" value =\"$akt_spieltag\"/>";

echo "Spieltag: <b>$akt_spieltag</b><br>  ";


## Input Tag
echo "Tag:<select name=\"Tag\">";

for ($i = 1; $i < 32; $i++){
    if ($i == $set_tag_get){
        $sel = " selected";
    } else {
        $sel = "";
    }
    echo "<option value=\"$i\"$sel>$i</option>
            ";
}

echo "</select>
";


## Input Monat
echo "Monat:<select name=\"Monat\">";

for ($i = 1; $i < 13; $i++){
    if ($i == $set_monat_get){
        $sel = " selected";
    } else {
        $sel = "";
    }
    echo "<option value=\"$i\"$sel>$i</option>
            ";
}

echo "</select>
";


## Input Jahr
$this_year = date("Y");
$next_year = date("Y") + 1;

$sel1 = "";
$sel2 = "";
if (isset($set_jahr_post) && ($this_year == $set_jahr_get)){
    $sel1 = " selected";
}
if (isset($set_jahr_post) && ($next_year == $set_jahr_get)){
    $sel2 = " selected";
}

echo "Jahr:<select name=\"Jahr\">
                <option value=\"$this_year\"$sel1>$this_year</option>
                <option value=\"$next_year\"$sel2>$next_year</option>
            </select>
";


## Enter Button
echo "<br><br><input type=\"Submit\" value=\"Enter\"></form>
";



### Tabelle mit eingetragenen Daten

## Wochentage Dictionary
$wochentage = array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag");

echo "<table border=\"1\" align=\"center\">
<tr><td>Spt</td><td>Timestamp</td><td>Datum</td><td>Tag</td><td></td></tr>";

for ($i = 1; $i < count($spieltag_times) + 1; $i++){
    $date       = date("d.m.y",$spieltag_times[$i]);
    $show_tag   = date("d",$spieltag_times[$i]);
    $show_monat = date("n",$spieltag_times[$i]);
    $show_jahr  = date("Y",$spieltag_times[$i]);
    
    
    $wochentag = $wochentage[date("w",$spieltag_times[$i])];
    
    echo "
        <tr>
            <td>$i</td>
            <td>$spieltag_times[$i]</td>
            <td>$date</td>
            <td>$wochentag[0]$wochentag[1]</td>
            <td><a href=\"index.php?index=8&spt=$i&Tag=$show_tag&Monat=$show_monat&Jahr=$show_jahr\">
             <i class=\"far fa-edit text-dark\"></i></a></td>
        </tr>
        ";

}

echo "</table>";



?>
<br>

</div>
<br>
