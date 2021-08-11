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

    if (($spieltag > 17) && (get_wettbewerb_code() == "Buli")){
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

    return array($datum, $team_heim, $team_aus, $tore_heim, $tore_aus, $team_heim_nr, $team_aus_nr, $real_sp_nr, $real_spieltag, $anz_spiele, $gruppe);

// warum nicht gleich ausgeben ?
// Wegen mobil/desktop/ipad?


}

function get_other_tipps($spieltag, $sp_nr, $modus) {
    global $g_pdo;
    
    
    $sql = "SELECT tore1, tore2 
            FROM `Ergebnisse` 
            WHERE (spieltag = $spieltag && sp_nr = $sp_nr)";

    foreach ($g_pdo->query($sql) as $row) {
        $tore1 = $row['tore1'];
        $tore2 = $row['tore2'];
        
        if ($tore1 == ""){
            $tore1 = NULL;
        }
        
        if ($tore2 == ""){
            $tore2 = NULL;
        }
    }
    
    $sql = "SELECT Tipps.user_nr AS user_nr, tore1, tore2 
            FROM `Tipps` 
            WHERE (spieltag = $spieltag && sp_nr = $sp_nr)";

    foreach ($g_pdo->query($sql) as $row) {
        $i = $row['user_nr'];
        $tipp1 = $row['tore1'];
        $tipp2 = $row['tore2'];
        
        if ((($modus == "Tipps")) || ( $modus == "Spieltag")){
            $tipp[$i] = $tipp1." : ". $tipp2;
            $user_nr[$i] = $i;
            $user_name[$i] = get_username_from_nr($i);   
            //$vorname[$i] = $row['vorname'];
            //$nachname[$i] = $row['nachname'];
            
            if (($tore1 != NULL) && ($tore2 != NULL)){
            
                if (($tore1 == $tipp1) && ($tore2 == $tipp2)){
                    $punkte[$i] = "+3";
                    #    echo "jo1";
                }
                elseif ($tore1 - $tore2 == $tipp1 - $tipp2){  
                    $punkte[$i] = "+2";
                    #    echo "jo2";
                }
                elseif ((($tore1 - $tore2 > 0) && ($tipp1 - $tipp2 > 0)) || (($tore1 - $tore2 < 0) && ($tipp1 - $tipp2 < 0)) ){
                    $punkte[$i] = "+1";
                    #    echo "jo3";
                }
                else {
                    $punkte[$i] = "";
                    #    echo "jo4";
                }
            }
        
        }
    
    }
    
    

    


    if (!check_game_date($spieltag,$sp_nr)){
        #return 0;
        return array($user_nr, $user_name, $tipp, $vorname, $nachname, $punkte);
    }
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

