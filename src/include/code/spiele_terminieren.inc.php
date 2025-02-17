<?php

function input_game_kickofftime($spieltag, $modus){
    ## Gibt Anstosszeiten in die Datenbank ein
    ## $modus ist abhängig von Hin-/Rückrunde, in $modus[0] steht die entsprechende Datums ID
    global $g_pdo;
    
    $error_count = 0;
    $input_count = 0;
    
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
            $sql = "UPDATE Spieltage SET datum".$modus[0]." = $eingabe WHERE sp_nr = $sp_nr AND spieltag = $spieltag";
            $result = $g_pdo->query($sql);
            if ($result != true){
                $error_count++;
            } else {
                $input_count++;
            }
        }
    }
    
    return array($input_count, $error_count);
}


function get_times($spieltag, $modus){
    ## Hole Spiele aus der DB
    ## $modus ist abhängig von Hin-/Rückrunde, in $modus[0] steht die entsprechende Datums ID
    global $g_pdo;

    $sql = "SELECT datum".$modus[0].",sp_nr FROM Spieltage WHERE spieltag = $spieltag";
    foreach ($g_pdo->query($sql) as $row) {
        $sp_nr = $row['sp_nr'];
        if (isset($row['datum1'])){
            $times[$sp_nr] = $row['datum1'];
        } else {
            $times[$sp_nr] = $row['datum2'];
        }
    }
    
    return $times;
}


function print_game_table($main_datum, $spieltag, $modus){
    ## Zeigt die Tabelle zum Auswählen der Anstosszeiten an
    ## $modus ist abhängig von Hin-/Rückrunde, in $modus[0] steht die entsprechende Datums ID
    global $g_pdo;
    
    $anstosszeiten = get_possible_kickofftime($main_datum);
    $spiel = get_times($spieltag, $modus);

    
    echo "<table border = \"1\" align = \"center\" width = \"100%\">";
    
    ## Tag anzeigen
    echo "<tr>";
    echo "<td></td>";
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
    $sql = "SELECT sp_nr, t1.team_name AS Team_name".$modus[0].", t2.team_name AS Team_name".$modus[1].",sp_nr
            FROM `Spieltage`,Teams t1, Teams t2
            WHERE (`spieltag` = $spieltag) AND (t1.team_nr = team1) AND (t2.team_nr = team2)
            ORDER BY sp_nr ASC";
    
    
    foreach ($g_pdo->query($sql) as $row) {
        echo "<tr>";
        
        $sp_nr   = $row['sp_nr'];
        $db_time = $spiel[$sp_nr];
        
        echo "<td align = \"center\">".$row['Team_name1']."-".$row['Team_name2']."</td>";
        echo print_game_row($main_datum, $sp_nr, $db_time, $anstosszeiten);
        
        echo "</tr>";
    }
    
    echo "</table>";
    
}


function print_game_row($main_datum, $sp_nr, $time, $anstosszeiten){
    ## Schreibt die "Zeilen" der "Anstosszeiten-Tabelle" innerhalb des Eingabe Formulars
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


function get_possible_kickofftime($main_datum){
    ## Gibt je nach Kalendertag, an dem der Spieltag beginnt, die möglichen Anstosszeiten zurück!
    
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
    
    return $anstosszeiten;
}


function set_all_spieltag_date(){
    ## Speichert die Anstosszeiten der Spiele ALLER Spieltags
    ## Nutzt dazu die Daten aus OpenligaDB
    $ret = "";
    $global_error = false;
    for ($i = akt_spieltag(); $i <= 34; $i++){
        list($error, $msg) = set_spieltag_date($i);
        if ($error){
            $global_error = true;
        }
        $ret .= $msg;
    }
    
    return array($global_error, $ret);    
}

function set_spieltag_date($spieltag) {
    ## Speichert die Anstosszeiten der Spiele des Spieltags
    ## Nutzt dazu die Daten aus OpenligaDB
    global $g_pdo;
    $ret =  "Spieltag $spieltag: ";
    $error = false;
    $counter = 0;
    
    // Hole Spieltag aus DB
    $jahr = substr(get_wettbewerb_jahr(get_curr_wett()), 0, 4);
    $modus = get_openliga_shortcut(get_curr_wett());
    $my_spiele = get_open_db_spieltag($modus, $jahr, $spieltag);
    
    // Mapping von open_db_name zu team_nr abrufen
    $team_mapping = [];
    $stmt = $g_pdo->query("SELECT open_db_name, team_nr FROM Teams");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $team_mapping[$row['open_db_name']] = $row['team_nr'];
    }
    
    // Bei Rückrunde Heim/Aus Team tauschen
    // Aber nur in der BuLi!!
    if (($spieltag >= 18) && (get_wettbewerb_code(get_curr_wett()) == "BuLi")){
        $aktueller_spieltag = $spieltag - 17;
        $column = "datum2";
    } else {
        $aktueller_spieltag = $spieltag;
        $column = "datum1";
    }
    
    // Wenn alle Spiele gleichzeitig anfangen, nichts updaten
    // (Dann ist der Spieltag noch nicht terminiert..)
    $first_match_time = $my_spiele[0]["matchDateTime"];
    if (array_reduce($my_spiele, fn($same, $spiel) => $same && ($spiel["matchDateTime"] == $first_match_time), true)) {
        $ret .= "Der Spieltag wurde noch nicht terminiert! <br>";
        return array($error, ""); 
    }
    
    foreach ($my_spiele as $spiel) {
        $team1_name = $spiel["team1"]["teamName"];
        $team2_name = $spiel["team2"]["teamName"];
        
        // Setze die Sekunden auf 59
        $spiel_timestamp = strtotime($spiel["matchDateTime"]) - date("s", strtotime($spiel["matchDateTime"])) + 59;
        
        if (isset($team_mapping[$team1_name]) && isset($team_mapping[$team2_name])) {
            $team1_id = $team_mapping[$team1_name];
            $team2_id = $team_mapping[$team2_name];
            
            if (($spieltag >= 18) && (get_wettbewerb_code(get_curr_wett()) == "BuLi")) {
                // In der BuLi Rückrunde: Reihenfolge tauschen!
                list($team1_id, $team2_id) = [$team2_id, $team1_id];
            }
            
            // Datum Updaten!
            $stmt = $g_pdo->prepare("UPDATE Spieltage SET $column = :datum WHERE spieltag = :spieltag AND team1 = :team1 AND team2 = :team2");
            $stmt->execute(['datum' => $spiel_timestamp, 'spieltag' => $aktueller_spieltag, 'team1' => $team1_id, 'team2' => $team2_id]);
            if ($stmt){
                $counter++;
            } else { 
                $error = true;
            }
        }
    }
    if ($error){
        $ret .= "Es gab mindestens einen Fehler beim Speichern..<br>";
    } else {
        $ret .= "Es wurden $counter Spiele erfolgreich gespeichert!<br>";
    }
    
    return array($error, $ret);
} 

?>
