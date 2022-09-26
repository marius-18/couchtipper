<?php
// Alle möglichen Sachen rund um Zeit

### Eine der wichtigsten Funktionen
### Ermittelt, ob wir bei der Buli in der Hinrunde oder in Rückrunde sind. 
### Wird (fast) überall genutzt, um den aktuellen Wettbewerb zu ermitteln
function get_curr_wett(){
    global $global_wett_id;
    
    if (wettbewerb_has_parts($global_wett_id)){
        // Decider: Hinrunde oder Rückrunde ?!
        if (akt_spieltag() <= 17 ) {
            $part = 0;
        } else {
            $part = 1;
        }
        
    } else {
        $part = 0;
    }

    return array($global_wett_id, $part);

}

### Ab hier: nochmal aufräumen!

function stamp_to_date($timestamp){

    if ($timestamp == 0) {
        return "";
    }
    
    $tage = array("Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag");
    $tag = date("w", $timestamp);

    $wochentag = $tage[$tag];

    $datum = date("d.m.Y - H:i", $timestamp);

    return $wochentag.", ".$datum."<br>";

}


function stamp_to_date_programm($timestamp){
    
    if ($timestamp == 0) {
        return "";
    }
    
    $tage = array("So","Mo","Di","Mi","Do","Fr","Sa");
    $tag = date("w", $timestamp);

    $wochentag = $tage[$tag];

    $datum = date("d.m. - H:i", $timestamp);

    return $wochentag.", ".$datum."";

}


function stamp_to_date_gruppe($timestamp){
    
    if ($timestamp == 0) {
        return "";
    }
    
    $tage = array("So","Mo","Di","Mi","Do","Fr","Sa");
    $tag = date("w", $timestamp);

    $wochentag = $tage[$tag];

    $datum = date("d.n. H:i", $timestamp);

    return $wochentag.", ".$datum."<br>";

}


function check_game_date($spieltag, $sp_nr) {
    // Prüft ob das entsprechende Spiel schon begonnen hat
    // true wenn spiel noch nicht begonnen hat

    global $g_pdo;
    global $g_modus; // Was ist das ?!

    $teil1 = "1";

    if (($g_modus != "WM") && ($g_modus != "EM") ){

        if ($spieltag > 17){
            $teil1 = "2";
            $spieltag = $spieltag - 17;
        }
    }

    $sql = "SELECT datum$teil1 AS datum FROM `Spieltage` WHERE ((spieltag = $spieltag) AND (sp_nr = $sp_nr))";


    foreach ($g_pdo->query($sql) as $row) {
        $datum = $row['datum'];
    }


    $timestamp = time();


    if ( $timestamp <= $datum ) {
        return true;
    } else {
        return false;
    }

}

function check_game_over($spieltag, $sp_nr) {
    // Prüft ob das entsprechende Spiel schon begonnen hat
    // true wenn spiel noch nicht begonnen hat

    global $g_pdo;
    global $g_modus; // Was ist das ?!

    $teil1 = "1";

    if (($g_modus != "WM") && ($g_modus != "EM") ){

        if ($spieltag > 17){
            $teil1 = "2";
            $spieltag = $spieltag - 17;
        }
    }

    $sql = "SELECT datum$teil1 AS datum FROM `Spieltage` WHERE ((spieltag = $spieltag) AND (sp_nr = $sp_nr))";


    foreach ($g_pdo->query($sql) as $row) {
        $datum = $row['datum'] + 90 * 60;
    }


    $timestamp = time();


    if ( $timestamp <= $datum ) {
        return true;
    } else {
        return false;
    }

}

function akt_spieltag(){
    #return 25;
    global $g_pdo;
    
    $sql = "Select max(spieltag) as spieltag FROM Datum where datum < (Select UNIX_TIMESTAMP(CURRENT_TIMESTAMP))";
    
    foreach ($g_pdo->query($sql) as $row) {
        $spieltag = $row['spieltag'];
    }   
        
    if ($spieltag == ""){
        $spieltag = 1;
    }
    
    return $spieltag;
}


function akt_spieltag_old() {
    //return 4;
    ### Das kann bestimmt auch die Datenbank!
    global $g_pdo;
    $datum = 1;
    $spt = 0;

    while (($datum < time()) && ($datum != "")){
        $spt ++;

        $datum = "";
        $sql = "SELECT datum FROM Datum WHERE spieltag='$spt'";
        foreach ($g_pdo->query($sql) as $row) {
            $datum = $row['datum'];
        }   
        
        //Abbruch für die Ligen.. 
        if ($spt>34){
            return 34;
            exit;
        }
    }

    $spt--;

    if ($spt<1){
        $spt = 1;
    }

    return $spt;
}



function last_spieltag(){
    return akt_spieltag() -1;
}

function spieltag_running($spt=""){
    // Ist wahr, wenn der Spieltag noch läuft..
    global $g_pdo;
    if ($spt == ""){
        $spt = akt_spieltag();
    }
    //Hin/rückrunde
    
    if ($spt>17){
        $state = "datum2";
        $spt -= 17;
    } else {
        $state = "datum1";
    }
    
    $sql = "SELECT max($state) as time FROM Spieltage WHERE spieltag = $spt";
    foreach ($g_pdo->query($sql) as $row) {
        $time = $row['time'];
    } 
    $time += 120 * 60; // 90min spiel + 15 min HZ + bisschen spiel für Nachspielzeit
    
    if (time() < $time) {
        // Spieltag läuft noch
        return 1;
    } else {
        return 0;
    }
}

function spt_select(){
    global $g_pdo;
    
    if (spieltag_running()){
        // Der laufende Spieltag wird immer zuerst angezeigt!!
        return akt_spieltag();
    }
    
    #return 3;
    $ende = 1;
    $spt = 0;

    while (($ende < time() ) && ($ende != "")){
        $spt++; 
        $datum = "";
        $sql = "SELECT datum FROM Datum WHERE spieltag='$spt'";
        
        foreach ($g_pdo->query($sql) as $row) {
            $datum = $row['datum'];
        }

        if (date("w", $datum) == 2){ // FALLS ENGLISCHE WOCHE!
        //if (true){
            $ende = $datum + (2*24*60*60);
        } else {
            $ende = $datum + (4*24*60*60);
        }

        if ($spt>34){
            return 34;
            exit;
        }
    }

    if ($spt<1){
        $spt = 1;
    }

    return $spt;
}



function spt_select1vlltnichtwichtig() { // spieltagsanzeige fuer SelectFormular

// AB MITTE DER WOCHE SOLL SCHON DER NEUE SPIELTAG ANGEZEIGT WERDEN !!!!!

//vllt über ende 
  global $g_pdo;
  $datum = 1;
  $spt = 0;

  $abzug = (3*24*60*60);
  $abzug = 0 ;

  while (($datum - $abzug < time()) && ($datum != "")){
    $spt ++;
    $datum = "";
    $sql = "SELECT datum FROM Datum WHERE spieltag='$spt'";
    foreach ($g_pdo->query($sql) as $row) {
      $datum = $row['datum'];
    }
echo "Spiel ". date("w", $datum);
    if (date("w", $datum) == 2){ // FALLS ENGLISCHE WOCHE!
      $abzug = (1*24*60*60);
    } else {
      $abzug = (3*24*60*60);
    }
    if ($spt>34){
      return 34;
      exit;
    }
  }

  $spt--;

  if ($spt<1){
    $spt = 1;
  }

  return $spt;
}

function check_gameday_bot_time($h, $min, $fenster){
    global $g_pdo;
    ##Testet, ob eigene Zeit im Fenster zum Senden von Bot Nachrichten ist!


    $monat = date("m");
    $tag   = date("d");
    $jahr  = date("Y");

    ### Zuerst prüfen, ob heute überhaupt ein Spieltag beginnt...
    $begin = mktime(0,0,59,$monat,$tag,$jahr);

    $sql = "SELECT spieltag FROM Datum WHERE datum=$begin";
    
    foreach ($g_pdo->query($sql) as $row) {
        $spieltag = $row['spieltag'];
    }
    
    if ($spieltag == ""){
        return 0;
    }
    
    
    ### Prüfen, ob die aktuelle Zeit im Fenster ist...
    $remind = mktime($h,$min,00,date("m"),date("d"),date("Y"));
    
    
    if ((time() - $remind <= $fenster) and (time() > $remind)){
        return 1;
    } else {
        return 0;
    }
}

function check_game_time($game_time, $timelist, $fenster){
    
    foreach ($timelist as $steps){
        $check_time = $game_time - $steps * 60;
        $diff = time() - $check_time;
        #echo "Zeit: ".time()." <br>";
        #echo "chk: $check_time <br>";
        #echo "diff: $diff <br>";
        if ((time() > $check_time) and ($diff <= $fenster)) {
            return true;
        } 
    }
    
    return false;
    
}
?>
