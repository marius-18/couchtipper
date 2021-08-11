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
        #$user_name[$user_nr] = $row['vorname']. " " .$row['nachname'];
        //$user_name[$user_nr] = $row['user_nr']; // Hier etwas pfusch.. da wird nur die Nummer übergeben
        $akt_punkte[$user_nr] = 0;
        $letzte_punkte[$user_nr] = 0;
  
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

        $sql = "SELECT punkte, user_nr FROM `Rangliste` WHERE spieltag = $letzter_spieltag"; 

        foreach ($g_pdo->query($sql) as $row){
            $user_nr = $row['user_nr'];
            $letzte_punkte [$user_nr] = $row['punkte'];
        }
    }

    
    array_multisort($punkte, SORT_DESC, $spiele, SORT_ASC, $akt_punkte,SORT_DESC, $schnitt, $letzte_punkte, $user); //SORTIERUNG HIER MIT GLEICHEN PLÄTZEN
 

    $platz = 1;
    
    foreach ($user as $i => $nr){

        if ($punkte[$i] == $punkte[$i-1]){
            $platz_r[$nr] = $platz_halten;
            $platz_halten = $platz_r[$nr];

        } else {
            $platz_r[$nr] = $platz;
            $platz_halten = $platz;
        }

        $platz++;

    }

    return array($punkte, $spiele, $akt_punkte, $schnitt, $letzte_punkte, $user, $platz_r);

}




?>
