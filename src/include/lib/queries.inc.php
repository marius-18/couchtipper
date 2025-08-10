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

function get_all_teams(){
    ## Returns an array of all teams that are stored in DB
    global $g_pdo;
    
    $sql = "SELECT `team_nr`, `team_name`, `open_db_name`, `city`, `stadium` FROM `Teams` 
            WHERE 1 
            ORDER BY team_nr ASC";
    
    foreach ($g_pdo->query($sql) as $row) {
        $team_nr                = $row['team_nr'];
        $team_name[$team_nr]    = $row['team_name'];
        $open_db_name[$team_nr] = $row['open_db_name'];
        $city[$team_nr]         = $row['city'];
        $stadium[$team_nr]      = $row['stadium'];
    }
    
    return array($team_name, $open_db_name, $city, $stadium);
}

function add_team($team_name, $open_db_name, $city, $stadium){
    ## Adds a team into DB
    global $g_pdo;
    
    $stmt = $g_pdo->prepare( "INSERT INTO `Teams` (`team_name`, `open_db_name`, `city`, `stadium`) 
            VALUES (:team_name, :opendb, :city, :stadium);");
    
    $params = array('team_name' => $team_name,  'opendb' => $open_db_name, 'city' => $city, 'stadium' => $stadium);
    
    return $stmt->execute($params);
    
}
?>
