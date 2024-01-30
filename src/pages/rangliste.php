<?php

##############################################################
######    Hinrunde / Rückrunde Tabelle!!
##############################################################

require_once('src/include/code/rangliste.inc.php');

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
        
        <button type=\"button\" class=\"btn btn-info\" onclick = \"rangliste_ausblenden(4)\" id=\"rangbutton4\">Bereich</button>
        <br><br>
        ";
        
        

    
        
    echo "<div class=\"\" id=\"rang1\" style=\"display: none;\">";
        print_rangliste(1,17,1);
    echo "</div>";

    echo "<div class=\"\" id=\"rang2\" style=\"display: block;\">";
        print_rangliste(18,akt_spieltag(),1);
    echo "</div>";

    echo "<div class=\"\" id=\"rang3\" style=\"display: none;\">";
        print_rangliste(1,akt_spieltag(),1);
    echo "</div>";

    echo "<div class=\"\" id=\"rang4\" style=\"display: none;\">";

    // Manuelle Spieltage

    echo "<div class=\"container\">";
    echo "<form action=\"\" method=\"POST\" name=\"rangliste_man_form\">";
    echo "<div class = \"row\">
            <div class = \"col\">
                <div class = \"form-group\">
                <label for=\"disable_rangliste_man_sel1\">Start:</label>
                <select class=\"form-control\" id=\"disable_rangliste_man_sel1\" name=\"rangliste_man_start\" onchange=\"disable_rangliste_man()\">
                    <option value=\"0\">Auswählen</option>";
                    for ($i=1;$i<=34;$i++){
                        if ($i == $rangliste_man_start){
                            $sel = "selected";
                        } else {
                            $sel = "";
                        }
                        echo "<option value=\"$i\" $sel>$i</option>";
    
                    }
                echo "</select>
                </div>
            </div>
        
        <div class=\"col\">
            <div class=\"form-group\">
                <label for=\"disable_rangliste_man_sel2\">Ende:</label>
                <select class=\"form-control\" id=\"disable_rangliste_man_sel2\" name=\"rangliste_man_ende\" onchange=\"this.form.submit()\">
                    <option value=\"0\">Auswählen</option>";
                    for ($i=1;$i<=34;$i++){
                        if ($i == $rangliste_man_ende){
                            $sel = "selected";
                        } else {
                            $sel = "";
                        }
                        echo "<option value=\"$i\" id=\"spt_ende$i\" class=\"spt_ende\" $sel>$i</option>";
    
                    }
  
                echo "</select>
                </div>
            </div>
        </div>
    </form>
    </div>";
    
    if (($rangliste_man_start > 0) && ($rangliste_man_ende > 0)){
        echo "<script>rangliste_ausblenden(4);</script>";
        print_rangliste($rangliste_man_start,$rangliste_man_ende,1);
    }
    
    
    echo "</div>";
} else {
    print_rangliste(1,akt_spieltag(),1);
}








?>





<script>
    function disable_rangliste_man(rel=0){
        var start_value = Number(document.getElementById("disable_rangliste_man_sel1").value);
        
        var elem_iter = document.getElementsByClassName('spt_ende');
        for (var i = 0; i < elem_iter.length; ++i) {
            elem_iter[i].disabled = false;
        }
    
        for (i = 1; i < start_value; i++){
            var name = "spt_ende" + i;
            document.getElementById(name).disabled = true;
        }
        
        // Wenn des andere Select schon einen passenden Wert hat => Reload
        var end_value = Number(document.getElementById("disable_rangliste_man_sel2").value);

        if ((end_value > 0) && (rel == 0) && (end_value >= start_value) ){
            console.log("zuiu");
            document.rangliste_man_form.submit();
        }

    }
    
    // Parameter 1, damit wir nicht direkt wieder reloaden
    disable_rangliste_man(1);
</script>
