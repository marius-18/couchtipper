<?php
//require_once('src/functions/main.inc.php');
//require_once('src/functions/template.inc.php');
//require_once('src/functions/tipp_template.inc.php');
//require_once('src/functions/get_games.inc.php');
//require_once('src/print/print_games.php');

require_once('src/include/code/input_template.inc.php');
require_once('src/include/code/get_games.inc.php'); // ist schon da!
require_once('src/include/code/print_games.php');
require_once('src/include/code/refresh.php');
require_once('src/include/lib/forms.inc.php');
?> 


<?php
$error = false;
$error_msg = "";


$spieltag = spt_select();

if (isset($_POST['spieltag'])){
   $spieltag = $_POST['spieltag'];
}

// SelectFormular zur Eingabe des Spieltages

select_spieltag($spieltag); 

echo "<br><div class = \"container\">";

$args = get_games($spieltag, "Spieltag", 1, "");


print_games($args, "Spieltag", 1);

echo $error_msg;

echo "
</div>
";



?>
