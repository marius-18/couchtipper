<?php

##############################################################
######    Hinrunde / Rückrunde Tabelle!!
##############################################################

require_once('src/include/code/rangliste.inc.php');
require_once('src/include/lib/charts.inc.php');

// TODO: Vielleicht mal schauen, ob wir die nicht direkt sichtbaren Tabellen per JS nachladen könnnen.. 
// TODO: Für schnelleres Laden beim start..


list($id,$id_part) = get_curr_wett();

// Übergabe für Manuelle Tabelle
if (isset($_POST["rangliste_man_start"])){
    $rangliste_man_start = $_POST["rangliste_man_start"];
} else {
    $rangliste_man_start = 0;   
}

if (isset($_POST["rangliste_man_ende"])){
    $rangliste_man_ende =  $_POST["rangliste_man_ende"];
} else {
    $rangliste_man_ende = 0;   
}

if ((get_wettbewerb_code(get_curr_wett()) == "BuLi") && ($id_part == 1)) {
    // Wenn wir im Buli Modus und in der Rückrunde sind, müssen buttons für Hinrunde/Gesamt angezeigt werden.
    
    echo "
        <button type=\"button\" class=\"btn btn-info\" onclick = \"rangliste_ausblenden(1)\" id=\"rangbutton1\">Hinrunde</button>

        <button type=\"button\" class=\"btn btn-info focus\" onclick = \"rangliste_ausblenden(2)\" id=\"rangbutton2\">R&uuml;ckrunde</button>

        <button type=\"button\" class=\"btn btn-info\" onclick = \"rangliste_ausblenden(3)\" id=\"rangbutton3\">Gesamt</button>
        <br><br>
        
        <button type=\"button\" class=\"btn btn-info\" onclick = \"rangliste_ausblenden(5)\" id=\"rangbutton5\">Verlauf</button>
        
        <button type=\"button\" class=\"btn btn-info\" onclick = \"rangliste_ausblenden(4)\" id=\"rangbutton4\">Bereich</button>
        <br><br>
        ";
        
        

    
        
    echo "<div class=\"\" id=\"rang1\" style=\"display: none;\">";
        print_rangliste(1,17,1, true);
    echo "</div>";

    echo "<div class=\"\" id=\"rang2\" style=\"display: block;\">";
        print_rangliste(18,akt_spieltag(),1, true);
    echo "</div>";

    echo "<div class=\"\" id=\"rang3\" style=\"display: none;\">";
        print_rangliste(1,akt_spieltag(),1);
    echo "</div>";

    echo "<div class=\"\" id=\"rang4\" style=\"display: none;\">";

    ## Zeige Formular für Spieltags Bereich an
    list($rangliste_man_start, $rangliste_man_ende, $ret) = spieltag_start_ende();
    echo $ret;
    
    if (($rangliste_man_start > 0) && ($rangliste_man_ende > 0)){
        echo "<script>rangliste_ausblenden(4);</script>";
        print_rangliste($rangliste_man_start,$rangliste_man_ende,1);
    }
    
    echo "</div>";
    
    
    echo "<div class=\"\" id=\"rang5\" style=\"display: none;\">";

    ## Zeige Formular für Verlauf (Hin- & Rückrunde) an 
    list($verlauf_mode, $ret) = verlauf_hin_rueck();
    echo $ret;
    
    if (($verlauf_mode > 0) ){
        echo "<script>rangliste_ausblenden(5);</script>";
            
        if ($verlauf_mode == 1){
            $args = get_table_verlauf_data("Hinrunde");
        } else {
            $args = get_table_verlauf_data("Rückrunde");        
        }
        
        print_tabelle_verlauf_chart($args, "randomnameofchart", "block");
    }
    
    echo "</div>";
    
    
} else {
    print_rangliste(1,akt_spieltag(),1, true);
}








?>
