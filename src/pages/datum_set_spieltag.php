<?php
if (!allow_date()){
    echo "<div class=\"container-fluid\">
            <div class=\"alert alert-danger\">
                <span class=\"h5\"><span class=\"badge badge-pill badge-danger\"><strong>Verbotener Zugriff!</strong></span></span><br><br>
                <strong>Dieser Bereich ist nur f&uuml;r Administratoren!</strong><br>
                Frage beim Administrator nach, um Rechte zum &Auml;ndern von Ergebnissen zu bekommen.
            </div>
          </div>";
       return;
}
?>


<div class="container-fluid">


<?php




input_dates();
list ($spieltag_times, $akt_spieltag) = get_spieltag_dates();
print_form_input_date($akt_spieltag); 
echo "<br>";
print_date_table($spieltag_times);



function input_dates(){
    ## Speichert die Start Tage für die Spieltage in die DB
    global $g_pdo;
    
    
    if (isset($_POST['Spieltag'])){
        $spieltag = $_POST['Spieltag'];
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
}


function get_spieltag_dates(){
    ## Hole die Timestamps der Spieltage aus der DB
    global $g_pdo;
    
    
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
    
    return array($spieltag_times, $akt_spieltag);
}


function print_form_input_date($akt_spieltag){
    ## Print Input Form for Dates
    
    $set_tag_get   = "";
    $set_monat_get = "";
    $set_jahr_get  = "";
        
    if (isset($_GET['spt']) && ($_GET['spt'] != "")){
        ## Wir ändern eine Eintragung
        echo "<b>&Auml;nderung</b><br>";
        $spieltag = $_GET['spt'];
        $set_tag_get = $_GET['Tag'];
        $set_monat_get = $_GET['Monat'];
        $set_jahr_get = $_GET['Jahr'];
    }

    echo "<form method = \"POST\" action = \"index.php?index=8\">
    <input type=\"hidden\" name=\"Spieltag\" value =\"$akt_spieltag\"/>";
    
    
    echo "Spieltag: <b>$akt_spieltag</b><br>  ";
    
    ## Input Tag
    echo "Tag:<select name=\"Tag\">";
    for ($i = 1; $i < 32; $i++){
        if ($i == $set_tag_get){
            $sel = " selected";
        } else {
            $sel = "";
        }
        echo "<option value=\"$i\"$sel>$i</option>";
    }
    echo "</select>";
    
    
    ## Input Monat
    echo "Monat:<select name=\"Monat\">";
    for ($i = 1; $i < 13; $i++){
        if ($i == $set_monat_get){
            $sel = " selected";
        } else {
            $sel = "";
        }
        echo "<option value=\"$i\"$sel>$i</option>";
    }
    echo "</select>";
    
    ## Input Jahr
    $this_year = date("Y");
    $next_year = date("Y") + 1;
    $last_year = date("Y") - 1;
    
    $sel0 = "";
    $sel1 = "";
    $sel2 = "";
    
    if (isset($set_jahr_get) && ($last_year == $set_jahr_get)){
        $sel0 = " selected";
    }
    if (isset($set_jahr_get) && ($this_year == $set_jahr_get)){
        $sel1 = " selected";
    }
    if (isset($set_jahr_get) && ($next_year == $set_jahr_get)){
        $sel2 = " selected";
    }
    
    echo "Jahr:<select name=\"Jahr\">
                <option value=\"$last_year\"$sel0>$last_year</option>
                <option value=\"$this_year\"$sel1>$this_year</option>
                <option value=\"$next_year\"$sel2>$next_year</option>
            </select>";
    
    ## Enter Button
    echo "<br><br>";
    echo "<input type=\"Submit\" value=\"Enter\"></form>";
}


function print_date_table($timestamps){
    ### Tabelle mit eingetragenen Daten

    ## Wochentage Dictionary
    $wochentage = array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag");

    echo "<table border=\"1\" align=\"center\">
            <tr><td>Spt</td><td>Timestamp</td><td>Datum</td><td>Tag</td><td></td></tr>";
    
    for ($i = 1; $i < count($timestamps) + 1; $i++){
        $datum  = date("d.m.y",$timestamps[$i]);
        $show_tag   = date("d",$timestamps[$i]);
        $show_monat = date("n",$timestamps[$i]);
        $show_jahr  = date("Y",$timestamps[$i]);
        
        $wochentag = substr($wochentage[date("w",$timestamps[$i])], 0, 2); 
        
        echo "
        <tr>
            <td>$i</td>
            <td>$timestamps[$i]</td>
            <td>$datum</td>
            <td>$wochentag</td>
            <td><a href=\"index.php?index=8&spt=$i&Tag=$show_tag&Monat=$show_monat&Jahr=$show_jahr\">
             <i class=\"far fa-edit text-dark\"></i></a></td>
        </tr>
        ";
    }
    
    echo "</table>";
}



?>


</div>
<br>
