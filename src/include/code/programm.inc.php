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


    return array($spieltag, $team_nr1, $team_nr2, $team_name, $datum, $tore1, $tore2);
}



function print_programm($spieltag, $team_nr1, $team_nr2, $team_name, $datum, $tore1, $tore2, $start, $ende, $id, $status){

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
        
        if ($tore1[$spt] == "") {
            $ergebnis =  "- : -";
        } else {
            $ergebnis = $tore1[$spt]." : ".$tore2[$spt];
        }
        
        if ($datum[$spt] != 0) {
            $spielzeit = stamp_to_date_programm($datum[$spt]);
        } else { 
            $spielzeit = "&nbsp;";
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





?>
