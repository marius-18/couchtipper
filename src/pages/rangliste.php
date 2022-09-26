<?php

##############################################################
######    Hinrunde / Rückrunde Tabelle!!
##############################################################

require_once('src/include/code/rangliste.inc.php');


list($id,$id_part) = get_curr_wett();

if ((get_wettbewerb_code(get_curr_wett()) == "BuLi") && ($id_part == 1)) {
    // Wenn wir im Buli Modus und in der Rückrunde sind, müssen buttons für Hinrunde/Gesamt angezeigt werden.
    
    echo "
        <button type=\"button\" class=\"btn btn-info\" onclick = \"rangliste_ausblenden(1)\" id=\"rangbutton1\">Hinrunde</button>

        <button type=\"button\" class=\"btn btn-info focus\" onclick = \"rangliste_ausblenden(2)\" id=\"rangbutton2\">R&uuml;ckrunde</button>

        <button type=\"button\" class=\"btn btn-info\" onclick = \"rangliste_ausblenden(3)\" id=\"rangbutton3\">Gesamt</button>
        <br><br>";
        
        
    echo "<div class=\"container\" id=\"rang1\" style=\"display: none;\">";
        print_rangliste(1,17,1);
    echo "</div>";

    echo "<div class=\"container\" id=\"rang2\" style=\"display: block;\">";
        print_rangliste(18,akt_spieltag(),1);
    echo "</div>";

    echo "<div class=\"container\" id=\"rang3\" style=\"display: none;\">";
        print_rangliste(1,akt_spieltag(),1);
    echo "</div>";

} else {
    print_rangliste(1,akt_spieltag(),1);
}


?>

