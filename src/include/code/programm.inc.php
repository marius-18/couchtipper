<?php

function programm($team_nr, $start, $ende) {
    /// Gibt die Spiele der Mannschaft mit $team_nr zwischen Spieltagen $start und $ende zurück
    global $g_pdo;
    
    if ($start <= 17){
        $sql = "
                SELECT spieltag, team1, team2, t1.team_name as name1, t2.team_name as name2, datum1, datum2 
                FROM `Spieltage`, Teams t1, Teams t2 
                WHERE ((team1 = '$team_nr' OR team2 = '$team_nr') AND (spieltag >= $start) AND (spieltag <= $ende) 
                AND (t1.team_nr = team1) AND (t2.team_nr = team2))";
        foreach ($g_pdo->query($sql) as $row) {
            $spieltag = $row['spieltag'];
            $team_nr1[$spieltag] = $row['team1'];
            $team_nr2[$spieltag] = $row['team2'];
            $datum[$spieltag] = $row['datum1'];
            if ($team_nr1[$spieltag] == $team_nr) {
                $team_name[$spieltag] = $row['name2'];
            } else {
                $team_name[$spieltag] = $row['name1'];
            }
        }
    }
    
    
    if ($ende > 17){
        
        $ende = $ende - 17;
        $start = $start-17;
        
        $sql = "
                SELECT spieltag, team1, team2, t1.team_name as name1, t2.team_name as name2, datum1, datum2 
                FROM `Spieltage`, Teams t1, Teams t2 
                WHERE ((team1 = '$team_nr' OR team2 = '$team_nr') AND (spieltag <= $ende) AND (spieltag >= $start) 
                AND (t1.team_nr = team1) AND (t2.team_nr = team2))";
        
        foreach ($g_pdo->query($sql) as $row) {
            $spieltag = $row['spieltag'] + 17;
            $team_nr1[$spieltag] = $row['team2'];
            $team_nr2[$spieltag] = $row['team1'];
            $datum[$spieltag] = $row['datum2'];
            
            if ($team_nr1[$spieltag] == $team_nr) {
                $team_name[$spieltag] = $row['name1'];
            } else {
                $team_name[$spieltag] = $row['name2'];
            }
        }
    }
    
    
    $sql = "
            SELECT Ergebnisse.spieltag, tore1, tore2 
            FROM `Spieltage`, Ergebnisse 
            WHERE (((Spieltage.spieltag = Ergebnisse.spieltag - 17) OR (Spieltage.spieltag = Ergebnisse.spieltag))
            AND (Spieltage.sp_nr = Ergebnisse.sp_nr) AND (team1 = $team_nr OR team2 = $team_nr))";
    
    foreach ($g_pdo->query($sql) as $row) {
        $spieltag = $row['spieltag'];
        $tore1[$spieltag] = $row['tore1'];
        $tore2[$spieltag] = $row['tore2'];
    }
    
    $zeitraum = get_zeitraum_of_all_spt();
    
    return array($spieltag, $team_nr1, $team_nr2, $team_name, $datum, $zeitraum, $tore1, $tore2);
}



function print_programm($spieltag, $team_nr1, $team_nr2, $team_name, $datum, $zeitraum, $tore1, $tore2, $start, $ende, $id, $status){

    if (!$status){
        $visible = "style=\"display:none\"";
    } else{
        $visible = "";
    }
    
    // Gruppierung, damit ausgeblendet werden kann!
    echo "<tbody id=\"$id\" $visible>";

    
    foreach ($team_nr1 as $spt => $team1) {

        if ($spt < $start){
            // Wenn unser Spieltag noch nicht gewünscht ist => Überspringen
            continue;
        }
        
        if ($spt > $ende){
            // Wenn wir zu weit sind => Schleife abbrechen
            break;
        }

        if ($spt == 18){
            // Am Übergang wird die Rückrunden-Zeile eingeblendet!
            echo "<tr class=\"table-active\"> <td colspan = \"4\"><span style = \" font-size:150%\"><b>R&uuml;ckrunde</b></span></td></tr>";
        }
        
        if (!isset($tore1[$spt]) || ($tore1[$spt] == "")) {
            $ergebnis =  "- : -";
        } else {
            $ergebnis = $tore1[$spt]." : ".$tore2[$spt];
        }
        
        if ($datum[$spt] != 0) {
            $spielzeit = stamp_to_date_programm($datum[$spt]);
        } else { 
            // Der Spieltag ist noch nicht genau Terminiert. Also wird hier das Wochenende (bzw. englische Woche angezeigt)
            $spielzeit = "<i>".print_interval_not_scheduled("program",$zeitraum[$spt][0], $zeitraum[$spt][1])."</i>";
        }
        
        
        echo " <tr>
                <td><b>$spt.</b> Spieltag</td>
                
                <td rowspan = \"3\" class=\"align-middle\">  <img src = \"/images/Vereine/$team1.gif\"  width = \"50\"></td>
                <td rowspan = \"3\" class=\"align-middle\">  <span style = \" font-size:200%\"><b>$ergebnis</b></span></td>
                <td rowspan = \"3\" class=\"align-middle\">  <img src = \"/images/Vereine/".$team_nr2[$spt].".gif\" width = \"50\"></td>
               </tr>

               <tr>
                <td>&nbsp;&nbsp;<b>".$team_name[$spt]."</b></td>
               </tr>
               
               <tr>
                <td>$spielzeit</td>
               </tr>
            ";

    }

    echo  "</tbody>";
}


function tournament_programm($team_nr, $modus){
    global $g_pdo;
    
    if ($modus == "Team"){
        ## Zeige nur die Spiele des Teams und zusätzlich alle nicht terminierten KO Spiele
        $sql_zusatz = "(team1 = '$team_nr' OR team2 = '$team_nr' OR team1 = '0' OR team2 = '0') AND";
    } else {
        $sql_zusatz = "";
    }
    
    ## Erstmal alle Spiele von dem entsprechenden Team 
    $sql = "SELECT spieltag, sp_nr, team1, team2, t1.team_name as name1, t2.team_name as name2, datum1, stadt, stadion
        FROM `Spieltage`, Teams t1, Teams t2, Spielorte
        WHERE ($sql_zusatz (t1.team_nr = team1) 
        AND (t2.team_nr = team2)
        AND (Spielorte.id = spielort))";
    
    foreach ($g_pdo->query($sql) as $row) {
        $spieltag = $row['spieltag'];
        $key = $spieltag . "-" . $row['sp_nr'];
        $team_nr1[$key] = $row['team1'];
        $team_nr2[$key] = $row['team2'];
        $datum[$key] = $row['datum1'];
        $zeitraum[$key] = "";
        $stadt[$key] = $row['stadt'];
        $stadion[$key] = $row['stadion'];
        
        if (($row['team1'] == 0) || ($row['team1'] == 0)){
            $team_name[$key] = get_final_name($key);
        } else {
            $team_name[$key] = $row['name1'] . " - " . $row['name2'];
        }
    }
    
    return array($spieltag, $team_nr1, $team_nr2, $team_name, $datum, $zeitraum, $stadt, $stadion);
}

function get_final_name($spieltag){
    ## TODO: das muss man dann anpassen
    $finals = array();
    $finals["14-1"] = "Achtelfinale 1";
    $finals["15-1"] = "Achtelfinale 3";
    $finals["16-1"] = "Achtelfinale 5";
    $finals["17-1"] = "Achtelfinale 7";
    $finals["14-2"] = "Achtelfinale 2";
    $finals["15-2"] = "Achtelfinale 4";
    $finals["16-2"] = "Achtelfinale 6";
    $finals["17-2"] = "Achtelfinale 8";

    
    $finals["18-1"] = "Viertelfinale 1";
    $finals["19-1"] = "Viertelfinale 3";

    $finals["18-2"] = "Viertelfinale 2";
    $finals["19-2"] = "Viertelfinale 4";

    
    $finals["20-1"] = "Halbfinale 1";
    $finals["21-1"] = "Halbfinale 2";
    
    $finals["22-1"] = "Finale";
    
    return $finals[$spieltag];
}

?>
