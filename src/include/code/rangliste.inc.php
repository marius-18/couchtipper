<?php

function rangliste ($begin, $ende, $gruppe, $rang_wett_id = ""){
    global $g_pdo;
    if ($ende < $begin){
        return 0;
    }

    $akt_spieltag = akt_spieltag();
    if ($ende < $akt_spieltag){
        $akt_spieltag = $ende; ## vielleicht hier ende und akt_spieltag tauschen!!! (alles über ende regeln)
    }
    if ($rang_wett_id == ""){
        list($id, $id_part) = get_curr_wett();
    } else {
        list($id, $id_part) = $rang_wett_id;  
    }
    
    $pay_border = Null;
    
    if (wettbewerb_has_parts($id)){
        // Decider: Hinrunde oder Rückrunde ?!
        if ($begin <= 17 && $ende <= 17 ) {
            $array1 = [$id, 0];
            $array2 = [$id, 0];
            
            $pay_border = get_wettbewerb_num_gewinner($array1);
            
        } elseif ($begin >= 18 && $ende >= 18 ) {
            $array1 = [$id, 1];
            $array2 = [$id, 1]; 
            
            $pay_border = get_wettbewerb_num_gewinner($array1);

        } else {
            $array1 = [$id, 0];
            $array2 = [$id, 1];
        }
        
    } else {
        $array1 = [$id, 0];
        $array2 = [$id, 0];
        
        $pay_border = get_wettbewerb_num_gewinner($array1);
    }
    


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
        if (!user_is_in_wettbewerb($array1, $user_nr) && !user_is_in_wettbewerb($array2, $user_nr)){
            continue;
        }
        $punkte[$user_nr] = $row['p'];
        $richtig[$user_nr] = $row['r'];
        $tendenz[$user_nr] = $row['t'];
        $differenz[$user_nr] = $row['d'];
        $user[$user_nr] = $user_nr;
        $akt_punkte[$user_nr] = 0;
        $letzte_punkte[$user_nr] = 0;
        $spieltagssieger[$user_nr] = 0;
        $spieltagssieger_last[$user_nr] = 0;
        $spiele[$user_nr] = 0;
  
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
            if (!user_is_in_wettbewerb($array1, $user_nr) && !user_is_in_wettbewerb($array2, $user_nr)){
                continue;
            }  
            $akt_punkte [$user_nr] = $row['punkte'];
        }
        
        // Achtung! DAS nur bei BULI oder ?!
        if (($akt_spieltag != 18) && ($ende != $begin)){ // Neu hinzugefügt!
            $sql = "SELECT punkte, user_nr FROM `Rangliste` WHERE spieltag = $letzter_spieltag"; 

            foreach ($g_pdo->query($sql) as $row){
                $user_nr = $row['user_nr'];
                if (!user_is_in_wettbewerb($array1, $user_nr) && !user_is_in_wettbewerb($array2, $user_nr)){
                    continue;
                }
                $letzte_punkte [$user_nr] = $row['punkte'];
            }
        }
    }
    
    
    ## Aktuelle Spieltagssieger
    $sql = "SELECT user_nr FROM `Rangliste` WHERE punkte = (SELECT max(punkte) FROM `Rangliste` WHERE spieltag = $akt_spieltag) and punkte != 0 and spieltag = $akt_spieltag";

    foreach ($g_pdo->query($sql) as $row){
        $user_nr = $row['user_nr'];
        if (!user_is_in_wettbewerb($array1, $user_nr) && !user_is_in_wettbewerb($array2, $user_nr)){
            continue;
        }
        $spieltagssieger[$user_nr] = 1;
    }

    if ($begin != $ende){
    ## Vorheriger Spieltagssieger
    $sql = "SELECT user_nr FROM `Rangliste` WHERE punkte = (SELECT max(punkte) FROM `Rangliste` WHERE spieltag = $letzter_spieltag) and spieltag = $letzter_spieltag";

    foreach ($g_pdo->query($sql) as $row){
        $user_nr = $row['user_nr'];
        if (!user_is_in_wettbewerb($array1, $user_nr) && !user_is_in_wettbewerb($array2, $user_nr)){
            continue;
        }
        $spieltagssieger_last[$user_nr] = 1;
        if ($akt_spieltag == 18){
            $spieltagssieger_last[$user_nr] = 0;
        }
    }
    }

    array_multisort($punkte, SORT_DESC, $spiele, SORT_ASC, $akt_punkte,SORT_DESC,
                     $schnitt, $letzte_punkte, $user, $spieltagssieger, $spieltagssieger_last); //SORTIERUNG HIER MIT GLEICHEN PLÄTZEN


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

    return array($punkte, $spiele, $akt_punkte, $schnitt, $letzte_punkte, $user, $platz_r, $spieltagssieger, $spieltagssieger_last, $pay_border);

}



function print_rangliste($begin, $ende, $modus, $chk_pay_line = false){
     $rang_wett_id = "";
     
     if ($chk_pay_line){
        $pay_line_info = "<br><div class=\"alert alert-info\">
                            <strong>Hinweis:</strong>
                            Die graue Linie markiert die Platzierung, bis zu der Gewinne ausgezahlt werden.</div>";
     } else {
        $pay_line_info = "";
     }
 
    list($punkte, $spiele, $akt_punkte, $schnitt, $letzte_punkte, $user, $platz, $spieltagssieger, $spieltagssieger_last, $pay_border) = rangliste($begin, $ende, $modus, $rang_wett_id);
    

    if ($ende != $begin) {
        ## Das brauchen wir nur, um den Platz am vorherigen Spieltag zu bestimmen
        list($punkte1, $spiele1, $akt_punkte1, $schnitt1, $letzte_punkte1, $user1, $platz_alt, $spieltagssieger1, $spieltagssieger_last1, $pay_border) = rangliste($begin, $ende-1, $modus, $rang_wett_id);
    } else {
        ## An Spieltag 18 fangen wir neu an! Da sollen die letzten Punkte nix zählen
        foreach ($user as $i => $nr){
            $platz_alt[$nr] = 1;
        }
    }
    echo "<div class=\"container-fluid\">
    <div class=\"table-responsive\">
        <table class=\"table table-sm table-striped  table-hover text-center center text-nowrap\" align=\"center\">
        <tr class=\"thead-dark\"><th>Pl</th><th>Spieler</th><th>&#931</th><th class=\"d-none d-sm-table-cell\">Spt.</th><th>&#216;</th>";

    
    if ($begin != $ende) {
        $sym_last_gameday = "<i class=\"fas fa-arrow-left\">";
    } else {
        $sym_last_gameday = "";
    }
    
    echo "<th><i class=\"fas fa-arrow-down\"></th><th>$sym_last_gameday</th><th></th><tr>";
    
    

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
        
        if ($begin == $ende){
            $aenderung = "";
            $letzte_punkte[$i] = "";
        }
        
        if (($pay_border != Null) && ($platz[$nr] > $pay_border) && ($chk_pay_line)){
            $pay_line = "<tr class=\"bg-secondary\">
                            <td colspan=\"3\"></td>
                            <td class=\"d-none d-sm-table-cell\"></td>
                            <td colspan=\"4\"></td></tr>";
            $chk_pay_line = false;
        } else {
            $pay_line = "";
        }
        
        echo "  $pay_line 
                <tr $logged>
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
   
   echo "</table></div>";
   echo $pay_line_info;
   echo "</div>";
}


function rangliste_seasons($seasons, $modus){
    rsort($seasons);

    foreach ($seasons as $saison){
        $bewerb = array($saison,0);
        new_db_connection($bewerb);

        ## Bei BuLi Saison wollen wir aufschlüsselung nach Hinrunde/Rückrunde
        if (wettbewerb_has_parts($saison)){
            $counter = 3;
        } else {
            $counter = 1;
        }

        ## Berechnungen für Hinrunde / Rückrunde / Gesamt
        for ($saison_part = 0; $saison_part < $counter; $saison_part++){

            ## Gesamt
            ## ACHTUNG: Gesamt muss zuerst, da bei Turnieren danach abgebrochen wird!
            if ($saison_part == 0){
                $start = 0;
                $ende = 34;
            }

            #Hinrunde
            if ($saison_part == 1){
                $start = 0;
                $ende = 17;
            }

            ## Rückrunde
            if ($saison_part == 2){
                $start = 18;
                $ende = 34;
            }




            list ($punkte, $spiele, $akt_punkte, $schnitt, $letzte_punkte, $user, $platz, $spieltagssieger, $spieltagssieger_last) = rangliste($start,$ende, "", $bewerb);

            foreach ($user as $index => $uid){
                $saison_punkte[$uid][$saison][$saison_part] = $punkte[$index];
                $platz_r[$uid][$saison][$saison_part] = $platz[$uid];
                $ges_user[$uid] = $uid;

                ## Gesamt Punkte / Platz nur einmal addieren! (Bei gesamter Saison)
                if ($saison_part == 0){
                    if (!isset($ges_punkte[$uid] )){
                        $ges_punkte[$uid] = $punkte[$index];
                    } else {
                        $ges_punkte[$uid] += $punkte[$index];
                    }
                }

                ## Schnitt der Platzierungen Berechnen
                ## Soll nicht für Buli Gesamt zählen.
                ## Nur bei Turnieren soll gesamt zählen.
                if ((!wettbewerb_has_parts($saison)) || ($saison_part != 0)){
                    if (!isset($ges_platz[$uid] )){
                        $ges_platz[$uid] = $platz[$uid];
                        $anz_platz[$uid] = 1;
                    } else {
                        $ges_platz[$uid] += $platz[$uid];
                        $anz_platz[$uid] += 1;
                    }
                }

            }
        }
    }


    foreach ($ges_platz as $index => $uid){
        $schnitt[$index] = round($ges_platz[$index] / $anz_platz[$index], 2);
    }

    return array($saison_punkte, $ges_punkte, $ges_user, $platz_r, $schnitt);
}

function alltime_rangliste($seasons, $modus){
    $ges_punkte = array();
    $ges_spiele = array();
    $ges_schnitt = array();
    $ges_user = array();
    $ges_platz = array();


    foreach ($seasons as $saison){
        $bewerb = array($saison,0);
        new_db_connection($bewerb);

        list ($punkte, $spiele, $akt_punkte, $schnitt, $letzte_punkte, $user, $platz1, $spieltagssieger, $spieltagssieger_last) = rangliste(0,34, "", $bewerb);


        foreach ($user as $index => $uid){
            if (!isset($ges_punkte[$uid])){
                $ges_punkte[$uid] = $punkte[$index];
            } else {
                $ges_punkte[$uid] += $punkte[$index];
            }

            if (!isset($ges_spiele[$uid])){
                $ges_spiele[$uid] = $spiele[$index];
            } else {
                $ges_spiele[$uid] += $spiele[$index];
            }

            if ($ges_spiele[$uid] != 0) {
                $ges_schnitt[$uid] = round($ges_punkte[$uid]/$ges_spiele[$uid], 2);
            }
            else {
                $ges_schnitt[$uid] = 0;
            }
            $ges_user[$uid] = $uid;

        }

        unset($punkte);
        unset($spiele);
        unset($schnitt);
        unset($user);
    }


    ### SORTIEREN

    array_multisort($ges_punkte, SORT_DESC, $ges_spiele, SORT_ASC, $ges_schnitt, $ges_user); //SORTIERUNG HIER MIT GLEICHEN PLÄTZEN


    $platz = 1;
    foreach ($ges_user as $i => $nr){

        if (($i != 0) && ($ges_punkte[$i] == $ges_punkte[$i-1])){
            $platz_r[$nr] = $platz_halten;
            $platz_halten = $platz_r[$nr];

        } else {
            $platz_r[$nr] = $platz;
            $platz_halten = $platz;
        }

        $platz++;

    }

    return array($ges_punkte, $ges_spiele, $ges_schnitt, $ges_user, $platz_r);
}


function print_alltime_rangliste($punkte, $spiele, $schnitt, $user, $platz){

    echo "<div class=\"container p-0\">
    <div class=\"table-responsive\">
        <table data-toggle=table data-sticky-header=true
        class=\"table table-sm table-striped  table-hover text-center center text-nowrap\" align=\"center\">

        <thead style=\"position: sticky;top: 100\" class=\"thead-dark data-sticky-header\">
        <th>Pl.</th>
        <th>Spieler</th>
        <th data-field=summe  data-sort-order=desc data-sortable=true>&#931</th>
        <th data-field=spt  data-sort-order=desc data-sortable=true>Spt.</th>
        <th data-field=avg  data-sort-order=desc data-sortable=true>&#216;</th>
        </thead>";

    foreach ($user as $i => $nr){
        if ($user[$i] == get_usernr()){
            $logged=" class=\"table-success\"";
        } else {
            $logged ="";
        }

        echo "  <tr $logged>
                <td>$platz[$nr].</td>
                <td>".get_username_from_nr($user[$i])."</td>
                <td>$punkte[$i]</td> 
                <td>$spiele[$i]</td>
                <td>$schnitt[$i]</td>
                "; 
        echo "
            </tr>";
   }
   
   echo "</table></div></div>";
}

function print_rangliste_seasons($punkte, $gesamt_punkte, $user, $platz, $seasons, $modus){
   
    if ($modus == "punkte"){
        $symbol = "&#931";
    }
    
    if ($modus == "platz"){
        $symbol = "&#216;";
    }
    

    echo "<div class=\"container-fluid p-0\">
    <div class=\"table-responsive\">
        <table 
         data-toggle=\"table\"
        data-sort-empty-last=\"true\"
        data-sticky-header=\"true\"
        class=\"table table-sm table-striped  table-hover text-center center text-nowrap\" align=\"center\">
        
        <thead style=\"position: sticky;top: 100\"
        class=\"thead-dark data-sticky-header\">
        <tr>
        <th rowspan=\"2\">Spieler</th>
        <th rowspan=\"2\" data-field=summe data-sort-order=desc data-sortable=true>$symbol</th>";
    
    list ($code, $jahr) = get_all_wettbewerbe();
    krsort($code);

    
    // Tabelle Header
    foreach ($code as $id => $wett){
        if ($wett == "BuLi"){
            $name = $jahr[$id];
        } else {
            $name = $wett . "" . $jahr[$id];
        }
        if (in_array($id, $seasons)){
            if (wettbewerb_has_parts($id)){
                echo "<th colspan=\"3\">$name</th>";
            } else {
                echo "<th rowspan=\"2\" data-field=$name  data-sort-order=desc data-sortable=true>$name</th>";
            }
        }
    }
    echo "</tr>";

    ## Zweite Header Zeile
    ## In Buli Saisons soll Hinrunde/ Rückrunde getrennt aufgezeigt werden
    echo "<tr>";
    // Tabelle Header
    foreach ($code as $id => $wett){
        $name = $wett . "" . $jahr[$id];
        if (in_array($id, $seasons)){
            if (wettbewerb_has_parts($id)){
                ## Nur in BuLi Saison 2 Zeilen erstellen
                echo "<th data-field=".$name."HR  data-sort-order=desc data-sortable=true
        data-sort-empty-last=\"true\"> RR</th>";
                echo "<th data-field=".$name."RR  data-sort-order=desc data-sortable=true> HR</th>";
                echo "<th data-field=".$name."Ges  data-sort-order=desc data-sortable=true> Ges</th>";
            }
        }
    }
    echo "</tr>";

    echo "</thead>";
    
    
    // Tabelle Body
    foreach ($user as $i => $nr){
        if ($user[$i] == get_usernr()){
            $logged=" class=\"table-success\"";
        } else {
            $logged ="";
        }

        echo "  <tr $logged>
                <td>".get_username_from_nr($user[$i])."</td>
                "; 
            echo "<td>".$gesamt_punkte[$i]."</td>";

            foreach ($code as $id => $wett){
             if (wettbewerb_has_parts($id)){
                 $count = 3;
            } else {
                $count = 1;
            }

            for ($saison_teil = $count - 1; $saison_teil >= 0; $saison_teil--){


            if (in_array($id, $seasons)){
                if ($modus == "punkte"){
                    if (isset($punkte[$i][$id][$saison_teil])){
                        $sum = $punkte[$i][$id][$saison_teil];
                    } else{
                        $sum = "";
                    }
                }
                
                if ($modus == "platz"){
                    if (isset($platz[$i][$id][$saison_teil])){
                        if ($platz[$i][$id][$saison_teil] == 1){
                            $sum = "<span style=\"background-color:#C98910\"class=\"badge badge-pill\">".$platz[$i][$id][$saison_teil]."</span>";
                        } elseif ($platz[$i][$id][$saison_teil] == 2) {
                            $sum = "<span style=\"background-color:#A8A8A8\"class=\"badge badge-pill\">".$platz[$i][$id][$saison_teil]."</span>";
                        } elseif ($platz[$i][$id][$saison_teil] == 3) {
                            $sum = "<span style=\"background-color:#965A38\"class=\"badge badge-pill\">".$platz[$i][$id][$saison_teil]."</span>";
                        } else {
                            $sum = $platz[$i][$id][$saison_teil];
                        }
                    } else{
                        $sum = "";
                    }
                }
                
                
                echo "<td>$sum</td>";
            }

            }
        }   
        
        echo "
            </tr>";
    }
    echo "</table></div></div>";
}


?>
