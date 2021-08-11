<?php
require_once('src/functions/main.inc.php');
require_once('src/functions/template.inc.php');
require_once('src/functions/tipp_template.inc.php');
require_once('src/functions/input_template.inc.php');
require_once('src/functions/get_games.inc.php');
require_once('src/print/print_games.php');
require_once('src/functions/refresh.php');

/*    // vielleicht nur wer angemeldet ? 	was ist mit anderen tipps ?

    WER NICHT ANGEMELDET IST KOMMT NICHT REIN
ERROR

vllt erst dann übersicht ??
achtung zeitsperre
*/

?>


<?php

$error = false;
$error_msg = "";

$spieltag = $_POST['spieltag'];

if ($spieltag == ""){ 
   $spieltag = spt_select();
}


if ($_POST['change'] == "1") {
  $change = true;
} else {
  $change = false;
}


// EINGABE IN DIE DATENBANK
// input() prüft und gibt alle Ergebnisse/Tipps ein
// Die Übergabe der Variablen geht direkt über POST

list($error, $error_msg) = input($spieltag, "Tipps", false);

// SelectFormular zur Eingabe des Spieltages

select_spieltag($spieltag); 


echo "<br><div class = \"content\">";

$args = get_games($spieltag, "Tipps", $change, get_usernr()); // HIEEEER MUSSS EVENTUELL NOCH USER REIN // VLLT AUCH HIER SWITCH MIT ANDEREN TIPPS ???

print_games($args, "Tipps", $change);

echo $error_msg;

echo "</div>";
echo "<br>";




?>
