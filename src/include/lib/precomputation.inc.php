<?php 
## TODO: Also Precompute for all Goals and for BuLi

function precompute_all_tipps_to_db($modus){
    ## Berechnet das "other Tipps" Array und speichert es in die Datenbank.. 
    global $g_pdo;
    list($other_tipps_args, $punkte) = get_other_tipps(0, $modus);
    
    $max_string_length = 1024;

    $help = json_encode(array($other_tipps_args, $punkte), JSON_FORCE_OBJECT);
    $split_array = str_split($help, $max_string_length );
    
    ## Zuerst wird die DB geleert..
    $sql = "TRUNCATE `Precompute_Tipps`";
    $result = $g_pdo->query($sql);
    
    if (!$result) {
        ## TODO: Hier gab es einen Fehler.. was machen??
        return 0;
    }
    
    $input_error = false;
    ## Jetzt durch alle Elemente gehen und in die DB speichern
    foreach ($split_array as $key => $val){
        $stmt = $g_pdo->prepare("INSERT INTO `Precompute_Tipps`(`id`, `value`) VALUES (:key, :val)");
        $params = array('key' => $key, 'val' => $val);
        $stmt->execute($params);
        if (!$stmt){
            $input_error = true;
        }
    }
    
    if (!$input_error){
        echo "Precomputation erfolgreich abgeschlossen<br>";
    } else {
        echo "FEHLER bei Precomputation<br>";   
    }
}


function get_precompute_tipps(){
    global $g_pdo;
    
    $stmt = $g_pdo->prepare("SELECT `id`, `value` FROM `Precompute_Tipps` WHERE 1 ORDER BY id ASC");
    $stmt->execute();
    $string = "";
    foreach ($stmt as $entry) { 
        $string .= $entry['value'];
    }
    
    list($other_tipps_args, $punkte) = json_decode($string, JSON_FORCE_OBJECT);

    return (array($other_tipps_args, $punkte));    
}


?>
