<?php
if (!allow_date()){
    echo "<div class=\"container-fluid\">
            <div class=\"alert alert-danger\">
                <span class=\"h5\"><span class=\"badge badge-pill badge-danger\"><strong>Verbotener Zugriff!</strong></span></span><br><br>
                <strong>Dieser Bereich ist nur f&uuml;r Administratoren!</strong><br>
                Frage beim Administrator nach, um Rechte zum &Auml;ndern von Ergebnissen zu bekommen.
            </div>
          </div>";
       return;
}
?>

<div class="container-fluid">


<?php

require_once('src/include/lib/forms.inc.php');
require_once('src/include/code/spiele_terminieren.inc.php');
require_once("src/include/code/get_games.inc.php");

## Spieltag Auswählen
$spieltag = spt_select();
if (isset($_POST['spieltag'])){
   $spieltag = $_POST['spieltag'];
}

select_spieltag($spieltag);


## Wichtig für Hin und Rückrunde..
## "Team$modus[0]" steht für das Heim Team, Auswärts bei Index 1
## "Datum$modus[0]" Ist das Datum, um das es hier geht
$modus = ["1","2"];
$real_spieltag = $spieltag;

if (($spieltag > 17) && (get_wettbewerb_code(get_curr_wett()) == "BuLi") ){
    ## In der Rückrunde wird die Reihenfolge der Teams und die Datum ID angepasst
    $modus = ["2","1"];
    $spieltag = $spieltag - 17;
}



$show_formular = true;
$error = false;
$error_msg = "";

## Update Ergebnisse in der Datenbank!
## (falls welche übergeben wurden..)
list($input_count, $error_count) = input_game_kickofftime($spieltag, $modus);

## Wenn Button gedrückt: Automatisches Eintragen aus OpenligaDB
if (isset($_POST["automated_input"])){
    ## Der spieltag soll automatisch geupdatet werden!
    $automated_log = "";
    list ($error, $automated_log) = set_spieltag_date($real_spieltag);
    if ($error){
        $error_msg = $automated_log;
    }
}

## Datum des Spieltags aus DB holen
$sql = "SELECT datum FROM Datum WHERE spieltag = $real_spieltag";
foreach ($g_pdo->query($sql) as $row) {
    $main_datum = $row['datum'];
}


## Fehler ausgeben, wenn Spieltag noch nicht terminiert ist
if (!isset($main_datum) || ($main_datum == "")) {
    $show_formular = false;
    $error = true;
    $error_msg = "Es muss erst ein Termin f&uuml;r den Spieltag ausgew&auml;hlt werden!";
}


## Gebe das eigentliche Formular aus
if ($show_formular){
    
    echo "<form action=\"\" method=\"post\">";
        print_game_table($main_datum, $spieltag, $modus);
        echo "<br>";
        echo "<input type=\"hidden\" name =\"spieltag\" value=\"$real_spieltag\" visible=\"false\">";
        echo "<input class=\"btn btn-success\" type=\"Submit\" value=\"Enter\">";
    echo "</form>";
    
    echo "<br>";
    
    ##  Button für Automatisches Eintragen
    echo "<form action=\"\" method=\"post\">";
        echo "<input type=\"hidden\" name =\"spieltag\" value=\"$real_spieltag\" visible=\"false\">";
        echo "<button type=\"submit\" name=\"automated_input\" value=\"1\" class=\"btn btn-info\" role=\"button\">Automatisch Terminieren</button>";
    echo "</form>";
    
    echo "<br>";
}


## Fehler oder Erfolgsnachricht anzeigen!
if (($error) || ($error_count > 0)) {
    if ($error_msg == ""){
        $error_msg = "Beim Speichern gab es $error_count Fehler.";
    }
    
    echo "<div class=\"alert alert-danger\">
            <span class=\"h5\"><span class=\"badge badge-pill badge-danger\"><strong>Fehler!</strong></span></span>
            $error_msg
          </div>";
} else {
    if ($input_count != 0){
        echo "<div class=\"alert alert-success\">
                <span class=\"h5\"><span class=\"badge badge-pill badge-success\"><strong>Erfolg!</strong></span></span>
                Es wurden <strong>$input_count Spiele</strong> geupdatet. Dabei gab es <strong>$error_count Fehler</strong>.
              </div>";
    }
    if (isset($automated_log)){
        echo "<div class=\"alert alert-success\">
                <span class=\"h5\"><span class=\"badge badge-pill badge-success\"><strong>Erfolg!</strong></span></span>
                Eine Autmatische Eintragung wurde vorgenommen. So sieht das Log aus:<br>
                $automated_log
              </div>";        
    }
}

?>


</div>
