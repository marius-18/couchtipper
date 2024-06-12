<div class="container">

<?php
//require_once('src/functions/main.inc.php');
//require_once('src/functions/template.inc.php'); ??? BRAUCHT MAN?
require_once('src/include/code/input_template.inc.php');
require_once('src/include/code/get_games.inc.php'); // ist schon da!
require_once('src/include/code/print_games.php');
require_once('src/include/code/refresh.php');
require_once('src/include/lib/forms.inc.php');


if (!is_logged()){
    echo "<div class=\"alert alert-danger\"><span class=\"badge badge-pill badge-danger\">Fehler!</span> Dieser Bereich steht nur für eingeloggte User zur Verfügung!</div>";
    exit;
}


if ((!is_checked_in()) && (is_logged()) && (is_active_wettbewerb()) ) {  
    echo "<div class=\"alert alert-warning text-center\" style=\"margin-bottom:0\">
                <strong>Achtung!</strong> Du bist im aktuellen Wettbewerb noch nicht eingecheckt! 
                <br>
                Du kannst deswegen noch keine Tipps abgeben! 
                <br>
                Wenn du dies ändern möchtest, klicke <a href=\"?new_check_in=1\" class=\"alert-link\">hier</a>!
        </div>";
    exit;
}




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
?>



</div>
<br>
