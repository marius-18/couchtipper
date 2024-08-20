<?php
include_once("src/include/code/tabelle.inc.php");

function update_tabelle($spieltag) {
    global $g_pdo;
    global $g_modus;
    #if (($g_modus == "WM") || ($g_modus == "EM")){
    if (is_big_tournament(get_curr_wett())){ 
        $max_group_spt = 13;
        if ($spieltag > $max_group_spt) {
            return 0;
        }
        $hilfe = 0;
    } else {

        if ($spieltag > 17) {
            $hilfe = 17;
        } else {
            $hilfe = 0;
        }
        
    }

    $sql = "SELECT Spieltage.spieltag AS spt, tore1, tore2, team1, team2 
            FROM `Ergebnisse`, Spieltage 
            WHERE (Spieltage.spieltag = Ergebnisse.spieltag - $hilfe) 
            AND (Ergebnisse.sp_nr = Spieltage.sp_nr) AND (Ergebnisse.spieltag = $spieltag)";
    $error = 0;
    foreach ($g_pdo->query($sql) as $row) {  
        $tore1 = $row['tore1'];
        $tore2 = $row['tore2'];
  
        if ($spieltag <= 17){
            $team1 = $row['team1'];
            $team2 = $row['team2'];
        } else {
            $team1 = $row['team2'];
            $team2 = $row['team1'];
        }

        // Erstmal nur die Heim-Mannschaft
        if ($tore1 > $tore2) {
            $sieg = 1;
            $unentschieden = 0;
            $niederlage = 0;
            $punkte = 3;
        }
        if ($tore1 == $tore2) {
            $sieg = 0;
            $unentschieden = 1;
            $niederlage = 0;
            $punkte = 1;
        }
        if ($tore1 < $tore2) {
            $sieg = 0;
            $unentschieden = 0;
            $niederlage = 1;
            $punkte = 0;
        }

        $sql1 = "INSERT INTO Tabelle (team_nr, spieltag, sieg, unentschieden, niederlage, punkte, tore, gegentore, heim)
                VALUES (:team1, :spieltag, :sieg, :unentschieden, :niederlage, :punkte, :tore1, :tore2, true)
                ON DUPLICATE KEY UPDATE sieg = :sieg, unentschieden= :unentschieden, niederlage = :niederlage, punkte = :punkte, tore = :tore1, gegentore = :tore2, heim = true";

        $statement = $g_pdo->prepare($sql1);
        $result = $statement->execute(array('team1' => $team1, 'spieltag' => $spieltag, 'sieg' => $sieg, 'unentschieden' => $unentschieden, 'niederlage' => $niederlage, 'punkte' =>
                                            $punkte, 'tore1' => $tore1, 'tore2' => $tore2));

        if ($result) {
            $error = 0;
        } else {
            $error = 1;
        }

        // Und jetzt auch noch die Auswärts-Mannschaften
        if ($tore1 < $tore2) {
            $sieg = 1;
            $unentschieden = 0;
            $niederlage = 0;
            $punkte = 3;
        } 
        if ($tore1 == $tore2) {
            $sieg = 0;
            $unentschieden = 1;
            $niederlage = 0;
            $punkte = 1;
        }
        if ($tore1 > $tore2) {
            $sieg = 0;
            $unentschieden = 0;
            $niederlage = 1;
            $punkte = 0;
        }

        $sql2 = "INSERT INTO Tabelle (team_nr, spieltag, sieg, unentschieden, niederlage, punkte, tore, gegentore, heim)
                VALUES (:team1, :spieltag, :sieg, :unentschieden, :niederlage, :punkte, :tore1, :tore2, false)
                ON DUPLICATE KEY UPDATE sieg = :sieg, unentschieden= :unentschieden, niederlage = :niederlage, punkte = :punkte, tore = :tore1, gegentore = :tore2, heim = false";

        $statement = $g_pdo->prepare($sql2);
        $result = $statement->execute(array('team1' => $team2, 'spieltag' => $spieltag, 'sieg' => $sieg, 'unentschieden' => $unentschieden, 'niederlage' => $niederlage, 'punkte' =>  
        $punkte,'tore1' => $tore2, 'tore2' => $tore1));

        if ($result) {
            $error = 0;
        } else {
            $error = 1;
        }

    }
    
    ##TODO: nur für Buli?!
    if (!is_big_tournament(get_curr_wett())){
        update_tabelle_platz($spieltag);
    }

    if ($error != 0) {
        echo "Es gab einen Fehler beim Aktualisieren";
    }
}



function update_rangliste($spieltag) {

    global $g_pdo;


    $sql = "SELECT user_nr, Tipps.tore1 as tipp1, Tipps.tore2 as tipp2, Ergebnisse.tore1, Ergebnisse.tore2 
            FROM `Tipps`, Ergebnisse WHERE (Tipps.spieltag = $spieltag) AND (Tipps.sp_nr = Ergebnisse.sp_nr) 
            AND (Tipps.spieltag = Ergebnisse.spieltag)";


    foreach ($g_pdo->query($sql) as $row) {
        $user_nr = $row['user_nr'];
        $user[$user_nr] = $user_nr;
        $tore1 = $row['tore1'];
        $tore2 = $row['tore2'];
        $tipp1 = $row['tipp1'];
        $tipp2 = $row['tipp2'];

        #echo "usr: $user_nr-";
        #echo "<br><";
        #echo $tore1.":";
        #echo $tore2;
        #echo "---";
        #echo $tipp1.":";
        #echo $tipp2;
        #echo "><br>";
        if (!isset($richtig[$user_nr])){
            $richtig[$user_nr] = 0;
        }
        if (!isset($differenz[$user_nr])){
            $differenz[$user_nr] = 0;
        }
        if (!isset($tendenz[$user_nr])){
            $tendenz[$user_nr] = 0;
        }
        if (!isset($punkte[$user_nr])){
            $punkte[$user_nr] = 0;
        }
        
        if (($tore1 == $tipp1) && ($tore2 == $tipp2)){
            $richtig[$user_nr] += 1;
            $differenz[$user_nr] += 0;
            $tendenz[$user_nr] += 0;
            $punkte[$user_nr] += 3;
            #    echo "jo1";
        }
        elseif ($tore1 - $tore2 == $tipp1 - $tipp2){
            $differenz[$user_nr] += 1;
            $tendenz[$user_nr] += 0;
            $richtig[$user_nr] += 0;   
            $punkte[$user_nr] += 2;
            #    echo "jo2";
        }
        elseif ((($tore1 - $tore2 > 0) && ($tipp1 - $tipp2 > 0)) || (($tore1 - $tore2 < 0) && ($tipp1 - $tipp2 < 0)) ){
            $tendenz[$user_nr] += 1;
            $differenz[$user_nr] += 0;
            $richtig[$user_nr] += 0;
            $punkte[$user_nr] += 1;
            #    echo "jo3";
        }
        else {
            $tendenz[$user_nr] += 0;
            $differenz[$user_nr] += 0;
            $richtig[$user_nr] += 0;
            $punkte[$user_nr] += 0;
            #    echo "jo4";
        }

        #echo "<br>--point";
        #echo $punkte[$user_nr];
        #echo "--<br>";
    }
    
    if (!isset($user_nr)){
        ## Datenbank gibt nichts zurück. Es gibt also noch kein Ergebnis, es muss also nichts geupdatet werden.. Daher Abbruch
        return;
    }

    foreach ($user as $user_nr) {

        $sql2 = "INSERT INTO Rangliste (user_nr, spieltag, richtig, tendenz, differenz, punkte)
                VALUES (:user, :spieltag, :richtig, :tendenz, :differenz, :punkte)
                ON DUPLICATE KEY UPDATE richtig = :richtig, tendenz= :tendenz, differenz = :differenz, punkte = :punkte";

        $statement = $g_pdo->prepare($sql2);
        $result = $statement->execute(array('user' => $user_nr, 'spieltag' => $spieltag, 'richtig' => $richtig[$user_nr], 'differenz' => $differenz[$user_nr], 'tendenz' => $tendenz[$user_nr], 'punkte' => $punkte[$user_nr]));
    }
    
    update_rangliste_position($spieltag);

}

function update_tabelle_platz($spieltag) {
    ## Geht durch den Spieltag und aktualisiert die Platzierung für jeden Verein an dem entsprechenden Spieltag
    global $g_pdo;
    
    list ($punkte, $tore, $gegentore, $diff, $team_name, $sieg, $unentschieden, $niederlage, $modus, $nummer) = tabelle(1,0,$spieltag);
    
    foreach ($nummer as $key => $team_nr){
        $curr_platz = $key +1;
        
        ## Check if same platz
        if ($key > 0){
            if (($punkte[$key -1] == $punkte[$key]) && 
                ($tore[$key] - $gegentore[$key] == $tore[$key-1] - $gegentore[$key-1] ) &&
                ($tore[$key] == $tore[$key-1])){
                $platz[$team_nr] = $platz_halten;
            } else {
                $platz[$team_nr] = $curr_platz;
                $platz_halten = $curr_platz;
            }
        } else {
            $platz[$team_nr] = $curr_platz;
                $platz_halten = $curr_platz;
        }
        
        $sql = "UPDATE `Tabelle` SET `platz`= ".$platz[$team_nr]." WHERE `team_nr` = $team_nr AND `spieltag`= $spieltag";

        $statement = $g_pdo->prepare($sql);
        $result = $statement->execute();
    }
}

function update_rangliste_position($spieltag) {
    global $g_pdo;
    
    // TODO: Bei modus buli vllt einfach ab 18 wieder von 0 anfangen.. wenn jemand nicht getippt hatte, dann einfach mit max( spieler) vollmachen... // Das muss auf jeden fall zum spieler eintritt..
    // oder halt einfach bei der statistik am ende alles auf max setzen.. ist vermutlich einfacher..
    
    if (($spieltag > 17) && !is_big_tournament(get_curr_wett())) {
        $start = 18;
    } else {
        $start = 1;
    }
    
     $sql = "SELECT sum(punkte) as p, Rangliste.user_nr  
            FROM `Rangliste`
            WHERE (Rangliste.spieltag >= $start AND Rangliste.spieltag <= $spieltag) 
            GROUP BY Rangliste.user_nr
            ORDER BY p DESC";

    $platz = 1;
    $last_punkte = -1; ### sonst last_punkte undefiniert am Anfang
    foreach ($g_pdo->query($sql) as $row) {
        $user_nr = $row['user_nr'];
        $punkte[$user_nr] = $row['p'];
        $user[$user_nr] = $user_nr;
        
        
        if ($punkte[$user_nr] == $last_punkte){
            $platz_r[$user_nr] = $platz_halten;
            $platz_halten = $platz_r[$user_nr];

        } else {
            $platz_r[$user_nr] = $platz;
            $platz_halten = $platz;
        }
        
        $last_punkte = $punkte[$user_nr];
        $platz++;
    }
    
    
    foreach ($user as $user_nr) {

        $sql2 = " UPDATE `Rangliste` SET `platz`= :platz WHERE user_nr = :userid AND spieltag = :spt";

        $statement = $g_pdo->prepare($sql2);
        $result = $statement->execute(array('userid' => $user_nr, 'platz' => $platz_r[$user_nr], 'spt' => $spieltag));
    }

}


#update_rangliste_position(18);
#clear_rangliste();

function clear_rangliste() {
    global $g_pdo;

    $sql = "SELECT user_nr FROM User WHERE 1";

    foreach ($g_pdo->query($sql) as $row) {
        $nr = $row['user_nr'];

        $sql_insert = "INSERT INTO `Rangliste`(`user_nr`, `richtig`, `tendenz`, `differenz`, `punkte`, `spieltag`) 
                        VALUES ($nr,0,0,0,0,1)";
        $statement = $g_pdo->prepare($sql_insert);
        $result = $statement->execute();

    }

}

function ko_sieger($spieltag, $spiel){
    global $g_pdo;
    $sql = "SELECT team1, team2, tore1, tore2 FROM `Spieltage`, Ergebnisse WHERE 
            (Spieltage.spieltag = Ergebnisse.spieltag) AND 
            (Spieltage.sp_nr = Ergebnisse.sp_nr) AND
            (Spieltage.spieltag = $spieltag) AND
            (Spieltage.sp_nr = $spiel)";

    foreach ($g_pdo -> query($sql) as $row){
        $tore1 = $row['tore1'];
        $tore2 = $row['tore2'];

        $team1 = $row['team1'];
        $team2 = $row['team2'];
    }

        if (!isset($tore1) ||  !isset($tore2) || ($tore1 == "") || ($tore2 == "")){ ##Zu oder geändert.. fehler?
            return null; 
        }
        
        if ($tore1 > $tore2) {
            return $team1;
        } elseif ($tore2 > $tore1){
            return $team2;
        }
      

   
}

function update_ko($spieltag, $spiel, $teamteil, $team_nr){
    global $g_pdo;
    
    #return 0;
    ### ACHTUNG, ANPASSEN!

    $sql = "UPDATE `Spieltage` SET `team$teamteil`=:team_nr WHERE spieltag = :spieltag AND sp_nr = :spiel";
    
    $statement = $g_pdo->prepare($sql);
    $result = $statement->execute(array('team_nr' => $team_nr, 'spieltag' => $spieltag, 'spiel' => $spiel));
    
}

function update_all_ko_spiele(){
    // VF 1
    update_ko(18,1,1,ko_sieger(15,2));
    update_ko(18,1,2,ko_sieger(14,2));
    
    // VF 2
    update_ko(18,2,1,ko_sieger(16,2));
    update_ko(18,2,2,ko_sieger(16,1));
    
    // VF 3
    update_ko(19,1,1,ko_sieger(15,1));
    update_ko(19,1,2,ko_sieger(14,1));
    
    // VF 4
    update_ko(19,2,1,ko_sieger(17,1));
    update_ko(19,2,2,ko_sieger(17,2));
    
    
    // HF 1
    update_ko(20,1,1,ko_sieger(18,1));
    update_ko(20,1,2,ko_sieger(18,2));
    
    // HF 2
    update_ko(21,1,1,ko_sieger(19,2));
    update_ko(21,1,2,ko_sieger(19,1));  

    // Finale
    update_ko(22,1,1,ko_sieger(20,1));
    update_ko(22,1,2,ko_sieger(21,1));    
}


function ko_gruppe($gruppe, $platz){
    ## Gibt die team_nr des Teams zurück, das in Gruppe $gruppe auf Platz: $platz steht.
    $platz -= 1; ## Array fängt bei 0 an...
    list($punkte, $tore, $gegentore, $diff, $team_name, $sieg, $unentschieden, $niederlage, $gruppe, $team_nr) = wm_tabelle($gruppe);
    
    
    if ($sieg[$platz] + $unentschieden[$platz] + $niederlage[$platz] == 3){
        ## Prüfen, ob schon alle Spiele gespielt.., vllt noch auf spielende Warten?
        return $team_nr[$platz];
    } else {
        ## Gruppe noch nicht beendet.. Noch nichts eingeben..
        return 0;
    }
        
}


function update_gruppenbeste(){
    ## AF 1
    update_ko(14,1,1,ko_gruppe("A", 2));
    update_ko(14,1,2,ko_gruppe("B", 2));
    
    ## AF 2
    update_ko(14,2,1,ko_gruppe("A", 1));
    update_ko(14,2,2,ko_gruppe("C", 2));

    ## AF 3
    update_ko(15,1,1,ko_gruppe("C", 1));
    ##update_ko(15,1,2,ko_gruppe("A", 2));

    ## AF 4
    update_ko(15,2,1,ko_gruppe("B", 1));
    ##update_ko(15,2,2,ko_gruppe("A", 2));

    ## AF 5
    update_ko(16,1,1,ko_gruppe("D", 2));
    update_ko(16,1,2,ko_gruppe("E", 2));

    ## AF 6
    update_ko(16,2,1,ko_gruppe("F", 1));
    ##update_ko(16,2,2,ko_gruppe("A", 2));

    ## AF 7
    update_ko(17,1,1,ko_gruppe("E", 1));
    ##update_ko(17,1,2,ko_gruppe("A", 2));

    ## AF 8
    update_ko(17,2,1,ko_gruppe("D", 1));
    update_ko(17,2,2,ko_gruppe("F", 2));    
}

/*
function check_finals() {
    // Das muss auf jeden Fall angepasst werden!
    # erstmal sql insert
    global $g_pdo;

    return 0;
    ### Das muss dann nochmal aktiviert werden...
    
    $sql = "
        UPDATE `Spieltage` SET `team1`=".ko_gruppe(C,1).",`team2`=".ko_gruppe(D,2)." WHERE spieltag = '16' AND sp_nr = '1';
        UPDATE `Spieltage` SET `team1`=".ko_gruppe(A,1).",`team2`=".ko_gruppe(B,2)." WHERE spieltag = '16' AND sp_nr = '2';
        UPDATE `Spieltage` SET `team1`=".ko_gruppe(B,1).",`team2`=".ko_gruppe(A,2)." WHERE spieltag = '17' AND sp_nr = '1';
        UPDATE `Spieltage` SET `team1`=".ko_gruppe(D,1).",`team2`=".ko_gruppe(C,2)." WHERE spieltag = '17' AND sp_nr = '2';

        UPDATE `Spieltage` SET `team1`=".ko_gruppe(E,1).",`team2`=".ko_gruppe(F,2)." WHERE spieltag = '18' AND sp_nr = '1';
        UPDATE `Spieltage` SET `team1`=".ko_gruppe(G,1).",`team2`=".ko_gruppe(H,2)." WHERE spieltag = '18' AND sp_nr = '2';
        UPDATE `Spieltage` SET `team1`=".ko_gruppe(F,1).",`team2`=".ko_gruppe(E,2)." WHERE spieltag = '19' AND sp_nr = '1';
        UPDATE `Spieltage` SET `team1`=".ko_gruppe(H,1).",`team2`=".ko_gruppe(G,2)." WHERE spieltag = '19' AND sp_nr = '2';

        
        UPDATE `Spieltage` SET `team1`=".ko_sieger(16,1,1).",`team2`=".ko_sieger(16,2,1)." WHERE spieltag = '20' AND sp_nr = '1';
        UPDATE `Spieltage` SET `team1`=".ko_sieger(18,1,1).",`team2`=".ko_sieger(18,2,1)." WHERE spieltag = '20' AND sp_nr = '2';
        UPDATE `Spieltage` SET `team1`=".ko_sieger(19,1,1).",`team2`=".ko_sieger(19,2,1)." WHERE spieltag = '21' AND sp_nr = '1';
        UPDATE `Spieltage` SET `team1`=".ko_sieger(17,1,1).",`team2`=".ko_sieger(17,2,1)." WHERE spieltag = '21' AND sp_nr = '2';

            
        UPDATE `Spieltage` SET `team1`=".ko_sieger(20,1,1).",`team2`=".ko_sieger(20,2,1)." WHERE spieltag = '22' AND sp_nr = '1';
        UPDATE `Spieltage` SET `team1`=".ko_sieger(21,1,1).",`team2`=".ko_sieger(21,2,1)." WHERE spieltag = '23' AND sp_nr = '1';


        UPDATE `Spieltage` SET `team1`=".ko_sieger(22,1,0).",`team2`=".ko_sieger(23,1,0)." WHERE spieltag = '24' AND sp_nr = '1';

        
        UPDATE `Spieltage` SET `team1`=".ko_sieger(22,1,1).",`team2`=".ko_sieger(23,1,1)." WHERE spieltag = '25' AND sp_nr = '1';

        ";

        $statement = $g_pdo->prepare($sql);
        $result = $statement->execute();
}*/

// HIer muss das irgenwie ausgeführt werden....?
//check_finals();

#function check_finals() {
#
#
#}

#function check_af() {
#
#}


?>
