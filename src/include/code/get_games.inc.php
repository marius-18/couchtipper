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



    $sql = "SELECT spieltag, sp_nr, t1.team_name AS Team_name$teil1, t2.team_name AS Team_name$teil2, 
            datum$teil1 AS datum, t1.team_nr AS Team_nr$teil1, t2.team_nr AS Team_nr$teil2
            FROM Spieltage,Teams t1, Teams t2
            WHERE (spieltag = $spieltag) AND (t1.team_nr = team1) AND (t2.team_nr = team2)";

    foreach ($g_pdo->query($sql) as $row) {
        $sp_nr = $row['sp_nr'];
        $team_heim [$sp_nr] = $row['Team_name1'];
        $team_aus [$sp_nr] = $row['Team_name2'];
        $datum [$sp_nr] = $row['datum'];
        $team_heim_nr [$sp_nr] = $row['Team_nr1'];
        $team_aus_nr [$sp_nr] = $row['Team_nr2'];
        $real_sp_nr [$sp_nr] = $sp_nr;
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


    array_multisort($datum, SORT_ASC, $team_heim, $team_aus, $tore_heim, $tore_aus, $team_heim_nr, $team_aus_nr, $real_sp_nr);

    return array($datum, $team_heim, $team_aus, $tore_heim, $tore_aus, $team_heim_nr, $team_aus_nr, $real_sp_nr, $real_spieltag, $anz_spiele);
### letzter parameter: $gruppe.. wofür? vllt für WM/EM?
// warum nicht gleich ausgeben ?
// Wegen mobil/desktop/ipad?


}

function get_open_db_spieltag($modus, $jahr, $spieltag){
    $url = "https://www.openligadb.de/api/getmatchdata/$modus/$jahr/$spieltag";
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
    // DB Abfrage je nach modus.. Hier bisher nur buli!
    $matches = get_open_db_spieltag($modus, $jahr, $spieltag);

    if ($spieltag <= 17) {
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
        
        foreach ($matches as $match) {
        
            if (!strnatcmp($match["Team1"]["TeamName"], $team1) and !strcmp($match["Team2"]["TeamName"], $team2)) {
                // Das ist das aktuelle Spiel!
                $tore_heim[$sp_nr] = $match["MatchResults"][0]["PointsTeam1"];
                $tore_aus[$sp_nr]  = $match["MatchResults"][0]["PointsTeam2"];
        
                echo "Spiel $sp_nr ";
                echo "$team1 ".$match["MatchResults"][0]["PointsTeam1"]." - ".$match["MatchResults"][0]["PointsTeam2"]." $team2";
                echo "<br>";

            }
        }
        
    }
    
    return array($tore_heim, $tore_aus);
}


function get_tore($spieltag, $modus){
    global $g_pdo;
    #return 0;
    // DB Abfrage je nach modus.. Hier bisher nur buli!
    if (get_curr_wett()[0] <= 2){
        ## Für die alten muss noch die datenbank namen und vereinsnamen rein
        return;
    }
    $jahr = substr(get_wettbewerb_jahr(get_curr_wett()), 0, 4);
    #echo "$jahr ";
    $matches = get_open_db_spieltag("bl1", $jahr, $spieltag);

    if ($spieltag <= 17) {
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
        $sp_nr = $row['sp_nr'];
        $team1[$sp_nr] = $row['Heim'];
        $team2[$sp_nr] = $row['Aus'];
        $spiel[$row['Heim']] = $sp_nr;
        $spiel[$row['Aus']] = $sp_nr;
        
    }
    
    $ret = array();
    foreach ($matches as $match) {
        $sp_nr = $spiel[$match["Team1"]["TeamName"]];
        $ret[$sp_nr] = "";
        
        if ($spiel[$match["Team1"]["TeamName"]] == $spiel[$match["Team2"]["TeamName"]]) {
            // Das ist das aktuelle Spiel!
            $ret[$sp_nr] = "<table align=\"center\">";
            $t1 = 0;
            $t2 = 0;
            foreach ($match["Goals"] as $goal){
                $zusatz = "";
                if ($goal["ScoreTeam1"] >  $t1) {
                    $ret[$sp_nr] .= "<tr class=\"table-info\">";
                }
                else if ($goal["ScoreTeam2"] >  $t2) {
                    $ret[$sp_nr] .= "<tr class=\"table-primary\">";
                } else {
                    $zusatz = "(VAR)";
                }
                
                if ($goal["IsPenalty"]){
                    $zusatz = "(11m)";
                }
                if ($goal["IsOwnGoal"]){
                    $zusatz = "(ET)";
                }
                $ret[$sp_nr] .= "<td>'".$goal["MatchMinute"]."</td>";
                $ret[$sp_nr] .= "<td>".$goal["ScoreTeam1"]." : ".$goal["ScoreTeam2"]."</td>";
                $ret[$sp_nr] .= "<td>".$goal["GoalGetterName"]."</td>";
                $ret[$sp_nr] .= "<td>$zusatz</td>";
                $ret[$sp_nr] .= "</tr>";
                
                $t1 = $goal["ScoreTeam1"];
                $t2 = $goal["ScoreTeam2"];

            }
            $ret[$sp_nr] .= "</table>";

        }
    }
    
    return $ret;
}

function get_other_tipps($spieltag, $modus) {
    global $g_pdo;
    
    
    $sql = "SELECT tore1, tore2, sp_nr
            FROM `Ergebnisse` 
            WHERE (spieltag = $spieltag)";

    foreach ($g_pdo->query($sql) as $row) {
        $sp_nr = $row['sp_nr'];
        $tore1[$sp_nr] = $row['tore1'];
        $tore2[$sp_nr] = $row['tore2'];
        
        if ($tore1[$sp_nr] == ""){
            $tore1[$sp_nr] = NULL;
        }
        
        if ($tore2[$sp_nr] == ""){
            $tore2[$sp_nr] = NULL;
        }
    }
    
    $sql = "SELECT Tipps.user_nr AS user_nr, tore1, tore2, sp_nr
            FROM `Tipps` 
            WHERE (spieltag = $spieltag)";
    $user_nr = NULL;
    $user_name = NULL;
    $tipp = NULL;
    $vorname = NULL;
    $nachname = NULL;
    $punkte = NULL;

    foreach ($g_pdo->query($sql) as $row) {
        $i = $row['user_nr'];
        $sp_nr = $row['sp_nr'];

        if (check_game_date($spieltag,$sp_nr)){
            continue;
        }
        
        
        $tipp1[$sp_nr] = $row['tore1'];
        $tipp2[$sp_nr] = $row['tore2'];  
        
        if (((($modus == "Tipps")) || ( $modus == "Spieltag")) && isset($tore1[$sp_nr]) && isset($tore2[$sp_nr])){
            $tipp[$sp_nr][$i] = $tipp1[$sp_nr]." : ". $tipp2[$sp_nr];
            $user_nr[$sp_nr][$i] = $i;
            $user_name[$sp_nr][$i] = get_username_from_nr($i);  
            $vorname[$sp_nr][$i] = "";
            $nachname[$sp_nr][$i] = "";
            //$vorname[$sp_nr][$i] = $row['vorname'];
            //$nachname[$sp_nr][$i] = $row['nachname'];
            
            if (($tore1[$sp_nr] != NULL) && ($tore2[$sp_nr] != NULL)){
            
                if (($tore1[$sp_nr] == $tipp1[$sp_nr]) && ($tore2[$sp_nr] == $tipp2[$sp_nr])){
                    $punkte[$sp_nr][$i] = "+3";
                }
                elseif ($tore1[$sp_nr] - $tore2[$sp_nr] == $tipp1[$sp_nr] - $tipp2[$sp_nr]){  
                    $punkte[$sp_nr][$i] = "+2";
                }
                elseif ((($tore1[$sp_nr] - $tore2[$sp_nr] > 0) && ($tipp1[$sp_nr] - $tipp2[$sp_nr] > 0)) || (($tore1[$sp_nr] - $tore2[$sp_nr] < 0) && ($tipp1[$sp_nr] - $tipp2[$sp_nr] < 0)) ){
                    $punkte[$sp_nr][$i] = "+1";
                }
                else {
                    $punkte[$sp_nr][$i] = "";
                }
            }
        
        }
    
    }
    
    #if (!check_game_date($spieltag,$sp_nr)){
        #return 0;
        return array($user_nr, $user_name, $tipp, $vorname, $nachname, $punkte);
    #}
}


function get_next_games(){
    ### Gibt die Spiele zurück die als nächstes starten
    global $g_pdo;
    list($wett_id, $part) = get_curr_wett();
    if (wettbewerb_has_parts($wett_id)){
        $part += 1;
        $date = "datum$part";
    } else {
        $date = "datum";   
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
$max_gruppen_spt = 13; // nach dem 15. Spieltag keine Gruppenspiele mehr!

#   for ($i = 1; $i <= $anz_spiele; $i++){
#      $tore_heim[$i] = "10";
#      $tore_aus[$i] = "111";
#   }

   $sql = "
     SELECT spieltag, sp_nr, t1.team_name AS Team_name1, t2.team_name AS Team_name2, 
     datum1 AS datum, t1.team_nr AS Team_nr1, t2.team_nr AS Team_nr2
     FROM Spieltage,Teams t1, Teams t2
     WHERE (t1.gruppe = '$gruppe') AND (t1.team_nr = team1) AND (t2.team_nr = team2) AND (spieltag<=$max_gruppen_spt)";

     foreach ($g_pdo->query($sql) as $row) {
      $sp_nr = $row['sp_nr']."-".$row['spieltag'];
      $team_heim [$sp_nr] = $row['Team_name1'];
      $team_aus [$sp_nr] = $row['Team_name2'];
      $datum [$sp_nr] = $row['datum'];
      $real_sp_nr [$sp_nr] = $sp_nr;
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

   return array($datum, $team_heim, $team_aus, $tore_heim, $tore_aus, $real_sp_nr);

}

function print_gruppe($gruppe){


    echo "<br>"; 

    $args = get_group_games($gruppe);
    list ($datum, $team_heim, $team_aus, $tore_heim, $tore_aus, $real_sp_nr) = $args;

    
    echo "<div class=\"table-responsive-sm\">";
    echo "<table class=\"table table-striped table-sm  table-hover text-center center\">";


    foreach ($real_sp_nr AS $i){
        echo "<tr>";

        echo "<td align = \"right\"> $team_heim[$i] </td> 
              <td align = \"center\"> $tore_heim[$i]:$tore_aus[$i] </td> 
              <td align =\"left\"> $team_aus[$i] </td> 
              <td  style=\"border-left: 3px solid #AAAAAA; padding-left: 1px;\">".stamp_to_date_gruppe($datum[$i])."</td>";
        
        echo "</tr>";
    }

    
    echo "</table>";
    echo "</div>";
}


?>

