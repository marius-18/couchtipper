<?php

function rangliste ($begin, $ende, $gruppe){
    global $g_pdo;
    $akt_spieltag = akt_spieltag();

    $sql = "SELECT sum(richtig) as r, sum(tendenz) as t, sum(differenz) as d, sum(punkte) as p, Rangliste.user_nr, User.user_name,  vorname, nachname  
            FROM `Rangliste`, `User` 
            WHERE (`User`.user_nr = Rangliste.user_nr AND Rangliste.spieltag >= $begin AND Rangliste.spieltag <= $ende) 
            GROUP BY Rangliste.user_nr";

    $sql = "SELECT sum(richtig) as r, sum(tendenz) as t, sum(differenz) as d, sum(punkte) as p, Rangliste.user_nr  
            FROM `Rangliste`
            WHERE (Rangliste.spieltag >= $begin AND Rangliste.spieltag <= $ende) 
            GROUP BY Rangliste.user_nr";

    foreach ($g_pdo->query($sql) as $row) {
        $user_nr = $row['user_nr'];
        $punkte[$user_nr] = $row['p'];
        $richtig[$user_nr] = $row['r'];
        $tendenz[$user_nr] = $row['t'];
        $differenz[$user_nr] = $row['d'];
        $user[$user_nr] = $user_nr;
        $akt_punkte[$user_nr] = 0;
        $letzte_punkte[$user_nr] = 0;
        $spieltagssieger[$user_nr] = 0;
        $spieltagssieger_last[$user_nr] = 0;
  
        $sql1 = "SELECT count(distinct(spieltag)) as spiele FROM `Tipps` WHERE (user_nr = $user_nr AND spieltag <= $akt_spieltag AND spieltag >= $begin AND spieltag <= $ende)";

        foreach ($g_pdo->query($sql1) as $row) {
            $spiele[$user_nr] = $row['spiele'];
        }

        if ($spiele[$user_nr] != 0) {
            $schnitt[$user_nr] = round($punkte[$user_nr]/$spiele[$user_nr], 2);
        }    
        else {
            $schnitt[$user_nr] = 0;
        }

    }

    $letzter_spieltag = $akt_spieltag - 1;

    $sql = "SELECT punkte, user_nr FROM `Rangliste` WHERE spieltag = $akt_spieltag"; 

    if ($akt_spieltag <= $ende) { //sonst gibts fehler wenn spieler in der Rückrunde dazukommen

        foreach ($g_pdo->query($sql) as $row){
            $user_nr = $row['user_nr'];
            $akt_punkte [$user_nr] = $row['punkte'];
        }
        
        // Achtung! DAS nur bei BULI oder ?!
        if ($akt_spieltag != 18){ // Neu hinzugefügt!
            $sql = "SELECT punkte, user_nr FROM `Rangliste` WHERE spieltag = $letzter_spieltag"; 

            foreach ($g_pdo->query($sql) as $row){
                $user_nr = $row['user_nr'];
                $letzte_punkte [$user_nr] = $row['punkte'];
            }
        }
    }
    
    
    $sql = "SELECT user_nr FROM `Rangliste` WHERE punkte = (SELECT max(punkte) FROM `Rangliste` WHERE spieltag = $akt_spieltag) and spieltag = $akt_spieltag";

    foreach ($g_pdo->query($sql) as $row){
        $user_nr = $row['user_nr'];
        $spieltagssieger[$user_nr] = 1;
    }

    $sql = "SELECT user_nr FROM `Rangliste` WHERE punkte = (SELECT max(punkte) FROM `Rangliste` WHERE spieltag = $letzter_spieltag) and spieltag = $letzter_spieltag";

    foreach ($g_pdo->query($sql) as $row){
        $user_nr = $row['user_nr'];
        $spieltagssieger_last[$user_nr] = 1;
    }
    
    array_multisort($punkte, SORT_DESC, $spiele, SORT_ASC, $akt_punkte,SORT_DESC, $schnitt, $letzte_punkte, $user, $spieltagssieger, $spieltagssieger_last); //SORTIERUNG HIER MIT GLEICHEN PLÄTZEN


    $platz = 1;
    foreach ($user as $i => $nr){

        if (($i != 0) && ($punkte[$i] == $punkte[$i-1])){
            $platz_r[$nr] = $platz_halten;
            $platz_halten = $platz_r[$nr];

        } else {
            $platz_r[$nr] = $platz;
            $platz_halten = $platz;
        }

        $platz++;

    }

    return array($punkte, $spiele, $akt_punkte, $schnitt, $letzte_punkte, $user, $platz_r, $spieltagssieger, $spieltagssieger_last);

}



function print_rangliste($begin, $ende, $modus){

    list($punkte1, $spiele1, $akt_punkte1, $schnitt1, $letzte_punkte1, $user1, $platz_alt, $spieltagssieger, $spieltagssieger_last) = rangliste($begin, $ende-1, $modus);
    list($punkte, $spiele, $akt_punkte, $schnitt, $letzte_punkte, $user, $platz, $spieltagssieger, $spieltagssieger_last) = rangliste($begin, $ende, $modus);

    echo "<div class=\"container\">
    <div class=\"table-responsive\">
        <table class=\"table table-sm table-striped  table-hover text-center center text-nowrap\" align=\"center\">
        <tr class=\"thead-dark\"><th>Pl</th><th>Spieler</th><th>&#931</th><th class=\"d-none d-sm-table-cell\">Spt.</th><th>&#216;</th>";

    echo "<th><i class=\"fas fa-arrow-down\"></th><th><i class=\"fas fa-arrow-left\"></th><th></th><tr>";

    foreach ($user as $i => $nr){
        if ($user[$i] == get_usernr()){
            $logged=" class=\"table-success\"";
        } else {
            $logged ="";
        }
        $dif[$i] = $platz_alt[$nr] - $platz[$nr] ;
        if ($platz_alt[$nr] < $platz[$nr]){
            $aenderung = "<span class=\"badge badge-pill badge-danger\"><i class=\"fas fa-arrow-down\"></i> " . -$dif[$i] ." </span>";
        }

        if ($platz_alt[$nr] == $platz[$nr]){
            $aenderung = "";
        }   
  
        if ($platz_alt[$nr] > $platz[$nr]){
            $aenderung = "<span class=\"badge badge-pill badge-success\"><i class=\"fas fa-arrow-up\"></i> " . $dif[$i] . "</span>";
        }
        
        if ($spieltagssieger[$i]){
            $akt_punkte[$i] = "<span class=\"badge badge-pill badge-warning\">" . $akt_punkte[$i] . "</span>";
        }
        
        if ($spieltagssieger_last[$i]){
            $letzte_punkte[$i] = "<span class=\"badge badge-pill badge-warning\">" . $letzte_punkte[$i] . "</span>";
        }        

        echo "  <tr $logged>
                <td>$platz[$nr].</td>
                <td>".get_username_from_nr($user[$i])."</td>
                <td>$punkte[$i]</td> 
                <td  class=\"d-none d-sm-table-cell\">$spiele[$i]</td>
                <td>$schnitt[$i]</td>
                <td>$akt_punkte[$i]</td>
                <td>$letzte_punkte[$i]</td>"; 

        echo "<th>$aenderung</th>";
        echo "
            </tr>";
   }
   
   echo "</table></div></div>";
}



?>
