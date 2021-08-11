<?php
require_once('src/functions/main.inc.php');
require_once('src/functions/template.inc.php');
require_once('src/functions/tipp_template.inc.php');
require_once('src/functions/get_games.inc.php');
require_once('src/print/print_games.php');


?> 


<?php

$error = false;
$error_msg = "";

$spieltag = $_POST['spieltag'];

if ($spieltag == ""){ 
   $spieltag = spt_select();
}

// SelectFormular zur Eingabe des Spieltages

select_spieltag($spieltag); 

echo "<br><div class = \"content\">";


$args = get_games($spieltag, "Spieltag", 1);


print_games($args, "Spieltag", 1);

echo $error_msg;

echo "
</div>
<br>";



?>
