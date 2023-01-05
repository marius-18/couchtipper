<?php
include_once("src/include/code/rangliste.inc.php");

####################################################
####################################################
###################################################
####### Hier ist alles Rund um's Geld drin
####################################################
####################################################
###################################################

function gewinn($array){
    // Berechnet denn Gewinn und den Prozentualen Anteil für jede Position 
    // Nimmt Einsatz, Anzahl Spieler, Anzahl Gewinner, Dämpfung und Tagesprämie entgegen
    // E = Einsatz, S = Spieler, G = Gewinner, d = Dämpfung [1.5]
    
    ## Eingabe als Array der Form: array($wett_id, $part)
    list($id, $id_part) = $array; 
    
    $E = get_wettbewerb_einsatz($array);
    $g = get_wettbewerb_gewinner($array);
    $d = get_wettbewerb_daempfung($array);
    $praemie = get_wettbewerb_praemie($array);

    $S = anz_user_wett($array);
    $G = round($S*$g); // Anzahl der Gewinner
    
    
    // Summe im Nenner berechnen
    $sum = 0;
    for ($i = 0; $i < $G; $i++){
        $sum += pow($i,$d);  
    }

    // Das wird für die Spieltagsprämie benötigt.
    $abzug = 17 * $praemie;
   
    // Der Gesamt Summe = Spieler * Einsatz - Spieltagsprämie
    $max = $E * $S - $abzug;
   
    // Gesamter Bruch
    $help = ($max - $G*$E) / $sum;

    for ($i = 1; $i <= $G; $i++){
        $gewinn[$i] = round(pow(($G-$i),$d) * $help + $E -0.1, 0); //-0.1 damit 0.5 abgerundet wird. --> gewinn geht immer auf!
        $prozent[$i] = round($gewinn[$i]/($max)*100,2);     
   }

  return array($gewinn,$prozent,$E, $S, $G, $d, $praemie);
  
}


function gewinn_zuordnung($array){
    ### Ordnet den Spielern ihren Gewinn zu
    ## Eingabe als Array der Form: array($wett_id, $part)
    list($id, $id_part) = $array;
    
    if (wettbewerb_has_parts($id)){
        if ($id_part == 0){
            // Hinrunde
            $begin = 1;
            $ende  = 17;
        } else {
            // Rückrunde
            $begin = 18;
            $ende  = 34;
        }
    }
    
    
    $gewinner = round(anz_user_wett($array) * get_wettbewerb_gewinner($array));
        
    list($punkte, $spiele, $akt_punkte, $schnitt, $letzte_punkte, $user, $platz, $spieltagssieger, $spieltagssieger_last) = rangliste($begin, $ende, 1);
    list($gewinn,$prozent,$E, $S, $G, $d, $praemie) = gewinn($array);
    
    
    // geteilte Plätze finden
    $geteilt = array();
    $alt = 0;
    foreach ($platz as $key => $pl){
        if ($pl <= $gewinner) {            
            if ($pl == $alt){
                if (!in_array($pl,$geteilt)){
                    array_push($geteilt, $pl);
                }
            }
        }
        $alt = $pl;
    }

    
    // geteilte Plätze durchgehen und den Gewinn dazu ausrechnen
    foreach ($geteilt as $i){
        $count = count(array_intersect($platz, array($i))); // Anzahl an spielern auf platz $i..

        // Berechne das Geld für diesen Platz
        // Alles aufsummieren
        $geld = 0;
        for ($x = 0; $x < $count; $x++){
            $geld += $gewinn[$i + $x];
        }
        // Und durch Anzahl der Spieler teilen
        $geld /= $count;
        
        // Am Ende das ganze noch auf 2 Stellen runden
        $gewinn[$i] = round($geld,2);
        
    }
    
    ## Löscht aus der platz Liste die Spieler raus, die nichts gewonnen haben
    $platz = array_filter($platz, function ($key) use ($gewinner) {return ($key<=$gewinner) ;});

    return(array($platz, $gewinn));
}


function tagessieger_geld($array){
    ### Berechnet alle Tagessieger
    global $g_pdo;
    ## Eingabe als Array der Form: array($wett_id, $part)
    list($id, $id_part) = $array;
    
    if (wettbewerb_has_parts($id)){
        if ($id_part == 0){
            // Hinrunde
            $begin = 1;
            $ende  = 17;
        } 
        if ($id_part == 1){
            // Rückrunde
            $begin = 18;
            $ende  = 34;
        }
        if ($id_part == 2){
            // Rückrunde
            $begin = 1;
            $ende  = 34;
        }
    }
    
    $praemie = get_wettbewerb_praemie($array);

    $sql = "SELECT user_nr, count(user_nr) as anzahl,  sum(1/anz) as quote FROM `Tagessieger` WHERE (spieltag >= $begin AND spieltag <= $ende) group by user_nr order by anzahl DESC, quote DESC";
    
    foreach ($g_pdo->query($sql) as $row) {
        $user_nr = $row['user_nr'];
        $user[$user_nr] = $user_nr;
        $anzahl[$user_nr] = $row['anzahl'];
        $anteil[$user_nr] = $row['quote'];
        $geld[$user_nr] = round($anteil[$user_nr] * $praemie, 2);
    }

    return array($user, $anzahl, $anteil, $geld);
}


function print_gewinn($array){
    ### Gibt die Liste der einzelnen Gewinne pro Platz aus
    
    list($gewinn, $prozent, $E, $S, $G, $d, $praemie) =  gewinn($array);

    $max = $E * $S  - 17 * $praemie;

    echo "
        wobei <br>
        E = Einsatz (=$E&#8364;)<br>
        G = Anzahl gewinnender Spieler (=$G)<br>
        S = Anzahl der Spieler (=$S)<br>
        max = Summe die ausgezahlt wird (E * S - 17 * $praemie&#8364; = $max&#8364;)
        ";

    echo "
        <br><br>
        Damit ergibt sich bisher folgende Verteilung
        ";
        
    echo "<table align = \"center\">
            <tr bgcolor=\"#B6B6B4\"><th>Platz</th><th>%</th><th> &euro; (gerundet) </th></tr>

        ";

    for ($i = 1; $i <= $G; $i++){
        echo "
            <tr>
                <th> $i </th>
                <th>". $prozent[$i]."</th>
                <th>".$gewinn[$i]."</th>
            </tr>";
    }

    echo " </table>";

    echo "(Alle Angaben sind ohne Gew&auml;hr ;))";

}


function print_gewinner($array){
    ### Gibt eine Liste mit allen Gewinnern aus
    list($platz, $gewinn) = gewinn_zuordnung($array);

    echo "<div class=\"table-responsive\">";
    echo "<table class=\"table table-sm text-center center text-nowrap table-striped table-hover\" align=\"center\" >
            <tr class=\"thead-dark\"><th>Platz</th><th>Spieler</th><th>Gewinn</th></tr>
            ";
            
    foreach ($platz as $user => $pl){
        echo "<tr>";
        echo "<td> $pl </td>";
        echo "<td>". get_username_from_nr($user). "</td>";
        echo "<td>". $gewinn[$pl] ." &euro;</td>";
        echo "</tr>";
    }
    
    echo "</table></div>";


}


function print_gesamt_gewinner($array){
    ### Gibt eine Liste mit Gewinnern und Tagessiegern aus
    
    list($user, $anzahl, $anteil, $geld) = tagessieger_geld($array);
    list($platz, $gewinn) = gewinn_zuordnung($array);

    
    echo "<div class=\"table-responsive\">";
    echo "<table class=\"table table-sm text-center center text-nowrap table-striped table-hover\" align=\"center\" >
            <tr class=\"thead-dark\"><th>Platz</th><th>Spieler</th><th>Gewinn</th><th>Spieltag</th><th>&#931</th></tr>
            ";
            
    ### Zuerst die Gewinner über die Rangliste (und ihre Tagessiege)
    $max_geld = 0;
    foreach ($platz as $usr => $pl){
        $gewinner = $gewinn[$pl];
        
        if ($geld[$usr] != ""){
            $tag = $geld[$usr] . "&euro;";
            $gesamt = $gewinner + $geld[$usr];
        } else {
            $tag = "";
        }
                
        #$gesamt = $gewinner + $tag;
        echo "<tr>";
        echo "<td> $pl </td>";
        echo "<td>". get_username_from_nr($usr). "</td>";
        echo "<td> $gewinner&euro;</td>";
        echo "<td> $tag</td>";
        echo "<td> $gesamt&euro;</td>";
        echo "</tr>";
        $max_geld += $geld[$usr];
        $max_geld += $gewinn[$pl];
        unset($user[$usr]);
    }
    
    
    ### Jetzt noch die restlichen Tagessieger
    
    if (!empty($user)) {
        echo "<tr><td colspan=\"5\" class=\"table-dark\"> Restliche Tagessieger </td></tr>";
    }
    
    foreach ($user as $user_nr) {   
        $username = get_username_from_nr($user_nr);
        echo "<tr><td></td><td>$username</td><td></td><td>".$geld[$user_nr]." &euro;</td><td>".$geld[$user_nr]." &euro;</td></tr>";
        $max_geld += $geld[$user_nr];
    }

    echo "<tr><td colspan=\"4\" class=\"table-info\"> Gesamte Auszahlung:</td> <td class=\"table-info\">$max_geld &euro;</td></tr>";
    
    echo "</table></div>";
}


function print_tagessieger_geld($array){

    list($user, $anzahl, $anteil, $geld) = tagessieger_geld($array);
    

    echo "<div class=\"table-responsive\">";
        echo "<table data-sort-name=Anzahl data-sort-order=desc data-toggle=table class=\"table table-sm text-center center text-nowrap table-striped table-hover\" align=\"center\" >
                <thead class=\"thead-dark\"><tr><th>Spieler</th><th data-field=Anzahl data-sortable=true>Anzahl</th><th data-field=Geld data-sortable=true data-sorter=totalCurrencySort>Gewinn</th></tr></thead>
                ";
            
        foreach ($user as $user_nr) {
            $username = "";
        
            if ($user_nr == get_usernr()){
                $username = "<i class=\"fas fa-circle text-success\"></i> ";
            } 
        
            $username .= get_username_from_nr($user_nr);
            echo "<tr><td>$username</td><td>".$anzahl[$user_nr]."</td><td>".$geld[$user_nr]." &euro;</td></tr>";
       
        }
       
        echo "</table>";
    echo "</div>";
}


function print_tagessieger_liste($array){
    ## Eingabe als Array der Form: array($wett_id, $part)
    list($id, $id_part) = $array;
    
    global $g_pdo;

    if (wettbewerb_has_parts($id)){
        if ($id_part == 0){
            // Hinrunde
            $begin = 1;
            $ende  = 17;
        } 
        if ($id_part == 1){
            // Rückrunde
            $begin = 18;
            $ende  = 34;
        }

        if ($id_part == 2){
            // Rückrunde
            $begin = 1;
            $ende  = 34;
        }
    }
    
   
    #echo "<div class=\"table-responsive\">";
    #    echo "<table data-sort-name=spieltag data-sort-order=asc data-toggle=table class=\"table table-sm text-center center text-nowrap\" align=\"center\" >
    #            <thead class=\"thead-dark\"><tr><th data-field=spieltag data-sortable=true>Spieltag</th><th>Spieler</th><th data-field=Geld data-sortable=true>Punkte</th></tr></thead>
    #            ";
    
    if ($array == get_curr_wett() && spieltag_running()){
        echo "
        <div class=\"alert alert-warning\">
        <span class=\"badge badge-pill badge-danger\">Achtung!</span> Der aktuelle Spieltag ist noch nicht beendet. Der Tagessieger kann sich der daher noch &auml;ndern!
        </div>";
    }
    
    echo "<div class=\"table-responsive\">";
        echo "<table class=\"table table-sm text-center center text-nowrap\" align=\"center\" >
                <thead class=\"thead-dark\"><tr><th>Spieltag</th><th>Spieler</th><th>Punkte</th></tr></thead>
                ";   
    

    for ($spt = $ende; $spt >= $begin ; $spt--){

        if ($spt%2 == 1){
            $active = "class=\"table-active\"";
        } else {
            $active = "";
        }
    
        $sql = "SELECT count(user_nr) as anz FROM `Rangliste` WHERE punkte = (SELECT max(punkte) FROM `Rangliste` WHERE spieltag = $spt) and spieltag = $spt";
    
        foreach ($g_pdo->query($sql) as $row) {
            $anz = $row['anz'];
        }
    
        if ($anz != 0) {
            echo "<tr $active> <td class=\"align-middle\" rowspan=\"$anz\">$spt</td>";
        }
    
        $sql = "SELECT user_nr, punkte FROM `Rangliste` WHERE punkte = (SELECT max(punkte) FROM `Rangliste` WHERE spieltag = $spt) and spieltag = $spt";
        
        $i = 1;
        foreach ($g_pdo->query($sql) as $row) {
            $user_nr = $row['user_nr'];
            $punkte = $row['punkte'];
        
            if ($user_nr == get_usernr()){
                $username = "<i class=\"fas fa-circle text-success\"></i> ". get_username_from_nr($user_nr);
            } else {
                $username = get_username_from_nr($user_nr);
            }
        
            echo "<td>".$username."</td><td>$punkte</td></tr>";
        
            // Wenn es noch weiter Tagessieger gibt -> neue Zeile
            if ($i < $anz){
                echo "<tr $active>";
            }
        
            $i++;
        }

    }

    echo "</table>";
    echo "</div>";


}

?>
