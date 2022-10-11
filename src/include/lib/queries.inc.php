<?php

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
?>
