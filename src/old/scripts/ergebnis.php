
<?php
require_once('src/functions/main.inc.php');
require_once('src/functions/template.inc.php');
require_once('src/functions/input_template.inc.php');
require_once('src/functions/tipp_template.inc.php');
require_once('src/functions/get_games.inc.php');
require_once('src/print/print_games.php');
require_once('src/functions/refresh.php');


if (!allow_erg()){
  echo "Dieser Bereich ist nur f&uuml;r Administratoren!<br>
  Frage beim Administrator nach, um Rechte zum &Auml;ndern von Ergebnissen zu bekommen.";
  exit;

}

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



// Die Ergebnisse werden geprüft und in die Datenbank geschrieben

list($error, $error_msg) = input ($spieltag, "Ergebnisse", false , 0);




// SelectFormular zur Eingabe des Spieltages

select_spieltag($spieltag); 

echo "<br><div class = \"content\">";


$args = get_games($spieltag, "Ergebnisse", $change);

// AB HIER NUR NOCH AUSGABE FÜR BROWSERVERSION (MOBIL/DESKTOP)

print_games($args, "Ergebnisse", $change);

echo $error_msg;


?>


</div>
<br>
