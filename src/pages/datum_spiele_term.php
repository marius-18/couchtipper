<div class="container-fluid">
<?php

require_once('src/include/lib/forms.inc.php');

if (!allow_date()){
   echo "Dieser Bereich ist nur f&uuml;r Administratoren!<br>
   Frage beim Administrator nach, um Rechte zum &Auml;ndern von Ergebnissen zu bekommen.";
   exit;
}


$spieltag = spt_select();
if (isset($_POST['spieltag'])){
   $spieltag = $_POST['spieltag'];
}

select_spieltag($spieltag);

$teil1 = "1";
$teil2 = "2";
$real_spieltag = $spieltag;

if (($spieltag > 17) && (get_wettbewerb_code(get_curr_wett()) == "BuLi") ){
    $teil1 = "2";
    $teil2 = "1";
    $spieltag = $spieltag - 17;
}

echo "<div class = \"content\"><br>";

?>


<?php


$show_formular = true;

$error_count = 0;
$error = false;
$error_msg = "";

$spiel = array();

if (isset($_POST['spiel1'])){
   $spiel[1] = $_POST['spiel1'];
}
if (isset($_POST['spiel2'])){
   $spiel[2] = $_POST['spiel2'];
}
if (isset($_POST['spiel3'])){
   $spiel[3] = $_POST['spiel3'];
}
if (isset($_POST['spiel4'])){
   $spiel[4] = $_POST['spiel4'];
}
if (isset($_POST['spiel5'])){
   $spiel[5] = $_POST['spiel5'];
}
if (isset($_POST['spiel6'])){
   $spiel[6] = $_POST['spiel6'];
}
if (isset($_POST['spiel7'])){
   $spiel[7] = $_POST['spiel7'];
}
if (isset($_POST['spiel8'])){
   $spiel[8] = $_POST['spiel8'];
}
if (isset($_POST['spiel9'])){
   $spiel[9] = $_POST['spiel9'];
}


## Uhrzeiten eintragen
for ($sp_nr = 1; $sp_nr < 10; $sp_nr++) {
    if (isset($spiel[$sp_nr])) {
        $eingabe = $spiel[$sp_nr];
        $sql = "UPDATE Spieltage SET datum$teil1 = $eingabe WHERE sp_nr = $sp_nr AND spieltag = $spieltag";
        $result = $g_pdo->query($sql);
        if ($result != true){
            $error_count++;
        }
    }
}



## Datum des Spieltags aus DB holen
$sql = "SELECT datum FROM Datum WHERE spieltag = $real_spieltag";
foreach ($g_pdo->query($sql) as $row) {
    $main_datum = $row['datum'];
}



## Hole Spiele aus der DB
$sql = "SELECT datum$teil1,sp_nr FROM Spieltage WHERE spieltag = $spieltag";
foreach ($g_pdo->query($sql) as $row) {
    $sp_nr = $row['sp_nr'];
    if (isset($row['datum1'])){
        $spiel[$sp_nr] = $row['datum1'];
    } else {
        $spiel[$sp_nr] = $row['datum2'];
    }
}



## Fehler ausgeben, wenn Spieltag noch nicht terminiert ist
if ($main_datum == "") {
    $show_formular = false;
    $error = true;
    $error_msg = "Es muss erst ein Termin f&uuml;r den Spieltag ausgew&auml;hlt werden!";

}



## Gebe das eigentliche Formular aus
if ($show_formular){
    $tag = date("N",$main_datum);
    
    ## Anstosszeiten definieren 
    if (!is_big_tournament(get_curr_wett())){
        if ($tag == 5) {
            ## Wenn Spieltag Freitags beginnt..
            $anstosszeiten = 
                ["Fr" => array("20:30"), 
                 "Sa" => array("15:30", "18:30"),
                 "So" => array("15:30", "17:30", "19:30")
                ];
        }
        
        if ($tag == 6) {
            ## Wenn Spieltag Samstags beginnt..
            $anstosszeiten = 
                [ 
                 "Sa" => array("15:30", "18:30", "20:30"),
                 "So" => array("15:30", "17:30", "19:30")
                ];
        }
        
        if ($tag == 2) {
            ## Wenn Spieltag Dienstags beginnt..
            $anstosszeiten = 
                [
                 "Di" => array("18:30", "20:30"),
                 "Mi" => array("18:30", "20:30")
                ];
        }
    } else {
        ## Bei WM/EM hier die Anstoßzeiten eintragen...   
        if (get_wettbewerb_code(get_curr_wett())== "EM"){
            ## EM Anstosszeiten
            $anstosszeiten = 
                [
                "Anstoss" => array("15:00", "18:00", "21:00")
                ];
        }
        
        if (get_wettbewerb_code(get_curr_wett()) == "WM"){
            ## WM Anstosszeiten
            $anstosszeiten = 
                [
                "Anstoss" => array("12:00", "14:00", "15:00", "16:00", "17:00", "18:00", "20:00", "21:00")
                ];
        }
    }
    
    ## Anstosszeiten anzeigen
    echo "<form action=\"\" method=\"post\">
            <table border = \"1\" align = \"center\" width = \"100%\">";
    
    ## Tag anzeigen
    echo "<tr>";
    echo "<td></td><td></td>";
    foreach ($anstosszeiten as $days => $zeiten){
        $my_anz = count($zeiten);
        echo "<td colspan= \"$my_anz\" align = \"center\">$days</td>";
    }
    echo "</tr>";
       
    ## Stunde Anzeigen
    echo "<tr>";
    echo "<td></td>";
    foreach ($anstosszeiten as $days => $zeiten){
        $my_anz = count($zeiten);
        foreach ($zeiten as $time){
            $my_stunde = explode(":",$time)[0];
            echo "<td align = \"center\">$my_stunde</td>";
        }
    }
    echo "</tr>";
    
    ## Minuten anzeigen
    echo "<tr>";
    echo "<td></td>";
    foreach ($anstosszeiten as $days => $zeiten){
        $my_anz = count($zeiten);
        foreach ($zeiten as $time){
            $my_stunde = explode(":",$time)[1];
            echo "<td align = \"center\">$my_stunde</td>";
        }
    }
    echo "</tr>";
    
    
    ## Spiele aus DB holen
    $sql = "SELECT sp_nr, t1.team_name AS Team_name$teil1, t2.team_name AS Team_name$teil2,sp_nr
            FROM `Spieltage`,Teams t1, Teams t2
            WHERE (`spieltag` = $spieltag) AND (t1.team_nr = team1) AND (t2.team_nr = team2)
            ORDER BY sp_nr ASC";
    
    
    foreach ($g_pdo->query($sql) as $row) {
        echo "<tr>";
        
        $sp_nr   = $row['sp_nr'];
        $db_time = $spiel[$sp_nr];
        echo "  <!--<td>$sp_nr</td>-->
                <td align = \"center\">".$row['Team_name1']."-".$row['Team_name2']."</td>";
                
        echo spiele_term($main_datum, $sp_nr, $db_time, $anstosszeiten);
        
        echo "</tr>";
    }
    
    echo "</table>
    <br>
    <input type=\"hidden\" name =\"spieltag\" value=\"$real_spieltag\" visible=\"false\">
    <input type=\"Submit\" value=\"Enter\">
    </form>
    ";
}


if ($error) {
    echo "<font color = \"red\"><b> $error_msg</b> </font><br><br>";
} else {
    echo "Es gab ".$error_count." Fehler beim Speichern";
}

?>

</div>
<br>





<?php

function spiele_term($main_datum, $sp_nr, $time, $anstosszeiten){
    $anstoss = array();
    $i = 0;
    $tage = 0;
    
    ## Anstosszeiten Timestamps berechnen
    foreach ($anstosszeiten as $days => $zeiten){
        $my_anz = count($zeiten);
        foreach ($zeiten as $uhrzeit){
            $stunde = explode(":", $uhrzeit)[0];
            $minute = explode(":", $uhrzeit)[1];
            
            ## start des Tages:
            $start_datum = $main_datum + 24*60*60*$tage;

            $anstoss[$i] = strtotime(date("d.m.Y", $start_datum) . " $stunde:$minute:59");
            $i++;
        }
        $tage++;
    }
    
    ## Checkboxen für die Anstosszeiten anzeigen
    foreach ($anstoss as $anstosszeit){
        $checked = "";
        if ($time == $anstosszeit) {
            $checked = "checked";
        }
        
        echo "<td align = \"center\">
                <input type=\"radio\" name=\"spiel$sp_nr\" value=\"$anstosszeit\" $checked>
             </td>";
    }
}

?>
</div>
