<?php
/*
// Zuständig für Eingabe Seiten von Spielen!
*/
// Gibt SpielNummer zurück
function get_sp_nr ($e) {
    $ret = round($e/2, 0, PHP_ROUND_HALF_UP);
    return $ret;
}

// Gibt Nummer des Teams zurück (team 1 oder 2)
function get_t_nr ($e) {
    $ret = $e % 2;

    if ($ret == 0){
        $ret = 2;
    }
    
    return $ret;
}

// Prüft ob Eingabe pos. Zahl ist
function is_pos_int($z){
    if ( (is_numeric($z)) && ($z >= 0) && ($z <= 99) ){  
        return true;
    } else {
        return false;
    }
}

//prüft ob das Array leer ist
function array_empty($a){
    $out = true;
    foreach ($a as $x => $y) {
        if ($y != ""){
            $out = false;
        }
    }
    return $out;
}


// EINGABE VON TIPPS / ERGEBNISSEN
// Modus = "Tipps" oder "Ergebnisse"
// Wenn $admin = true nehmen wir nicht die Spieler Nummer, sondern die vom Admin vorgesehne
// Damit ein Admin Tipps bei ANDEREN Spielern eingeben darf.

function input($spieltag, $modus, $admin, $admin_nr){  
    global $g_pdo;

    $error = false;
    $error_msg = "";
    $e = array();
    
    if (isset($_POST['erg11'])){
        $e[1] = $_POST['erg11'];
    }
    if (isset($_POST['erg12'])){
        $e[2] = $_POST['erg12'];
    }
    if (isset($_POST['erg21'])){
        $e[3] = $_POST['erg21'];
    }
    if (isset($_POST['erg22'])){
        $e[4] = $_POST['erg22'];
    }
    if (isset($_POST['erg31'])){
        $e[5] = $_POST['erg31'];
    }
    if (isset($_POST['erg32'])){
        $e[6] = $_POST['erg32'];
    }
    if (isset($_POST['erg41'])){
        $e[7] = $_POST['erg41'];
    }
    if (isset($_POST['erg42'])){
        $e[8] = $_POST['erg42'];
    }
    if (isset($_POST['erg51'])){
        $e[9] = $_POST['erg51'];
    }
    if (isset($_POST['erg52'])){
        $e[10] = $_POST['erg52'];
    }
    if (isset($_POST['erg61'])){
        $e[11] = $_POST['erg61'];
    }
    if (isset($_POST['erg62'])){
        $e[12] = $_POST['erg62'];
    }
    if (isset($_POST['erg71'])){
        $e[13] = $_POST['erg71'];
    }
    if (isset($_POST['erg72'])){
        $e[14] = $_POST['erg72'];
    }
    if (isset($_POST['erg81'])){
        $e[15] = $_POST['erg81'];
    }
    if (isset($_POST['erg82'])){
        $e[16] = $_POST['erg82'];
    }
    if (isset($_POST['erg91'])){
        $e[17] = $_POST['erg91'];
    }
    if (isset($_POST['erg92'])){
        $e[18] = $_POST['erg92'];
    }

    if (!array_empty($e)){ //es wurde was übergeben

        for ($heim = 1; $heim <= 18; $heim = $heim + 2){
            $aus = $heim + 1;

            if (isset($e[$heim]) && isset($e[$aus]) && ($e[$heim] != "") && ($e[$aus] != "") ) {
                $input_nr = get_sp_nr($heim); //Spielnummer
                $input_spt = $spieltag;       //Spieltag

                if (!$admin) {
                    $input_user_nr = get_usernr();
                } else {
                    $input_user_nr = $admin_nr;
                }
      
                $debug_user = get_username(); //zur Kontrolle

                $input_ip = $_SERVER['REMOTE_ADDR'];

                $input_tore1 = $e[$heim];
                $input_tore2 = $e[$aus];

                if ((is_pos_int($input_tore1)) && (is_pos_int($input_tore2))){
                    // Prüfe ob Eingaben erlaubt sind (für tipps entweder berechtigung oder vorm spiel, für Ergebnisse berechtigung)
                    if (($modus == "Tipps" && check_game_date($input_spt, $input_nr)) || (($modus == "Ergebnisse" && allow_erg()) && !check_game_date($input_spt, $input_nr)) || ($modus == "Tipps" && allow_tipps())) {  

                        if ($modus == "Tipps"){
                            $sql = "INSERT INTO Tipps (spieltag, sp_nr, user_nr, tore1, tore2, debug_user, debug_ip) 
                                    VALUES (:spieltag, :sp_nr, :user_nr, :tore1, :tore2, :user, :ip)
                                    ON DUPLICATE KEY UPDATE tore1 = :tore1, tore2 = :tore2, debug_user = :user, debug_ip = :ip, debug_time = :time";
                        } else {
                            if ($modus == "Ergebnisse") {
                                $sql = "INSERT INTO Ergebnisse (spieltag, sp_nr, tore1, tore2, debug_user, debug_ip) 
                                        VALUES (:spieltag, :sp_nr, :tore1, :tore2, :user, :ip)
                                        ON DUPLICATE KEY UPDATE tore1 = :tore1, tore2 = :tore2, debug_user = :user, debug_ip = :ip, debug_time = :time";
                            }
                        }

                        $statement = $g_pdo->prepare($sql);

                        $result = $statement->execute(array('spieltag' => $input_spt, 'sp_nr' => $input_nr, 'user_nr' => $input_user_nr, 'tore1' => $input_tore1, 
                                                            'tore2' => $input_tore2, 'user' => $debug_user, 'ip' => $input_ip, 'time' => date('Y-m-d H:i:s')));


                        if ($result == true) {
                            $error_msg = "Die $modus wurden fehlerlos eingegeben.";

                            if ($modus == "Ergebnisse"){
                                update_tabelle($input_spt);
                                //clear_rangliste();
                                update_rangliste($input_spt);
                            }
                            if ($modus == "Tipps"){
                                //clear_rangliste();
                                update_rangliste($input_spt);
                            }

                        } 	

                    } else {
                        
                        $error = true;
                        if ($modus == "Ergebnisse"){
                            if (check_game_date($input_spt, $input_nr)){
                                $error_msg =  "Das Spiel hat noch nicht begonnen. <br>Du kannst noch keine Ergebnisse eingeben!";

                            } else {
                            
                                $error_msg =  "Du bist nicht berechtigt Ergebnisse einzugeben! <br> Frage einen Administrator nach den Rechten";
                            }
                        }
          
                        if ($modus == "Tipps"){
                            $error_msg = "Du kannst dieses Spiel nicht mehr tippen! <br>Du bist leider zu sp&auml;t.<br>Tippe beim n&auml;chsten Mal etwas fr&uuml;her";
                        }
                    }
 
                } else {
                    $error = true;
                    $error_msg =  "Die Eingaben sind nicht korrekt. Bitte nur positive Zahlen <100 verwenden!";
                }

            }

        }

    } // END OF INPUT



    return array ($error, $error_msg);


}


function input_cronjob($input_spt){
    global $g_pdo;
    $jahr = substr(get_wettbewerb_jahr(get_curr_wett()), 0, 4);
    $modus = get_openliga_shortcut(get_curr_wett());
    
    list($tore_heim, $tore_aus) = get_ergebnis($input_spt, $modus, $jahr);

    foreach ($tore_heim as $input_nr => $value){
        
        $input_tore1 = $tore_heim[$input_nr];
        $input_tore2 = $tore_aus[$input_nr];
        if ((is_pos_int($input_tore1)) && (is_pos_int($input_tore2))){
            // Prüfe ob Eingaben erlaubt sind (für tipps entweder berechtigung oder vorm spiel, für Ergebnisse berechtigung)
            if ( !check_game_date($input_spt, $input_nr) ) {  
                // Gibt nur Ergebnisse automatisch ein, wenn ich nichts eingegeben hatte (damit ich überschreiben kann)
                $sql = "INSERT INTO Ergebnisse (spieltag, sp_nr, tore1, tore2, debug_user, debug_ip) 
                        VALUES (:spieltag, :sp_nr, :tore1, :tore2, :user, :ip)
                        ON DUPLICATE KEY UPDATE tore1 = IF (debug_user = 'Marius', tore1, :tore1), tore2 = IF (debug_user = 'Marius', tore2, :tore2), 
                        debug_user = IF (debug_user = 'Marius', debug_user, :user), debug_ip = IF (debug_user = 'Marius', debug_ip, :ip), 
                        debug_time = IF (debug_user = 'Marius', debug_time, :time)";

                $statement = $g_pdo->prepare($sql);
                $result = $statement->execute(array('spieltag' => $input_spt, 'sp_nr' => $input_nr, 'tore1' => $input_tore1, 
                                                    'tore2' => $input_tore2, 'user' => 'BOT', 'ip' => '127.0.0.1', 'time' => date('Y-m-d H:i:s')));

                if ($result) {
                    $error_msg = "Die Ergebnisse wurden fehlerlos eingegeben.";
                    update_tabelle($input_spt);
                    //clear_rangliste();
                    update_rangliste($input_spt);
                }
            }
 
        } else {
            $error = true;
            $error_msg =  "Die Eingaben sind nicht korrekt. Bitte nur positive Zahlen <100 verwenden!";
        }
    }
}


?>
