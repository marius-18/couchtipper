<?php
//require_once('src/functions/main.inc.php');
//require_once('src/functions/template.inc.php'); ??? BRAUCHT MAN?
require_once('src/include/code/input_template.inc.php');
require_once('src/include/code/get_games.inc.php'); // ist schon da!
require_once('src/include/code/print_games.php');
require_once('src/include/code/refresh.php');
require_once('src/include/lib/forms.inc.php');


/*    // vielleicht nur wer angemeldet ? 	was ist mit anderen tipps ?

    WER NICHT ANGEMELDET IST KOMMT NICHT REIN
ERROR

vllt erst dann übersicht ??
achtung zeitsperre
*/



echo "<div class=\"container\">";
$error = false;
$error_msg = "";

$spieltag = spt_select();
if (isset($_POST['spieltag'])){
    $spieltag = $_POST['spieltag'];
}


$change = false;

if (isset($_POST['change']) && ($_POST['change'] == "1")) {
    $change = true;
}

// EINGABE IN DIE DATENBANK
// input() prüft und gibt alle Ergebnisse/Tipps ein
// Die Übergabe der Variablen geht direkt über POST

list($error, $error_msg) = input($spieltag, "Tipps", false, 0);

// SelectFormular zur Eingabe des Spieltages

select_spieltag($spieltag); 

$args = get_games($spieltag, "Tipps", $change, get_usernr()); // HIEEEER MUSSS EVENTUELL NOCH USER REIN // VLLT AUCH HIER SWITCH MIT ANDEREN TIPPS ???

print_games($args, "Tipps", $change);


echo $error_msg;

echo "</div>";
echo "<br>";




?>
