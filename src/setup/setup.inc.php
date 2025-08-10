<?php
require_once("src/include/code/get_games.inc.php");

function read_gameplan_from_openligaDB($modus, $jahr, $spieltag = ""){
    ## Liest den Spielplan von OpenLigaDB ein
    ## Gibt Array mit Spieltagen, Spielen, etc. zurück
    $matches = get_open_db_spieltag($modus, $jahr, $spieltag);
    $sp_nr = 1;
    foreach ($matches as $match) {
        $spt = explode(".", $match["group"]["groupName"])[0];
        
        $ret_matches[$spt][$sp_nr][0] = $match["team1"]["teamName"];
        $ret_matches[$spt][$sp_nr][1] = $match["team2"]["teamName"];
        $ret_matches[$spt][$sp_nr][2] = $match["matchDateTime"];
        
        if ($sp_nr == 9){
            $sp_nr = 1;
        } else {
            $sp_nr += 1;
        }
    }
    return $ret_matches;
}


function read_gameplan_from_file($src){
    $lines = file($src);

    for($i=0; $i < count($lines); $i++){
        $split = explode(",", $lines[$i]);
        if (count($split) > 1){
            ## Einträge haben mehr als 1 Spalte
            if (is_date($split[0])){
                ## Die erste Spalte muss ein Datum enthalten
                if (is_numeric($split[2])){
                    ## Spalte 3 muss eine Zahl sein (Spieltag)
                    if ($split[2] <= 17){
                        ## Wir brauchen nur die Spieltage der Hinrunde für die DB
                        $spt = $split[2];
                        $sp_nr = ($split[3]-1)%9+1;

                        $matches[$spt][$sp_nr][0] = remove_last_blank($split[4]);
                        $matches[$spt][$sp_nr][1] = remove_last_blank($split[5]);
                        $matches[$spt][$sp_nr][2] = remove_last_blank($split[0]);
                    }
                }
            }
        }
    }
    return $matches;
}


function remove_last_blank($string){
    ## Entfernt das letzte Leerzeichen eines Strings
    if (substr($string, -1, 1) == " "){
        $string = substr($string, 0, -1);
    }
    return mb_convert_encoding($string, 'utf-8', 'auto');
    
}


function is_date($string){
    ## Prüft ob der String ein Datum ist
    $year_ident = "202";
    if (str_contains($string, $year_ident)) {
        return 1;
    } else {
        return 0;}
    
}


function extract_season_details(){
    global $g_pdo;
    
    #$matches = read_gameplan_from_file("src/Bundesliga_Spielplan_2024_2025.csv");
    $matches = read_gameplan_from_openligaDB("bl1", "2025");
    
    ## Read all teams and names from DB
    $sql = "SELECT `team_nr`, `team_name`, `open_db_name` FROM `Teams` WHERE 1";
    foreach ($g_pdo->query($sql) as $row) {
        $team_nr = $row['team_nr'];
        $team_name = mb_convert_encoding($row['open_db_name'], 'utf-8', 'auto');
        $team_from_nr[$team_nr] = $team_name;
        $nr_from_team[$team_name] = $team_nr;
    }
    
    ## create output arrays
    $season_teams = array();
    $season_dates = array();
    $season_matches = array();
    
    ## Go through all gamedays
    foreach ($matches as $spieltag => $submatches){
        $start_datum = "3100-01-01T00:00:00";
        $unterminiert = true;
        $season_matches[$spieltag] = array();
        
        ## Go through all matches
        foreach ($submatches as $sp_nr => $teams){
            $team1 = $teams[0];
            $team2 = $teams[1];
            $datum = $teams[2];
            
            ## Check for smallest date
            $dt1 = new DateTime($datum);
            $dt2 = new DateTime($start_datum);
            if ($dt1 < $dt2){
                $start_datum = $datum;
            }
            
            ## If all dates are the same => gameday not terminated
            if ($start_datum != $datum){
                $unterminiert = false;
            }
            
            ## Add match to output array
            $season_matches[$spieltag][$sp_nr] = array($nr_from_team[$team1], $nr_from_team[$team2]);
            
            ## Add Team to team list
            if (!in_array($nr_from_team[$team1], $season_teams, true)){
                array_push($season_teams, $nr_from_team[$team1]);
            }
        }
        
        ## Smallest date is the beginning of gameday
        $season_dates[$spieltag] = array($start_datum, $unterminiert);
    }
    
    return array($season_teams, $season_dates, $season_matches);
}


function create_table($teams){
    ## Fügt die Teams in die Tabelle ein, damit die Tabelle nicht leer ist und Fehler erzeugt
    global $g_pdo;
    
    $html = "<table class=\"table\">";
    foreach ($teams as $team_nr){

        $html .= "<tr>";
        $html .= "<td>".$team_nr."</td>";
        $html .= "<td>".get_team_name($team_nr)."</td>";
        $html .= "</tr>";
        
        $stmt = $g_pdo->prepare("
        INSERT INTO `Tabelle`
            (`team_nr`, `sieg`, `unentschieden`, `niederlage`, `punkte`, `tore`, `gegentore`, `heim`, `spieltag`, `platz`)
        VALUES
            (:id, 0, 0, 0, 0, 0, 0, 0, 1, 0),
            (:id, 0, 0, 0, 0, 0, 0, 0, 18, 0),
            (:id, 0, 0, 0, 0, 0, 0, 1, 2, 0)
        ON DUPLICATE KEY UPDATE
            `sieg` = VALUES(`sieg`),
            `unentschieden` = VALUES(`unentschieden`),
            `niederlage` = VALUES(`niederlage`),
            `punkte` = VALUES(`punkte`),
            `tore` = VALUES(`tore`),
            `gegentore` = VALUES(`gegentore`),
            `heim` = VALUES(`heim`),
            `platz` = VALUES(`platz`);
        ");

    $params = array('id' => $team_nr);
    $stmt->execute($params);   

    }
    
    $html .= "</table>";
    
    return array($stmt, $html);
    
}


function create_all_spieltag_dates($season_dates){
    ## Geht durch alle Spieltage und legt das Start Datum fest
    $html = "<table class=\"table\">";
    $html .= "<tr><td>Spieltag</td><td>Datum</td><td>Term.</td><td>DB</td></tr>";
    $erfolg = true;
    foreach($season_dates as $spieltag => $value){
        list ($date, $unterminiert) =  $value;
        $succ = set_spieltag_date($spieltag, $date, $unterminiert);
        
        if (!$succ){
            $erfolg = false;
        }
        
        $html .= "<tr>";
        $html .= "<td>$spieltag</td>";
        $html .= "<td>$date</td>";
        if (!$unterminiert) {
            $icon = '<i class="fas fa-check text-success"></i>'; // grüner Haken
        } else {
            $icon = '<i class="fas fa-times text-danger"></i>';   // rotes X
        }
        $html .= "<td>{$icon}</td>";
        
        
        if ($succ) {
            $icon = '<i class="fas fa-check text-success"></i>'; // grüner Haken
        } else {
            $icon = '<i class="fas fa-times text-danger"></i>';   // rotes X
        }
        $html .= "<td>{$icon}</td>";
        $html .= "</tr>";
        
    }
    $html .= "</table>";
    
    return array($erfolg, $html);
}


function set_spieltag_date($spieltag, $datum, $unterminiert){
    ## Gibt das Start Datum für einen Spieltag in die DB ein
    global $g_pdo;
    
    $dt = new DateTime($datum);
    if (($unterminiert) && ($dt->format('N') == 6) && ($spieltag != 34)){
        $abzug = 24*60*60;
    } else {
        $abzug = 0;
    }
    
    $datum = strtotime($datum) - $abzug;
    $datum = date('Y-m-dT00:00:59', $datum);
    $datum = strtotime($datum);
    
    $stmt = $g_pdo->prepare("
        INSERT INTO `Datum`(`spieltag`, `datum`) 
        VALUES (:spieltag, :datum)
        ON DUPLICATE KEY UPDATE datum = :datum");

    $params = array('spieltag' => $spieltag, 'datum' => $datum);
    
    return $stmt->execute($params); 
}


function create_all_game_pairings($season_dates){
    ## Geht durch alle Spieltage und erstellt alle Spiel Paarungen
    $erfolg = true;
    $html = "";
    foreach($season_dates as $spieltag => $value1){
        $html .= "<b>Spieltag $spieltag</b>";
        $html .= "<table class=\"table\">";
        foreach($value1 as $sp_nr => $value){
            list($team1, $team2) = $value;
            $succ = create_game_pairing($spieltag, $sp_nr, $team1, $team2);
            
            if ($succ === false){
                $erfolg = false;
            }
            
            $html .= "<tr>";
            $html .= "<td>$sp_nr</td>";
            $html .= "<td>".get_team_name($team1)."</td>";
            $html .= "<td>".get_team_name($team2)."</td>";
            
            if ($succ === true) {
                $icon = '<i class="fas fa-check text-success"></i>'; // grüner Haken
            } elseif ($succ === false) {
                $icon = '<i class="fas fa-times text-danger"></i>';  // rotes X
            } else {
                $icon = '<i class="fas fa-minus text-secondary"></i>'; // neutrales Minus
            }
            
            $html .= "<td>{$icon}</td>";
            
            $html .= "</tr>";
        }
        $html .= "</table>";
    }
    
    return array($erfolg, $html);
}


function create_game_pairing($spieltag, $sp_nr, $team1, $team2){
    ## Gibt Spiel Paarung in DB ein
    global $g_pdo;
    
    if ($spieltag > 17){
        return null;
    }
    $stmt = $g_pdo->prepare("
        INSERT INTO `Spieltage` (`spieltag`, `sp_nr`, `team1`, `team2`) 
        VALUES (:spieltag, :sp_nr, :team1, :team2)
        ON DUPLICATE KEY UPDATE 
            team1 = VALUES(team1),
            team2 = VALUES(team2);");
    
    $params = array('spieltag' => $spieltag,  'sp_nr' => $sp_nr, 'team1' => $team1, 'team2' => $team2);
    
    return $stmt->execute($params);
}


function print_all_teams(){
    ## Gibt Tabelle mit allen Teams aus
    list($team_name, $open_db_name, $city, $stadium) = get_all_teams();
    echo "<table class=\"table\">";
    foreach($team_name as $team_nr => $value){
        echo "<tr>";
        echo "<td>".$team_nr."</td>";
        echo "<td>".$team_name[$team_nr]."</td>";
        echo "<td>".$open_db_name[$team_nr]."</td>";
        echo "<td>".$city[$team_nr]."</td>";
        echo "<td>".$stadium[$team_nr]."</td>";

        echo "</tr>";
    }
    echo "</table>";
}


function add_team_to_db(){
    ## Formular zum hinzufügen eines Teams
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $team_name      = $_POST['team_name']     ?? null;
        $open_db_name   = $_POST['open_db_name']  ?? null;
        $city           = $_POST['city']          ?? null;
        $stadium        = $_POST['stadium']       ?? null;

        if (
            $team_name      !== null &&
            $open_db_name   !== null &&
            $city           !== null &&
            $stadium        !== null 
        ) {
            $success = add_team($team_name, $open_db_name, $city, $stadium);
            if ($success) {
                $messages = "<div class='alert alert-success'>Eintrag erfolgreich gespeichert.</div>";
            } else {
                $messages = "<div class='alert alert-danger'>Fehler beim Speichern des Eintrags.</div>";
            }
            
            echo $messages;
        }
    }
    
    echo '
    <form method="post">
        <div class="row mb-3 align-items-center">
            <label for="team_name" class="col-sm-2 col-form-label">Team Name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="team_name" name="team_name" required>
            </div>
        </div>
        
        <div class="row mb-3 align-items-center">
            <label for="open_db_name" class="col-sm-2 col-form-label">Open DB Name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="open_db_name" name="open_db_name" required>
            </div>
        </div>
        
        <div class="row mb-3 align-items-center">
            <label for="city" class="col-sm-2 col-form-label">City</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="city" name="city" required>
            </div>
        </div>
        
        <div class="row mb-3 align-items-center">
            <label for="stadium" class="col-sm-2 col-form-label">Stadium</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="stadium" name="stadium" required>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Speichern</button>
    </form>';

}


    


function import_sql_file(string $filePath): string {
    ## Importiert SQL Datei in DB
    global $g_pdo;

    if (!file_exists($filePath) || !is_readable($filePath)) {
        return "Fehler: SQL-Datei nicht gefunden oder nicht lesbar: $filePath";
    }

    $sql = file_get_contents($filePath);
    if ($sql === false || trim($sql) === '') {
        return "Fehler: SQL-Datei ist leer oder konnte nicht gelesen werden.";
    }

    // DEFINER-Klauseln entfernen
    $sql = preg_replace('/DEFINER\s*=\s*`[^`]+`\s*@\s*`[^`]+`/i', '', $sql);

    // START TRANSACTION / COMMIT entfernen
    $sql = preg_replace('/\bSTART\s+TRANSACTION\b;?/i', '', $sql);
    $sql = preg_replace('/\bCOMMIT\b;?/i', '', $sql);

    // SQL-Statements aufteilen
    $statements = array_filter(array_map('trim', preg_split('/;\s*[\r\n]+/m', $sql)));

    try {
        if (!$g_pdo->inTransaction()) {
            $g_pdo->beginTransaction();
        }

        foreach ($statements as $stmt) {
            if ($stmt !== '') {
                $g_pdo->exec($stmt);
            }
        }

        if ($g_pdo->inTransaction()) {
            $g_pdo->commit();
        }

        return "Erfolg: Die SQL-Datei <code>" . basename($filePath) . "</code> wurde erfolgreich importiert.";
    } catch (PDOException $e) {
        if ($g_pdo->inTransaction()) {
            $g_pdo->rollBack();
        }
        return "Fehler beim Import: " . htmlspecialchars($e->getMessage());
    }
}
