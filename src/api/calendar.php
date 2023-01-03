<?php
//This is the most important coding.
header("Content-Type: text/Calendar");
header("Content-Disposition: inline; filename=couchtipper-Saisonkalender.ics");

require_once("src/include/code/programm.inc.php");

function create_team_events($team){
    
    list($spieltag, $team_nr1, $team_nr2, $team_name, $datum, $zeitraum, $tore1, $tore2) = programm($team,1,34);

    foreach ($team_nr1 as $spt => $team1) {

        
        if ($team1 == $team){
            $title = get_team_open_db_name($team_nr2[$spt]);#$team_name[$spt];
            $title .= " Heimspiel";
            $location = get_team_city($team)." - ".get_team_stadium($team);
        } else {
            $title = get_team_open_db_name($team_nr1[$spt]);#$team_name[$spt];
            $title .= " Auswärtsspiel";
            $location = get_team_city($team_nr1[$spt])." - ".get_team_stadium($team_nr1[$spt]);
        }
        
        if ($datum[$spt] != 0){
            $starttime = stamp_to_cal($datum[$spt]);
            $endtime = stamp_to_cal($datum[$spt] +90*60 + 15*60);
        } else {
            $starttime = stamp_to_cal($zeitraum[$spt][0]);
            $endtime  = stamp_to_cal($zeitraum[$spt][1]+24*60*60-60);
        }
        
        $uid = $spt."".$datum[$spt]."@couchtipper_cal";
        $notes = $spt .". Spieltag Bundesliga Saison 2022/23. Automatisch erstellt von couchtipper.de"; 
    
    
        $title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');
        $location = html_entity_decode($location, ENT_COMPAT, 'UTF-8');
        #$location = preg_replace('/([\,;])/','\\\$1', $location); 
    
        $curr_time = stamp_to_cal(time());
        
        create_event($uid, $location, $title, $notes, $starttime, $endtime, $curr_time);

    }
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
    
    
    echo "END:VCALENDAR";
}

$team = $_GET['team'];
create_cal($team);

?>
