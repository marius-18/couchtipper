<?php
/*
// Holt Spiele und Ergebnisse aus der Datenbank
*/
//require_once('time.inc.php');



//$modus =  "Spieltag", "Tipps", "Ergebnisse"

function get_games ($spieltag, $modus, $change, $user_nr) {
    global $g_pdo;
    global $g_modus;
    // Hinrunde / Rückrunde
    $teil1 = "1";
    $teil2 = "2";
    $real_spieltag = $spieltag;
    $add = "0";

    if (($spieltag > 17) && (get_wettbewerb_code(get_curr_wett()) == "BuLi")){
        $teil1 = "2";
        $teil2 = "1";
        $spieltag = $spieltag - 17;
    }

    $sql = "SELECT COUNT(sp_nr) AS anz FROM `Spieltage` WHERE spieltag = $spieltag";
    foreach ($g_pdo->query($sql) as $row) {
        $anz_spiele =  $row['anz'];
    }


    // Initialisiere Arrays wegen sort()
    for ($i=1; $i<= $anz_spiele; $i++){
        $tore_heim[$i] = "";
        $tore_aus[$i] = "";
    } 


    if (get_wettbewerb_code(get_curr_wett()) == "BuLi"){
        $sql = "SELECT spieltag, sp_nr, t1.team_name AS Team_name$teil1, t2.team_name AS Team_name$teil2, 
            datum$teil1 AS datum, t1.team_nr AS Team_nr$teil1, t2.team_nr AS Team_nr$teil2, t$teil1.stadium AS stadion, t$teil1.city AS stadt
            FROM Spieltage,Teams t1, Teams t2
            WHERE (spieltag = $spieltag) AND (t1.team_nr = team1) AND (t2.team_nr = team2)";
    } else {
        $sql = "SELECT spieltag, sp_nr, t1.team_name AS Team_name$teil1, t2.team_name AS Team_name$teil2, 
            datum$teil1 AS datum, t1.team_nr AS Team_nr$teil1, t2.team_nr AS Team_nr$teil2, stadion, stadt
            FROM Spieltage,Teams t1, Teams t2, Spielorte
            WHERE (spieltag = $spieltag) AND (t1.team_nr = team1) AND (t2.team_nr = team2) AND (Spielorte.id=spielort)";
    }

    foreach ($g_pdo->query($sql) as $row) {
        $sp_nr = $row['sp_nr'];
        $team_heim [$sp_nr] = $row['Team_name1'];
        $team_aus [$sp_nr] = $row['Team_name2'];
        $datum [$sp_nr] = $row['datum'];
        $team_heim_nr [$sp_nr] = $row['Team_nr1'];
        $team_aus_nr [$sp_nr] = $row['Team_nr2'];
        $real_sp_nr [$sp_nr] = $sp_nr;
        $stadion[$sp_nr] = $row['stadion'];
        $stadt[$sp_nr] = $row['stadt'];
        
    }


    if ($modus == "Tipps") { // HIER MUSS NOCH DIE USERNR eingebene werden
        $sql = "SELECT sp_nr, tore1, tore2 FROM Tipps
                WHERE ((spieltag = $real_spieltag) AND (user_nr = $user_nr))";
    } else { 

        if (($modus == "Ergebnisse") || ($modus == "Spieltag")) {
            $sql = "SELECT sp_nr, tore1, tore2 FROM Ergebnisse
                    WHERE ((spieltag = $real_spieltag))";
        } else {
            if ($modus == "Wm_Tabelle"){ // Wozu?!
                $sql ="";
            }
        }
    }


    foreach ($g_pdo->query($sql) as $row) {
        $sp_nr = $row['sp_nr'];
        $tore_heim [$sp_nr] = $row['tore1'];
        $tore_aus [$sp_nr] = $row['tore2'];


        // falls eine Änderung gewählt wurde, Ergebnisse in Textfelder schreiben
        if (($change) && ($modus != "Spieltag")){
            $help_tore1 = $row['tore1'];
            $help_tore2 = $row['tore2'];


            if ((check_game_date($real_spieltag, $sp_nr) && ($modus == "Tipps") && (get_usernr() != "")) || ((allow_erg()) && ($modus == "Ergebnisse")) || (allow_tipps() && ($modus == "Tipps")) ) {
                $disable = "";
                $farbe = "";
            } else {

                $disable = "disabled";
                $farbe = "background-color: darkgray;";
            }


            $tore_heim[$sp_nr] = "<input type=\"number\" name=\"erg".$sp_nr."1\" max=\"99\" min=\"0\" style=\"width: 40%; $farbe\" value =\"$help_tore1\" $disable>";
            $tore_aus [$sp_nr] = "<input type=\"number\" name=\"erg".$sp_nr."2\" max=\"99\" min=\"0\" style=\"width: 40%; $farbe\" value =\"$help_tore2\" $disable>";
        }

    }



    // falls keine Tore eingetragen sind, werden Textfelder angezeigt
    for ($i=1; $i<= $anz_spiele; $i++){
        if ((check_game_date($real_spieltag, $i)  && ($modus == "Tipps") && (get_usernr() != "")) || ((allow_erg()) && ($modus == "Ergebnisse")) || (allow_tipps() && ($modus == "Tipps"))   ) {
            // sperrt nummernfelder wenn zu spät // VLLT ZÄHLER wenn keine spiele verfügbar ?

            $disable = "";
            $farbe = "";
        } else {
            $disable = "disabled";
            $farbe = "background-color: darkgray;";
        } 


        if (($tore_heim[$i] == "") || ($tore_aus[$i] == "")){
            if (($modus == "Tipps") || ($modus == "Ergebnisse")) {
                $tore_heim[$i] = "<input type=\"number\" name=\"erg".$i."1\" size=\"2\" max=\"99\" min=\"0\" style=\"width: 40%; $farbe\" $disable>";
                $tore_aus [$i] = "<input type=\"number\" name=\"erg".$i."2\" size=\"2\" max=\"99\" min=\"0\" style=\"width: 40%; $farbe\" $disable>";
                $aenderung = true; // HM WAS IS DAS
            } else {
                if ($modus == "Spieltag"){
                    $tore_heim[$i] = " - ";
                    $tore_aus[$i] = " - ";
                }
            }
        }

    }

   array_multisort($datum, SORT_ASC, $team_heim, $team_aus, $tore_heim, $tore_aus, $team_heim_nr, $team_aus_nr, $real_sp_nr, $stadt, $stadion);
    return array($datum, $team_heim, $team_aus, $tore_heim, $tore_aus, $team_heim_nr, $team_aus_nr, $real_sp_nr, $real_spieltag, $anz_spiele, $stadt, $stadion);
### letzter parameter: $gruppe.. wofür? vllt für WM/EM?
// warum nicht gleich ausgeben ?
// Wegen mobil/desktop/ipad?


}

function get_open_db_spieltag($modus, $jahr, $spieltag){
    
    if (get_wettbewerb_code(get_curr_wett())  == "EM"){
        switch ($spieltag) {
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
                $opl_spieltag = 1;
                break;
            case 6:
            case 7:
            case 8:
            case 9:
                $opl_spieltag = 2;
                break;
            case 10:
            case 11:
            case 12:
            case 13:
                $opl_spieltag = 3;
                break;
            case 14:
            case 15:
            case 16:
            case 17:
                $opl_spieltag = 4;
                break;
            case 18:
            case 19:
                $opl_spieltag = 5;
                break;
            case 20:
            case 21:
                $opl_spieltag = 6;
                break;
            case 22:
                $opl_spieltag = 7;
                break;
        }
        #$matches = get_open_db_spieltag(get_openliga_shortcut(get_curr_wett()), $jahr, $opl_spieltag);
        $spieltag = $opl_spieltag;
    } elseif (get_wettbewerb_code(get_curr_wett())  == "WM") {
        switch ($spieltag) {
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:   
            case 10:
            case 11:
            case 12:
            case 13:  
            case 14:
            case 15:
                $opl_spieltag = 1;
                break;
            case 16:
            case 17:
            case 18:
            case 19:
                $opl_spieltag = 2;
                break;
            case 20:
            case 21:
                $opl_spieltag = 3;
                break;
            case 22:
            case 23:
                $opl_spieltag = 4;
                break;
            case 24:
                $opl_spieltag = 5;
                break;
            case 25:
                $opl_spieltag = 6;
                break;
        }
        $spieltag = $opl_spieltag;
        #$matches = get_open_db_spieltag(get_openliga_shortcut(get_curr_wett()), $jahr, $opl_spieltag);
        #print_r($matches);
    }
    
    #$url = "https://www.openligadb.de/api/getmatchdata/$modus/$jahr/$spieltag";
    $url = "https://api.openligadb.de/getmatchdata/$modus/$jahr/$spieltag";
    #echo $url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $output = curl_exec($ch);
    curl_close($ch);

    $matches = json_decode($output, true);
    
    return $matches;
}


function get_ergebnis($spieltag,$modus, $jahr){
    global $g_pdo;
    //TODO: DB Abfrage je nach modus.. Funktioniert hier bisher nur für buli!
    $matches = get_open_db_spieltag($modus, $jahr, $spieltag);
    
    
    if (($spieltag <= 17) || (is_big_tournament(get_curr_wett()))) {
        $heim = "Heim";
        $aus = "Aus";
    } else {
        $heim = "Aus";
        $aus = "Heim";
        $spieltag = $spieltag - 17;
    }
    $sql = "SELECT team1, team2, TeamHeim.open_db_name as $heim, TeamAus.open_db_name as $aus, sp_nr
            FROM `Spieltage`, Teams as TeamHeim, Teams as TeamAus 
            WHERE (spieltag = $spieltag) and (team1 = TeamHeim.team_nr) and (team2 = TeamAus.team_nr)";
    foreach ($g_pdo->query($sql) as $row) {
        $team1 = $row['Heim'];
        $team2 = $row['Aus'];
        $sp_nr = $row['sp_nr'];
        #echo "$team1 - $team2";

        foreach ($matches as $match) {
            #echo "$team1 - $team2 und ";
            #echo $match["team1"]["teamName"] . " - " . $match["team2"]["teamName"];
            #echo "<br><br>";
            if ((!strnatcmp($match["team1"]["teamName"], $team1) and !strcmp($match["team2"]["teamName"], $team2))
            or (!strnatcmp($match["team1"]["teamName"], $team2) and !strcmp($match["team2"]["teamName"], $team1))){
                // Das ist das aktuelle Spiel!
                $tore_heim[$sp_nr] = $match["matchResults"][1]["pointsTeam1"];
                $tore_aus[$sp_nr]  = $match["matchResults"][1]["pointsTeam2"];
        
                echo "Spiel $sp_nr ";
                echo "$team1 ".$tore_heim[$sp_nr]." - ".$tore_aus[$sp_nr]." $team2";
                echo "<br>";

            }
        }
        
    }
    
    return array($tore_heim, $tore_aus);
}


function get_tore($spieltag, $modus){
    #TODO: ordentlich machen, wozu $modus??
    global $g_pdo;
    #return 0;
    // DB Abfrage je nach modus.. Hier bisher nur buli!
    if (get_curr_wett()[0] <= -6){
        ## Für die alten muss noch die datenbank namen und vereinsnamen rein
        return array("","","","","","","","","");
    }
    $jahr = substr(get_wettbewerb_jahr(get_curr_wett()), 0, 4);
    $heim = "Heim";
    $aus = "Aus";
    
    $matches = get_open_db_spieltag(get_openliga_shortcut(get_curr_wett()), $jahr, $spieltag);

    
    if (($spieltag >= 18) && !(is_big_tournament(get_curr_wett()))) {
        $heim = "Aus";
        $aus = "Heim";
        $spieltag = $spieltag - 17;
    }
    
    $sql = "SELECT team1, team2, TeamHeim.open_db_name as $heim, TeamAus.open_db_name as $aus, sp_nr
            FROM `Spieltage`, Teams as TeamHeim, Teams as TeamAus 
            WHERE (spieltag = $spieltag) and (team1 = TeamHeim.team_nr) and (team2 = TeamAus.team_nr)";
    foreach ($g_pdo->query($sql) as $row) {
        $sp_nr = $row['sp_nr'];
        $team1[$sp_nr] = $row['Heim'];
        $team2[$sp_nr] = $row['Aus'];
        $spiel[$row['Heim']] = $sp_nr;
        $spiel[$row['Aus']] = $sp_nr;
        
    }
    $ret = array();
    foreach ($matches as $match) {
 #       print_r($match);
        if (isset($spiel[$match["team1"]["teamName"]]) && isset($spiel[$match["team2"]["teamName"]])) {
#print_r($match["team1"]["teamName"]);
#print_r($match["team2"]["teamName"]);
#print_r($spiel);
#            echo $sp_nr;
#echo "<br><br>";
            $sp_nr = $spiel[$match["team1"]["teamName"]];
            if ($spiel[$match["team1"]["teamName"]] == $spiel[$match["team2"]["teamName"]]) {
        $ret[$sp_nr] = "";
#echo  "UI";
#print_r($match);
#print_r($spiel[$match["team2"]["teamName"]]);
#echo "<br><br>";
        #print_r($match);

// usort($match["goals"], function($a, $b) {
//     if ($a['goalID'] > $b['goalID']) {
//         return 1;
//     } elseif ($a['goalID'] < $b['goalID']) {
//         return -1;
//     }
//     return 0;
// });

usort($match["goals"], function($a, $b) {
    if ($a['matchMinute'] > $b['matchMinute']) {
        return 1;
    } elseif ($a['matchMinute'] < $b['matchMinute']) {
        return -1;
    }
    return 0;
});


//echo "<br><br><br>";
            //print_r($match["goals"]);
// Das ist das aktuelle Spiel!
            $ret[$sp_nr] = "<table align=\"center\">";
            $t1 = 0;
            $t2 = 0;
            foreach ($match["goals"] as $goal){
                $zusatz = "";
                if ($goal["scoreTeam1"] >  $t1) {
                    $ret[$sp_nr] .= "<tr class=\"table-info\">";
                }
                else if ($goal["scoreTeam2"] >  $t2) {
                    $ret[$sp_nr] .= "<tr class=\"table-primary\">";
                } else {
                    $zusatz = "(VAR)";
                }
                
                if ($goal["isPenalty"]){
                    $zusatz = "(11m)";
                }
                if ($goal["isOwnGoal"]){
                    $zusatz = "(ET)";
                }
                if ($goal["isOvertime"]){
                    $match_minute = $goal["matchMinute"];
                    
                    if ($match_minute > 120){
                        $rest = $match_minute - 120;
                        $match_minute = "120'+$rest";
                    } elseif (($match_minute > 90) && ($match_minute < 115)){
                        $rest = $match_minute - 90;
                        $match_minute = "90'+$rest";
                    } elseif (($match_minute > 45) && ($match_minute < 80)){
                        $rest = $match_minute - 45;
                        $match_minute = "45'+$rest";
                    } else{
                        $match_minute .= "'";
                    }

                } else {
                    $match_minute = $goal["matchMinute"]."'";
                }
                $ret[$sp_nr] .= "<td>".$match_minute."</td>";
                $ret[$sp_nr] .= "<td>".$goal["scoreTeam1"]." : ".$goal["scoreTeam2"]."</td>";
                $ret[$sp_nr] .= "<td>".$goal["goalGetterName"]."</td>";
                $ret[$sp_nr] .= "<td>$zusatz</td>";
                $ret[$sp_nr] .= "</tr>";
                
                $t1 = $goal["scoreTeam1"];
                $t2 = $goal["scoreTeam2"];

            }
            $ret[$sp_nr] .= "</table>";

        }
    }
    }
    
    return $ret;
}

function get_other_tipps($spieltag, $modus) {
    global $g_pdo;
    ## TODO: $max SPieltag einführen 
    ## bei Spieltag == 0 geht es zum GruppenModus für Turniere..
    ## Dabei werden alle Spieltage zusammen ausgegeben
    $max_spieltag = 13;

    if ($spieltag == 0){
        $sql = "SELECT tore1, tore2, sp_nr, spieltag
        FROM `Ergebnisse`
        WHERE spieltag <= $max_spieltag";
    } else {
        $sql = "SELECT tore1, tore2, sp_nr, spieltag
        FROM `Ergebnisse`
        WHERE (spieltag = $spieltag)";
    }
    

    foreach ($g_pdo->query($sql) as $row) {
        $sp_nr = $row['sp_nr'];
        $spt   = $row['spieltag'];
        $tore1[$spt][$sp_nr] = $row['tore1'];
        $tore2[$spt][$sp_nr] = $row['tore2'];
        
        if ($tore1[$spt][$sp_nr] == ""){
            $tore1[$spt][$sp_nr] = NULL;
        }
        
        if ($tore2[$spt][$sp_nr] == ""){
            $tore2[$spt][$sp_nr] = NULL;
        }
    }
    
    if ($spieltag == 0){
        $sql = "SELECT Tipps.user_nr AS user_nr, tore1, tore2, sp_nr, spieltag
        FROM `Tipps`
        WHERE  spieltag <= $max_spieltag";
    } else {
        $sql = "SELECT Tipps.user_nr AS user_nr, tore1, tore2, sp_nr, spieltag
        FROM `Tipps`
        WHERE (spieltag = $spieltag)";
    }

    $user_nr = array();
    $user_name = array();
    $tipp = array();
    $vorname = array();
    $nachname = array();
    $punkte = array();
    $user_dict = get_all_username();
    
    $user_nr[$spieltag] = NULL;
    $user_name[$spieltag] = NULL;
    $tipp[$spieltag] = NULL;
    $vorname[$spieltag] = NULL;
    $nachname[$spieltag] = NULL;
    $punkte[$spieltag] = NULL;
    
    foreach ($g_pdo->query($sql) as $row) {
        $i = $row['user_nr'];
        $sp_nr = $row['sp_nr'];
        $spt   = $row['spieltag'];

        if (check_game_date($spt,$sp_nr)){
            continue;
        }
        
        
        $tipp1[$sp_nr] = $row['tore1'];
        $tipp2[$sp_nr] = $row['tore2'];
        
        if (((($modus == "Tipps")) || ( $modus == "Spieltag")) && isset($tore1[$spt][$sp_nr]) && isset($tore2[$spt][$sp_nr])){
            $tipp[$spt][$sp_nr][$i] = $tipp1[$sp_nr]." : ". $tipp2[$sp_nr];
            $user_nr[$spt][$sp_nr][$i] = $i;
            $user_name[$spt][$sp_nr][$i] = $user_dict[$i];#get_username_from_nr($i);
            $vorname[$spt][$sp_nr][$i] = "";
            $nachname[$spt][$sp_nr][$i] = "";
            //$vorname[$sp_nr][$i] = $row['vorname'];
            //$nachname[$sp_nr][$i] = $row['nachname'];

            if (($tore1[$spt][$sp_nr] !== NULL) && ($tore2[$spt][$sp_nr] !== NULL)){
                if (($tore1[$spt][$sp_nr] == $tipp1[$sp_nr]) && ($tore2[$spt][$sp_nr] == $tipp2[$sp_nr])){
                    $punkte[$spt][$sp_nr][$i] = "+3";
                }
                elseif ($tore1[$spt][$sp_nr] - $tore2[$spt][$sp_nr] == $tipp1[$sp_nr] - $tipp2[$sp_nr]){
                    $punkte[$spt][$sp_nr][$i] = "+2";
                }
                elseif ((($tore1[$spt][$sp_nr] - $tore2[$spt][$sp_nr] > 0) && ($tipp1[$sp_nr] - $tipp2[$sp_nr] > 0)) || (($tore1[$spt][$sp_nr] - $tore2[$spt][$sp_nr] < 0) && ($tipp1[$sp_nr] - $tipp2[$sp_nr] < 0)) ){
                    $punkte[$spt][$sp_nr][$i] = "+1";
                }
                else {
                    $punkte[$spt][$sp_nr][$i] = "";
                }
            }
        
        }
    
    }
    
    #if (!check_game_date($spieltag,$sp_nr)){
        #return 0;
        if ($spieltag == 0){
            return array(array($user_nr, $user_name, $tipp, $vorname, $nachname), $punkte);
        } else {
            return array(array($user_nr[$spieltag], $user_name[$spieltag], $tipp[$spieltag], $vorname[$spieltag], $nachname[$spieltag]), $punkte[$spieltag]);
        }
    #}
}


function get_next_games(){
    ### Gibt die Spiele zurück die als nächstes starten
    global $g_pdo;
    list($wett_id, $part) = get_curr_wett();
    if (wettbewerb_has_parts($wett_id)){
        ## TODO: Warum denn +1? soll das so?
        $part += 1;
        $date = "datum$part";
    } else {
        $date = "datum1";   
    }
    
    $time = time();
    $sql = "SELECT sp_nr, $date AS date FROM `Spieltage` WHERE $date = (SELECT min($date) FROM `Spieltage` WHERE $date > $time)";

    foreach ($g_pdo->query($sql) as $row) {
        $sp_nr = $row['sp_nr'];
        $spiel[$sp_nr] = $sp_nr;
        $datum = $row['date'];
    }
    
    return array($spiel, $datum);
}

function get_bot_spiele($user_nr, $next_games, $mode){
    ### $mode == "gameday": Erstellt eine Liste von Spielen des Spieltags
    ### $mode == "Tipps"  : Erstellt selbe Liste und prüft ob Spiele schon getippt wurden.. 
    list($datum, $team_heim, $team_aus, $tore_heim, $tore_aus, $team_heim_nr, $team_aus_nr, $real_sp_nr, $real_spieltag, $anz_spiele, $gruppe) = get_games(akt_spieltag(), "Tipps", 0, $user_nr);
    $spiele = "";
    $fav_team = 12; // fav team zu §user_nr aus DB
    
    if ($mode == "tipps"){
        $tipped = false; // Nur true, wenn mind 1 Spiel noch nicht getippt ist... 
        foreach ($team_heim as $index => $team){
            if (in_array($real_sp_nr[$index], $next_games) and !is_numeric($tore_heim[$index])){
                if (($team_heim_nr[$index] == $fav_team) || ($team_aus_nr[$index] == $fav_team) ){
                    $spiele .= "<b>";
                } 
                $spiele .= $team;
                if (is_numeric($tore_heim[$index])){
                    $spiele .= " " . $tore_heim[$index];
                } else { $tipped = true;}
                        
                $spiele .= " - ";
                if (is_numeric($tore_aus[$index])){
                    $spiele .= $tore_aus[$index] . " ";
                } else { $tipped = true;}
                        
                $spiele .= $team_aus[$index];
                if (($team_heim_nr[$index] == $fav_team) || ($team_aus_nr[$index] == $fav_team) ){
                    $spiele .= "</b>";
                }       
                $spiele .= "\n";
                $time = round(($datum[$index] - time()) / (60*60),1);
                $time .= " Stunden";
            }
                    
        }
    }
    
    if ($mode == "gameday"){
        foreach ($team_heim as $key => $team){
            if (($team_heim_nr[$key] == $fav_team) || ($team_aus_nr[$key] == $fav_team) ){
                $spiele .= "<b>";
            } 
            $spiele .= $team;
            $spiele .= " - ";
            $spiele .= $team_aus[$key];
            if (($team_heim_nr[$key] == $fav_team) || ($team_aus_nr[$key] == $fav_team) ){
                $spiele .= "</b>";
            }       
            $spiele .= "\n";
        }
    }
    
    ## HTML - Codes umwandeln
    $search  = array('&auml;', '&uuml;', '&ouml;');
    $replace = array('ä', 'ü', 'ö');
    $spiele =  str_replace($search, $replace, $spiele);
    
    return array($spiele, $tipped, $time);
                
}


function get_group_games($gruppe){
   global $g_pdo;
//return array(1,1,1,1,1,1);

    $anz_spiele = 6; //Gruppenspiele
    if (get_wettbewerb_code(get_curr_wett())  == "EM"){
        $max_gruppen_spt = 13; // nach dem 13. Spieltag keine Gruppenspiele mehr!
    }
    if (get_wettbewerb_code(get_curr_wett())  == "WM"){
        $max_gruppen_spt = 15; // nach dem 15. Spieltag keine Gruppenspiele mehr!
    }
    
    #   for ($i = 1; $i <= $anz_spiele; $i++){
#      $tore_heim[$i] = "10";
#      $tore_aus[$i] = "111";
#   }

   $sql = "
     SELECT spieltag, sp_nr, t1.team_name AS Team_name1, t2.team_name AS Team_name2, 
     datum1 AS datum, t1.team_nr AS Team_nr1, t2.team_nr AS Team_nr2, Spielorte.stadt AS stadt, Spielorte.stadion AS stadion
     FROM Spieltage,Teams t1, Teams t2, Spielorte
     WHERE (t1.gruppe = '$gruppe') AND (t1.team_nr = team1) AND (t2.team_nr = team2) AND (spieltag<=$max_gruppen_spt)  AND (spielort = Spielorte.id)";

     foreach ($g_pdo->query($sql) as $row) {
      $sp_nr = $row['sp_nr']."-".$row['spieltag'];
      $team_heim [$sp_nr] = $row['Team_name1'];
      $team_aus [$sp_nr] = $row['Team_name2'];
      $datum [$sp_nr] = $row['datum'];
      $real_sp_nr [$sp_nr] = $sp_nr;
      $tore_heim[$sp_nr] = "";
      $tore_aus[$sp_nr] = "";
      $stadt[$sp_nr] = $row["stadt"];
      $stadion[$sp_nr] = $row["stadion"];
   }

   $sql = "
     SELECT Spieltage.sp_nr, tore1, tore2, Spieltage.spieltag
     FROM `Ergebnisse`,Spieltage, Teams 
     WHERE (Ergebnisse.spieltag = Spieltage.spieltag) 
     AND (Ergebnisse.sp_nr = Spieltage.sp_nr) 
     AND (Teams.team_nr = team1)
     AND (gruppe = '$gruppe')
     AND (Spieltage.spieltag<=$max_gruppen_spt)";


   foreach ($g_pdo->query($sql) as $row) {
      $sp_nr = $row['sp_nr']."-".$row['spieltag'];
      $tore_heim [$sp_nr] = $row['tore1'];
      $tore_aus [$sp_nr] = $row['tore2'];
   }

   return array($datum, $team_heim, $team_aus, $tore_heim, $tore_aus, $real_sp_nr, $stadt, $stadion);

}

function get_all_ergebnissse(){
    ## Ursprünglich für Kreuztabelle
    ## Gibt Array mit Ergebnissen zurück
    ## Format: [heim][auswärts] = Ergebnis
    ## Falls Spiel noch nicht begonnen hat ==> Datum
    global $g_pdo;
    
    // Alle Spieltage, erstmal Datum auslesen
    $sql = "SELECT `spieltag`, `sp_nr`, team1, team2, datum1, datum2 FROM `Spieltage` WHERE 1";
    foreach ($g_pdo->query($sql) as $row) {
        $team1 = $row['team1'];
        $team2 = $row['team2'];
        
        $datum1 = $row['datum1'];
        $datum2 = $row['datum2'];
        
        $ergebnisse[$team1][$team2] = "<font size=\"-1\">".stamp_to_day($datum1)."</font>";
        $ergebnisse[$team2][$team1] = "<font size=\"-1\">".stamp_to_day($datum2)."</font>";
    }
    
    // Hinrunde mit Ergebnissen überschreiben
    $sql = "SELECT `Ergebnisse`.`spieltag`, `Spieltage`.`sp_nr`, team1, team2, datum1, tore1, tore2 
            FROM `Spieltage`, `Ergebnisse` 
            WHERE `Ergebnisse`.`spieltag` <= 17
                AND  `Spieltage`.`spieltag` = `Ergebnisse`.`spieltag` 
                AND `Spieltage`.`sp_nr` = `Ergebnisse`.`sp_nr`";
    foreach ($g_pdo->query($sql) as $row) {
        $team1 = $row['team1'];
        $team2 = $row['team2'];
        
        $tore1 = $row['tore1'];
        $tore2 = $row['tore2'];
        $spieltag = $row['spieltag'];
        $datum1 = $row['datum1'];
        
        $ergebnisse[$team1][$team2] = "<div data-toggle=\"tooltip\" data-placement=\"top\" title=\"".stamp_to_day($datum1)." - $spieltag. Spieltag\"><b>$tore1:$tore2</b></div>";
    }
    
    // Rückrunde mit Ergebnissen überschreiben
    $sql = "SELECT `Ergebnisse`.`spieltag`, `Spieltage`.`sp_nr`, team1, team2, datum2, tore1, tore2 
            FROM `Spieltage`, `Ergebnisse` 
            WHERE `Ergebnisse`.`spieltag` > 17
                AND  `Spieltage`.`spieltag` = `Ergebnisse`.`spieltag` -17
                AND `Spieltage`.`sp_nr` = `Ergebnisse`.`sp_nr`";
    foreach ($g_pdo->query($sql) as $row) {
        $team1 = $row['team2'];
        $team2 = $row['team1'];
        
        $tore1 = $row['tore1'];
        $tore2 = $row['tore2'];
        $spieltag = $row['spieltag'];
        $datum2 = $row['datum2'];
        
        $ergebnisse[$team1][$team2] = "<div data-toggle=\"tooltip\" data-placement=\"top\" title=\"".stamp_to_day($datum2)." - $spieltag. Spieltag\"><b>$tore1:$tore2</b></div>";
    }
    
    return $ergebnisse;
}

?>

