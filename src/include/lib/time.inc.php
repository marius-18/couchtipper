<?php
// Alle möglichen Sachen rund um Zeit

/// PFUUUUSCH


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

function akt_spieltag() {
    ##return 4;
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

function spt_select(){
    global $g_pdo;
    //return 31;
    $ende = 1;
    $spt = 0;

    while (($ende < time() ) && ($ende != "")){
        $spt++; 
        $datum = "";
        $sql = "SELECT datum FROM Datum WHERE spieltag='$spt'";
        
        foreach ($g_pdo->query($sql) as $row) {
            $datum = $row['datum'];
        }

        //if (date("w", $datum) == 2){ // FALLS ENGLISCHE WOCHE!
        if (true){
            $ende = $datum + (1*24*60*60);
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

?>
