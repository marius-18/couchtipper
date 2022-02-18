<?php
include_once("src/include/code/gewinn.inc.php");
?>



<div class="container">
<div class="alert alert-danger">
<strong>Achtung:</strong> der Gewinn hängt davon ab, wie viele User mittippen. Damit kann sich die Verteilung noch ändern.</div>
Der Gewinn für Platz x berechnet sich mittels: <br><br> 
<img src="images/gewinn_neu21.png" width = "300px"><br><br>



<?php


list($id,$id_part) = get_curr_wett();

    if ((get_wettbewerb_code(get_curr_wett()) == "Buli") && ($id_part == 1)) {
    // Wenn wir im Buli Modus und in der Rückrunde sind, müssen buttons für Hinrunde/Gesamt angezeigt werden.
    
    echo "
        <button type=\"button\" class=\"btn btn-info\" onclick = \"gewinn_ausblenden(1)\" id=\"gewinn_button1\">Hinrunde</button>

        <button type=\"button\" class=\"btn btn-info focus\" onclick = \"gewinn_ausblenden(2)\" id=\"gewinn_button2\">R&uuml;ckrunde</button>

        <!--<button type=\"button\" class=\"btn btn-info\" onclick = \"gewinn_ausblenden(3)\" id=\"gewinn_button3\">Gesamt</button>-->
        <br><br>";
        
        
    echo "<div class=\"container\" id=\"gewinn1\" style=\"display: none;\">";     
        print_gewinn(get_hinrunde(get_curr_wett()));
   
        echo "<br><br>Der Aktuelle Stand ist: ";

        print_gewinner(get_hinrunde(get_curr_wett()));

        print_gesamt_gewinner(get_hinrunde(get_curr_wett()));  
    
    echo "</div>";

    echo "<div class=\"container\" id=\"gewinn2\" style=\"display: block;\">";     
        print_gewinn(get_curr_wett());
   
        echo "<br><br>Der Aktuelle Stand ist: ";

        print_gewinner(get_curr_wett());

        print_gesamt_gewinner(get_curr_wett());  
    
    echo "</div>";
    } else {
    
        print_gewinn(get_curr_wett());
   
        echo "<br><br>Der Aktuelle Stand ist: ";

        print_gewinner(get_curr_wett());

        print_gesamt_gewinner(get_curr_wett());

    }
    
?>
</div>
<br>
