<?php
### Hier kann man noch etwas mehr einfügen... nochmal durch alles andere durchgehen und suchen
function get_team_name($team){
    global $g_pdo;
    
    $sql = "SELECT `team_name` FROM `Teams` WHERE `team_nr` = $team";
    foreach ($g_pdo->query($sql) as $row) {
        $name = $row['team_name'];
    }
    return $name;
}

function get_team_city($team){
    global $g_pdo;
    
    $sql = "SELECT `city` FROM `Teams` WHERE `team_nr` = $team";
    foreach ($g_pdo->query($sql) as $row) {
        $city = $row['city'];
    }
    return $city;
}

function get_team_stadium($team){
    global $g_pdo;
    
    $sql = "SELECT `stadium` FROM `Teams` WHERE `team_nr` = $team";
    foreach ($g_pdo->query($sql) as $row) {
        $stadium = $row['stadium'];
    }
    return $stadium;
}

function get_team_open_db_name($team){
    global $g_pdo;
    
    $sql = "SELECT `open_db_name` FROM `Teams` WHERE `team_nr` = $team";
    foreach ($g_pdo->query($sql) as $row) {
        $open_db_name = $row['open_db_name'];
    }
    return $open_db_name;
}



function get_img_details(){
    ## Returns Image Path and image suffix for corresponding competition
    if (!is_big_tournament(get_curr_wett())) {
        $img_folder = "Vereine"; 
    } elseif (get_wettbewerb_code(get_curr_wett()) == "EM")  {
        $img_folder = "Nations/EM";   
    } else {
        $img_folder = "Nations/WM";           
    }
    
    if (get_wettbewerb_code(get_curr_wett()) == "BuLi"){
        $endung = "gif";
    } else {
        $endung = "png";
    }
    
    return array($img_folder, $endung);
}
?>
