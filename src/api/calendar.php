<?php
//This is the most important coding.
header("Content-Type: text/Calendar; charset=utf-8");
header("Content-Disposition: inline; filename=couchtipper-Saisonkalender.ics");

require_once("src/include/code/programm.inc.php");

function create_team_events($team){
    
    if (!is_big_tournament(get_curr_wett())){
        list($spieltag, $team_nr1, $team_nr2, $team_name, $datum, $zeitraum, $tore1, $tore2) = programm($team,1,34);
    } else {
        list($spieltag, $team_nr1, $team_nr2, $team_name, $datum, $zeitraum, $stadt, $stadion) = tournament_programm($team, "Alles");        
    }
    
    foreach ($team_nr1 as $spt => $team1) {
        
        if ($team1 == $team){
            if (!is_big_tournament(get_curr_wett())){
                $title = get_team_open_db_name($team_nr2[$spt]);#$team_name[$spt];
                $title .= " Heimspiel";
                $location = get_team_city($team)." - ".get_team_stadium($team);
            } else {
                $title = $team_name[$spt];
                $location = $stadt[$spt]." - ".$stadion[$spt];
            }
        } else {
            if (!is_big_tournament(get_curr_wett())){
                $title = get_team_open_db_name($team_nr1[$spt]);#$team_name[$spt];
                $title .= " Auswärtsspiel";
                $location = get_team_city($team_nr1[$spt])." - ".get_team_stadium($team_nr1[$spt]);
            } else{
                $title = $team_name[$spt];
                $location = $stadt[$spt]." - ".$stadion[$spt];
            }
        }
        
        if ($datum[$spt] != 0){
            $starttime = stamp_to_cal($datum[$spt]);
            $endtime = stamp_to_cal($datum[$spt] +90*60 + 15*60);
        } else {
            $starttime = stamp_to_cal($zeitraum[$spt][0]);
            $endtime  = stamp_to_cal($zeitraum[$spt][1]+24*60*60-60);
        }
        
        $uid = $spt."".$datum[$spt]."@couchtipper_cal";
        
        if (!is_big_tournament(get_curr_wett())){
            $wett_name = get_wettbewerb_name(get_curr_wett());
            $wett_jahr = get_wettbewerb_jahr(get_curr_wett());
            
            $notes = $spt .". Spieltag der Bundesliga Saison $wett_jahr. Automatisch erstellt von couchtipper.de"; 
        } else {
            $wett_name = get_wettbewerb_name(get_curr_wett());
            $wett_jahr = get_wettbewerb_jahr(get_curr_wett());
            $notes = "Kalender zur $wett_name $wett_jahr - Automatisch erstellt von couchtipper.de";
        }
        
        $title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');
        $location = html_entity_decode($location, ENT_COMPAT, 'UTF-8');
        #$location = preg_replace('/([\,;])/','\\\$1', $location); 
        
        $curr_time = stamp_to_cal(time());
        
        create_event($uid, $location, $title, $notes, $starttime, $endtime, $curr_time);
    }
}

function create_additional_events($team){
    #echo "<br><br>";
    #echo getcwd();
    list($wett_id, $wett_id_part) = get_curr_wett();
    
    $lines = file("src/api/other_games.txt");
    for($i=0;$i < count($lines); $i++){
        
        $values = explode(", ", $lines[$i]);

        if ($values[0] != $wett_id){
            continue;
        }
        
        $datum      = $values[1];
        $uhrzeit    = $values[2];
        $gegner     = $values[3];
        $wett_name  = $values[4];
        $spt        = $values[5];
        $wo         = $values[6];
        $stadt      = $values[7];
        $stadion    = $values[8];
        
        $datum = strtotime($datum . $uhrzeit);

        
        $starttime = stamp_to_cal($datum);
        $endtime = stamp_to_cal($datum +90*60 + 15*60);

        $title = $gegner;
        $title .= " " . $wo;
        
        $location = "$stadt - $stadion";
        $notes = "$spt $wett_name - Automatisch erstellt von couchtipper.de";
    
        
        $uid = $spt."".$datum."@couchtipper_cal";
        
        $title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');
        $location = html_entity_decode($location, ENT_COMPAT, 'UTF-8');
        $curr_time = stamp_to_cal(time());

        create_event($uid, $location, $title, $notes, $starttime, $endtime, $curr_time);
        
    }
    

    



    #echo "<br><br>";
}


function create_event($uid, $location, $title, $notes, $starttime, $endtime, $curr_time){
    $out = "BEGIN:VEVENT\n";
    $out .= "UID:$uid\n";
    $out .= "LOCATION:$location\n";
    $out .= "SUMMARY:$title\n";
    $out .= "DESCRIPTION:$notes\n";
    $out .= "CLASS:PUBLIC\n";
    $out .= "DTSTART;TZID=Europe/Berlin:$starttime\n";
    $out .= "DTEND;TZID=Europe/Berlin:$endtime\n";
    $out .= "DTSTAMP;TZID=Europe/Berlin:$curr_time\n";
    $out .= "END:VEVENT\n"; 
    echo $out;
}


function create_cal($team){
    echo "BEGIN:VCALENDAR\n";
    echo "VERSION:2.0\n";
    echo "PRODID:couchtipper.de\n";
    echo "METHOD:REQUEST\n";
    
    $wett_jahr = get_wettbewerb_jahr(get_curr_wett());
    if (is_big_tournament(get_curr_wett())){
        $wett_name = get_wettbewerb_name(get_curr_wett());
        echo "NAME:Couchtipper $wett_name $wett_jahr\n";
        echo "X-WR-CALNAME:Couchtipper $wett_name $wett_jahr\n";
    } else {
        echo "NAME:Couchtipper Bundesliga Saison $wett_jahr\n";
        echo "X-WR-CALNAME:Couchtipper Bundesliga Saison $wett_jahr\n";
    }

    echo "BEGIN:VTIMEZONE\n";
    echo "TZID:Europe/Berlin\n";
    echo "X-LIC-LOCATION:Europe/Berlin\n";
    
    echo "BEGIN:DAYLIGHT\n";
    echo "TZOFFSETFROM:+0100\n";
    echo "TZOFFSETTO:+0200\n";
    echo "TZNAME:CEST\n";
    echo "DTSTART:19700329T020000\n";
    echo "RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=3\n";
    echo "END:DAYLIGHT\n";
    
    echo "BEGIN:STANDARD\n";
    echo "TZOFFSETFROM:+0200\n";    
    echo "TZOFFSETTO:+0100\n";
    echo "TZNAME:CET\n";
    echo "DTSTART:19701025T030000\n";
    echo "RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=10\n";
    echo "END:STANDARD\n";
    
    echo "END:VTIMEZONE\n";

    
    create_team_events($team);
    create_additional_events($team);
    
    echo "END:VCALENDAR";
}

$team = $_GET['team'];

if (isset($_GET['year'])){
    create_cal($team);
}


?>
