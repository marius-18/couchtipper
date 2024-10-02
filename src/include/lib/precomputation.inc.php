<?php 
## TODO: Also Precompute for all Goals and for BuLi

function precompute_all_tipps_to_db($spieltag, $modus){
    ## Berechnet das "other Tipps" Array und speichert es in die Datenbank.. 
    ## Bei EM/WM haben wir spieltag == 0, um alle spiele der Gruppenphase zu bekommen
    ## Ansonsten halt nur der entsprechende Spieltag
    global $g_pdo;
    list($other_tipps_args, $punkte) = get_other_tipps($spieltag, $modus);
    
    $max_string_length = 1024;

    $help = json_encode(array($other_tipps_args, $punkte), JSON_FORCE_OBJECT);
    $split_array = str_split($help, $max_string_length );
    
    if ($spieltag == 0) {
        ## Zuerst wird die DB geleert..
        $sql = "TRUNCATE `Precompute_Tipps`";
        $result = $g_pdo->query($sql);
    } else {
        ## Entsprechenden Spieltag leeren
        $sql = "DELETE FROM `Precompute_Tipps` WHERE spieltag = $spieltag";
        $result = $g_pdo->query($sql);
    }
    
    
    if (!$result) {
        ## TODO: Hier gab es einen Fehler.. was machen??
        return 0;
    }
    
    $input_error = false;
    ## Jetzt durch alle Elemente gehen und in die DB speichern
    foreach ($split_array as $key => $val){
        if ($spieltag == 0){
            $stmt = $g_pdo->prepare("INSERT INTO `Precompute_Tipps`(`id`, `value`) VALUES (:key, :val)");
            $params = array('key' => $key, 'val' => $val);
        } else {
            $stmt = $g_pdo->prepare("INSERT INTO `Precompute_Tipps`(`id`, `spieltag`, `value`) VALUES (:key, :spieltag, :val)");
            $params = array('key' => $key, 'spieltag' => $spieltag, 'val' => $val);
        }
        $stmt->execute($params);
        if (!$stmt){
            $input_error = true;
        }
    }
    
    if (!$input_error){
        echo "Precomputation Tipps erfolgreich abgeschlossen<br>";
    } else {
        echo "FEHLER bei Precomputation Tipps<br>";   
    }
}


function get_precompute_tipps($spieltag = Null){
    global $g_pdo;
    if ($spieltag != Null){
        $stmt = $g_pdo->prepare("SELECT `id`, `value` FROM `Precompute_Tipps` WHERE spieltag = $spieltag ORDER BY id ASC");
    } else {
        $stmt = $g_pdo->prepare("SELECT `id`, `value` FROM `Precompute_Tipps` WHERE 1 ORDER BY id ASC");
    }
    
    try {
        ## Testen ob die DB Abfrage überhaupt durch geht.. 
        ## Falls Precomputation nicht vorhanden
        $stmt->execute();
    } catch (Exception $e){
        return 0;
    }
    
    $string = "";
    foreach ($stmt as $entry) { 
        $string .= $entry['value'];
    }
    
    list($other_tipps_args, $punkte) = json_decode($string, JSON_FORCE_OBJECT);

    return (array($other_tipps_args, $punkte));    
}



function precompute_all_tore_to_db($spieltag, $max_spieltag){
    ## Berechnet das "other Tipps" Array und speichert es in die Datenbank..
    global $g_pdo;
    
    if ($spieltag == NULL){
        $tore = array();
        for ($i = 1; $i <= $max_spieltag; $i++){
            $tore[$i] = get_tore($i, "rtrtr");
        }
    } else {
        $tore = get_tore($spieltag, "rtrtr");
    }

    $max_string_length = 1024;

    $help = json_encode($tore, JSON_FORCE_OBJECT);
    $split_array = str_split($help, $max_string_length );

    if ($spieltag == NULL){
        ## Zuerst wird die DB geleert..
        $sql = "TRUNCATE `Precompute_Tore`";
        $result = $g_pdo->query($sql);
    } else {
        ## Entsprechenden Spieltag leeren
        $sql = "DELETE FROM `Precompute_Tore` WHERE spieltag = $spieltag";
        $result = $g_pdo->query($sql);
    }

    if (!$result) {
        ## TODO: Hier gab es einen Fehler.. was machen??
        return 0;
    }

    $input_error = false;
    ## Jetzt durch alle Elemente gehen und in die DB speichern
    foreach ($split_array as $key => $val){
        if ($spieltag == NULL){
            $stmt = $g_pdo->prepare("INSERT INTO `Precompute_Tore`(`id`, `value`) VALUES (:key, :val)");
            $params = array('key' => $key, 'val' => $val);
        } else{
            $stmt = $g_pdo->prepare("INSERT INTO `Precompute_Tore`(`id`, `spieltag`, `value`) VALUES (:key, :spieltag, :val)");
            $params = array('key' => $key, 'spieltag' => $spieltag, 'val' => $val);
        }
        $stmt->execute($params);
        if (!$stmt){
            $input_error = true;
        }
    }

    if (!$input_error){
        echo "Precomputation Tore erfolgreich abgeschlossen<br>";
    } else {
        echo "FEHLER bei Precomputation Tore<br>";
    }
}

function get_precompute_tore($spieltag = NULL){
    global $g_pdo;
    if ($spieltag != Null){
        $stmt = $g_pdo->prepare("SELECT `id`, `value` FROM `Precompute_Tore` WHERE spieltag = $spieltag ORDER BY id ASC");
    } else {
        $stmt = $g_pdo->prepare("SELECT `id`, `value` FROM `Precompute_Tore` WHERE 1 ORDER BY id ASC");
    }
    
    try {
        ## Testen ob die DB Abfrage überhaupt durch geht.. 
        ## Falls Precomputation nicht vorhanden
        $stmt->execute();
    } catch (Exception $e){
        return 0;
    }
    
    $string = "";
    foreach ($stmt as $entry) {
        $string .= $entry['value'];
    }

    $tore = json_decode($string, JSON_FORCE_OBJECT);
    
    if (!isset($tore)){
       $tore = array();
    }
    
    return $tore;
}




?>
