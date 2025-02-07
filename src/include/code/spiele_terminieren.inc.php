<?php

function set_all_spieltag_date(){
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
