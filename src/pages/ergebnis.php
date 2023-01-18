<br><div class = "container container-rounded">

<?php
//require_once('src/functions/template.inc.php');
//require_once('src/functions/tipp_template.inc.php');

require_once('src/include/code/input_template.inc.php');
require_once('src/include/code/get_games.inc.php'); // ist schon da!
require_once('src/include/code/print_games.php');
require_once('src/include/code/refresh.php');
require_once('src/include/lib/forms.inc.php');

if (!allow_erg()){
  echo "<div class=\"alert alert-danger\"> Dieser Bereich ist <strong>nur f&uuml;r Administratoren</strong>!<br>
  Frage beim Administrator nach, um Rechte zum &Auml;ndern von Ergebnissen zu bekommen.</div>";
  exit;

}

?> 


<?php

$error = false;
$error_msg = "";

$spieltag = spt_select();

if (isset($_POST['spieltag'])) {
    $spieltag = $_POST['spieltag'];
}


if (isset($_POST['change']) && ($_POST['change'] == "1")) {
  $change = true;
} else {
  $change = false;
}



// Die Ergebnisse werden geprüft und in die Datenbank geschrieben

list($error, $error_msg) = input ($spieltag, "Ergebnisse", false , 0);



// SelectFormular zur Eingabe des Spieltages

echo "<div class=\"alert alert-danger\"><strong>Achtung!</strong> Du gibst gerade <strong>Ergebnisse</strong> ein!</div>";

select_spieltag($spieltag); 


$args = get_games($spieltag, "Ergebnisse", $change, "");

// AB HIER NUR NOCH AUSGABE FÜR BROWSERVERSION (MOBIL/DESKTOP)

print_games($args, "Ergebnisse", $change);

if ($error){
    echo "<div class=\"alert alert-danger\"><strong>Fehler:</strong> $error_msg</div>";
} 
if (!$error && $error_msg != "") {
    echo "<div class=\"alert alert-success\">$error_msg</div>";
}


?>


</div>
<br>
