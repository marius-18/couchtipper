<link href='src/include/styles/ko1.css' rel='stylesheet'/>
<script>
$(document).ready(function(){
  $('[data-toggle="popover"]').popover();
});
</script>

<div class="container-fluid">
<?php
require_once('src/include/code/refresh.php');

$mannschaften = 16;
$maxEbene = log($mannschaften, 2);
$gesamtgroesse = 1.5*$mannschaften;
    
print_ko($mannschaften, $maxEbene, $gesamtgroesse);



function print_ko($mannschaften, $maxEbene, $gesamtgroesse){
    
    echo "<div class=\"table-responsive\">";
    echo "<table class=\"table table-sm text-nowrap  table-borderless text-center center\">";

    for($zeile = 1; $zeile <= $gesamtgroesse; $zeile++) {
        echo "<tr>";
        for($ebene = 1; $ebene <= $maxEbene; $ebene++) {
            zelle($zeile, $ebene);
        }
        echo "</tr>";
    }
    echo "</table></div>";
}


function zelle($zeile, $spalte){

    $start_oben = ceil(pow(2,$spalte-2) * 3);
    $start_unten = $start_oben +1;
    $start_title = $start_oben-1;
    $counter = pow(2,$spalte-1) * 3;
    
    if ((($zeile  - $start_oben) % $counter ) == 0) {
        ## Oberes Team
        if (get_wettbewerb_code(get_curr_wett()) == "WM" && get_wettbewerb_jahr(get_curr_wett()) == "2018"){
            echo team_label_2018($zeile,$spalte, "top")."";            
        } else {
            echo team_label($zeile,$spalte, "top")."";
        }
        return;
    }
    
    if ((($zeile - $start_unten) % $counter ) == 0) {
        // Unteres Team
        if (get_wettbewerb_code(get_curr_wett()) == "WM" && get_wettbewerb_jahr(get_curr_wett()) == "2018"){
            echo team_label_2018($zeile,$spalte, "down")."";            
        } else {
            echo team_label($zeile,$spalte, "down")."";
        }
        return;
    }
    
    if ((($zeile  - $start_title) % $counter ) == 0) {
        // In diesem Feld steht der Titel
        echo "<td colspan=\"2\">";
        label($zeile,$spalte,"name");
        echo "</td>";
        return;
    }
    
    echo "<td></td><td></td>";

    
}

function help($i){
    
    if (get_wettbewerb_jahr(get_curr_wett()) == "2020"){
        $array = array(
            1 => 5, 
            2 => 6, 
            3 => 2, 
            4 => 4, 
            5 => 1,
            6 => 3,
            7 => 7, 
            8 => 8);
    }
    
    if (get_wettbewerb_jahr(get_curr_wett()) == "2018"){
        $array = array(
            1 => 2, 
            2 => 1, 
            3 => 5, 
            4 => 6, 
            5 => 3,
            6 => 4,
            7 => 7, 
            8 => 8);
    }
    
    if (get_wettbewerb_jahr(get_curr_wett()) == "2024"){
        $array = array(
            1 => 1, 
            2 => 3, 
            3 => 5, 
            4 => 6, 
            5 => 7,
            6 => 8,
            7 => 2, 
            8 => 4);
    }
    
    
    return $array[$i];
    
}

function label($zeile, $spalte, $typ){
    if ($typ == "name"){
        switch ($spalte){
            case 1: 
                echo "AF ".help(($zeile+2)/3);
                echo "<a data-toggle=\"popover\" title=\"Achtelfinale ".help(($zeile+2)/3) ."\" data-content=\" " .datum(1,help(($zeile+2)/3))."\"> <i class=\"far fa-clock\"></i></a>";
                break;
            case 2:
                echo "VF ".($zeile+4)/6;
                echo "<a data-toggle=\"popover\" title=\"Viertelfinale ".($zeile+4)/6 ."\" data-content=\" " .datum(2,($zeile+4)/6)."\"> <i class=\"far fa-clock\"></i></a>";
                break;

            case 3:
                echo "HF ".($zeile+7)/12;
                echo "<a data-toggle=\"popover\" title=\"Halbfinale ".($zeile+7)/12 ."\" data-content=\" " .datum(3,($zeile+7)/12)."\"> <i class=\"far fa-clock\"></i></a>";
                break;
            case 4: 
                echo "Finale<a data-toggle=\"popover\" title=\"Finale\" data-content=\"".datum(4,1) ."\"> <i class=\"far fa-clock\"></i></a>";
                break;
        }
    }
}


function teams($spieltag, $spiel, $sonst, $team_pos, $mode){
    global $g_pdo;
    $sql = "SELECT t1.team_name AS team1, t2.team_name AS team2 FROM `Spieltage`, Teams AS t1, Teams AS t2 WHERE 
           ((team1 = t1.team_nr) AND (team2 = t2.team_nr) AND (spieltag = $spieltag) AND (sp_nr = $spiel) )";
    
    foreach ($g_pdo->query($sql) as $row) {
        $team[1] = $row['team1'];
        $team[2] = $row['team2'];
    }
    
    if ($team[$team_pos] != "TBD") {
        $tm =  $team[$team_pos];
    } else {
        $tm =  $sonst;
    }
    
    $erg = erg($spieltag, $spiel, $team_pos);
    $result = "<td class=\"erg_$mode\">$erg</td><td class=\"spiel_$mode \"> ".$tm."</td>";
    
    return $result;
}

function erg($spieltag,$spiel,$team_pos){
    global $g_pdo;
    
    $sql = "SELECT tore1, tore2 FROM Ergebnisse WHERE ((spieltag = $spieltag) AND (sp_nr = $spiel))";
    foreach ($g_pdo->query($sql) as $row) {
        $tore[1] = $row['tore1'];
        $tore[2] = $row['tore2'];
    }
    
    return $tore[$team_pos];
}

function datum($spieltag, $spiel){
    ## Gibt das Datum des ausgewählten Spiel zurück
    global $g_pdo;
    
    ## EM Übersetzung: KO Spiele zwischen 14 und 22. spieltag aufteilung usw..
    $array = [
       1 => [ 1 => array(14,1), 2 => array(14,2), 3 => array(15,1),   4 => array(15,2), 5 => array(16,1), 6 => array(16,2), 7 => array(17,1), 8 => array(17,2) ],
       2 => [ 1 => array(18,1), 2 => array(18,2), 3 => array(19,1),   4 => array(19,2)],
       3 => [ 1 => array(20,1), 2 => array(21,1)],
       4 => [ 1 => array(22,1) ]];
       
    $erg = $array[$spieltag][$spiel];
    
    $real_spt = $erg[0];
    $real_spl = $erg[1];
    
    $sql = "SELECT datum1, stadt FROM `Spieltage`, `Spielorte` WHERE spieltag = $real_spt AND sp_nr = $real_spl AND `Spielorte`.id = `Spieltage`.spielort";
    foreach ($g_pdo->query($sql) as $row) {
        $date = $row['datum1'];
        $spielort = $row['stadt'];
    }
    
    return stamp_to_date_programm($date)." ".$spielort;
}





    
    
    
function team_label($zeile, $spalte, $mode){
    if ($spalte == 1){
        $af_zeile = help(ceil($zeile/3));
        $af_spieltage = [0,14,15,16,17];
        
        $af_spieltag = $af_spieltage[ceil($af_zeile/2)];
        $af_spiel = ($af_zeile+1)%2+1;
        
        $af_position = ($zeile+1)%3+1;
        
        $AF = [
            1 => array("","2. Gruppe A","2. Gruppe B"),
            2 => array("","1. Gruppe A","2. Gruppe C"),
            
            3 => array("","1. Gruppe C","3. Gruppe D/E/F"),
            4 => array("","1. Gruppe B","3. Gruppe A/D/E/F"),
            
            5 => array("","2. Gruppe D","2. Gruppe E"),
            6 => array("","1. Gruppe F","3. Gruppe A/B/C"),
            
            7 => array("","1. Gruppe E","3. Gruppe A/B/C/D"),
            8 => array("","1. Gruppe D","2. Gruppe F"),
        
        ];
        
        return teams($af_spieltag,$af_spiel,$AF[$af_zeile][$af_position],$af_position, $mode);
        
    }
    
    if ($spalte == 2){
        ## Viertelfinale
        $vf_zeile = ceil($zeile/3);
        
        ## VF auf 2 Spieltage aufgeteilt..
        if ($vf_zeile <= 4){
            $vf_spt = 18;
        } else {
            $vf_spt = 19;
        }
        
        ## Team Position: immer abwechselnd 1 und 2..
        $vf_team_pos = (($vf_zeile+1) % 2)+1;
        
        ## Spiel Nummer: zwei mal 1, dann zwei mal 2.. (für jedes Spiel 2 mannschaften)
        $vf_spiel = ((ceil($vf_zeile/2)+1) % 2)+1;
        
        return teams($vf_spt,$vf_spiel,"Sieger AF ".help($vf_zeile),$vf_team_pos, $mode);
        
    }
    if ($spalte == 3){
        switch (ceil($zeile/6)) {
            case 1:
                return teams(20,1,"Sieger VF 1",1, $mode);
                break;
            case 2:
                return teams(20,1,"Sieger VF 2",2, $mode);
                break;        
            case 3:
                return teams(21,1,"Sieger VF 3",1, $mode);
                break;
            case 4:
                return teams(21,1,"Sieger VF 4",2, $mode);
                break;              
        }        
    }
    if ($spalte == 4){
        switch (ceil($zeile/12)) {
            case 1:
                return teams(22,1,"Sieger HF 1",1, $mode);
                break;
            case 2:
                return teams(22,1,"Sieger HF 2",2, $mode);
                break;                     
        }         
    }
}

function team_label_2018($zeile, $spalte, $mode){
    if ($spalte == 1){
        switch ($zeile) {
            case 2:
                return teams(16,2,"2. Gruppe D",1, $mode);
                break;
            case 3:
                return teams(16,2,"2. Gruppe E",2, $mode);
                break;        
            case 5:
                return teams(16,1,"1. Gruppe F",1, $mode);
                break;
            case 6:
                return teams(16,1,"3. Gruppe A/B/C ",2, $mode);
                break;         
            case 8:
                return teams(18,1,"1. Gruppe A",1, $mode);
                break;
            case 9:
                return teams(18,1,"2. Gruppe C",2, $mode);
                break;        
            case 11:
                return teams(18,2,"1. Gruppe B",1, $mode);
                break;
            case 12:
                return teams(18,2,"3. Gruppe A/D/E/F",2, $mode);
                break;
            case 14:
                return teams(17,1,"2. Gruppe A",1, $mode);
                break;
            case 15:
                return teams(17,1,"2. Gruppe B",2, $mode);
                break;        
            case 17:
                return teams(17,2,"1. Gruppe C",1, $mode);
                break;
            case 18:
                return teams(17,2,"3. Gruppe D/E/F",2, $mode);
                break;         
            case 20:
                return teams(19,1,"1. Gruppe D",1, $mode);
                break;
            case 21:
                return teams(19,1,"2. Gruppe F",2, $mode);
                break;        
            case 23:
                return teams(19,2,"1. Gruppe E",1, $mode);
                break;
            case 24:
                return teams(19,2,"3. Gruppe A/B/C/D",2, $mode);
                break; 
        }

        return "<td class=\"erg_$mode\">1</td><td class=\"spiel_$mode \"> "."Sieger Gruppe X";
    }
    if ($spalte == 2){
        switch (ceil($zeile/3)) {
            case 1:
                return teams(20,1,"Sieger AF 5",1, $mode);
                break;
            case 2:
                return teams(20,1,"Sieger AF 6",2, $mode);
                break;        
            case 3:
                return teams(20,2,"Sieger AF 2",1, $mode);
                break;
            case 4:
                return teams(20,2,"Sieger AF 4",2, $mode);
                break;         
            case 5:
                return teams(21,2,"Sieger AF 1",1, $mode);
                break;
            case 6:
                return teams(21,2,"Sieger AF 3",2, $mode);
                break;        
            case 7:
                return teams(21,1,"Sieger AF 7",1, $mode);
                break;
            case 8:
                return teams(21,1,"Sieger AF 8",2, $mode);
                break;         
        }
        
    }
    if ($spalte == 3){
        switch (ceil($zeile/6)) {
            case 1:
                return teams(22,1,"Sieger VF 1",1, $mode);
                break;
            case 2:
                return teams(22,1,"Sieger VF 2",2, $mode);
                break;        
            case 3:
                return teams(23,1,"Sieger VF 3",1, $mode);
                break;
            case 4:
                return teams(23,1,"Sieger VF 4",2, $mode);
                break;              
        }        
    }
    if ($spalte == 4){
        switch (ceil($zeile/12)) {
            case 1:
                return teams(25,1,"Sieger HF 1",1, $mode);
                break;
            case 2:
                return teams(25,1,"Sieger HF 2",2, $mode);
                break;                     
        }         
    }
}

?> 


</div>
