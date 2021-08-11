<?php

require_once('src/functions/main.inc.php');
require_once('src/functions/tabelle.template.php');
require_once('src/print/print_tabelle.php');

echo "<button type=\"button\" class=\"btn btn-info focus\" onclick = \"rank_ausblenden(500)\" id=\"510\">Gesamt</button>

<button type=\"button\" class=\"btn btn-info \" onclick = \"rank_ausblenden(501)\" id=\"511\">Heim</button>

<button type=\"button\" class=\"btn btn-info\" onclick = \"rank_ausblenden(502)\" id=\"512\">Ausw&auml;rts</button>
<br><br>
<button type=\"button\" class=\"btn btn-info\" onclick = \"rank_ausblenden(503)\" id=\"513\">Tendenz</button>

<button type=\"button\" class=\"btn btn-info\" onclick = \"rank_ausblenden(504)\" id=\"514\">Hinrunde</button>

<button type=\"button\" class=\"btn btn-info\" onclick = \"rank_ausblenden(505)\" id=\"515\">R&uuml;ckrunde</button>
<br><br>
";



?>
      <?php print_tabelle(tabelle("", 0), 500,""); ?>
      <?php print_tabelle(tabelle("Heim", 0), 501, "none"); ?>
      <?php print_tabelle(tabelle("Auswaerts", 0), 502, "none"); ?>
      <?php print_tabelle(tabelle("Tendenz", akt_spieltag()-5), 503, "none"); ?>
      <?php print_tabelle(tabelle("Hinrunde", 0), 504, "none"); ?>
      <?php print_tabelle(tabelle("Rückrunde", 17), 505, "none"); ?>


