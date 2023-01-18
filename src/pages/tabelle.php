<?php
require_once('src/include/code/tabelle.inc.php');
require_once('src/include/code/get_games.inc.php');
?>

<script>
function rank_ausblenden(wert) {
a = Math.floor(wert/100);

    for (i=a*100; i<a*100+10; i++){
        if (i == wert){
            document.getElementById(wert).style.display = "";
            document.getElementById(wert+10).className = "btn btn-info focus";
        } else{
            document.getElementById(i).style.display = "none";
            document.getElementById(i+10).className = "btn btn-info";
        }
    }
}
</script>

<?php
echo "
<button type=\"button\" class=\"btn btn-info focus\" onclick = \"rank_ausblenden(500)\" id=\"510\">Gesamt</button>
<button type=\"button\" class=\"btn btn-info \" onclick = \"rank_ausblenden(501)\" id=\"511\">Heim</button>
<button type=\"button\" class=\"btn btn-info\" onclick = \"rank_ausblenden(502)\" id=\"512\">Ausw&auml;rts</button>
<br><br>
<button type=\"button\" class=\"btn btn-info\" onclick = \"rank_ausblenden(503)\" id=\"513\">Tendenz</button>
";

if ((get_wettbewerb_code(get_curr_wett()) == "BuLi") && (get_curr_wett()[1] == 1)) {
echo "
<button type=\"button\" class=\"btn btn-info\" onclick = \"rank_ausblenden(504)\" id=\"514\">Hinrunde</button>

<button type=\"button\" class=\"btn btn-info\" onclick = \"rank_ausblenden(505)\" id=\"515\">R&uuml;ckrunde</button>
";
}

echo "<br><br>";

?>
      <?php print_tabelle(tabelle("", 0), 500,""); ?>
      <?php print_tabelle(tabelle("Heim", 0), 501, "none"); ?>
      <?php print_tabelle(tabelle("Auswaerts", 0), 502, "none"); ?>
      <?php print_tabelle(tabelle("Tendenz", akt_spieltag()-5), 503, "none"); ?>
      <?php print_tabelle(tabelle("Hinrunde", 0), 504, "none"); ?>
      <?php print_tabelle(tabelle("Rückrunde", 17), 505, "none"); ?>



