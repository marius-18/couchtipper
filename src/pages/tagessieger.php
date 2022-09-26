
<script >

function totalCurrencySort(a, b, rowA, rowB) {  
    // sortiert Euro Währungen (löscht die letzten beiden Zeichen und sortiert dann die Zahlen) 
    a = parseFloat(a.slice(0, -2)); // remove $
    b = parseFloat(b.slice(0, -2));
    if (a > b) return 1;
    if (a < b) return -1;
    return 0;
}
</script>

<div class="container">
<?php
if (!is_logged()){
    echo "<div class=\"alert alert-danger\"><span class=\"badge badge-pill badge-danger\">Fehler!</span> Dieser Bereich steht nur für eingeloggte User zur Verfügung!</div>";
    exit;
}

?>


<?php
include_once("src/include/code/gewinn.inc.php");

///////////////////////////////////////////////////////////////////////////////////////
/////// Übersicht über alle Tagessieger
///////////////////////////////////////////////////////////////////////////////////////

list($id,$id_part) = get_curr_wett();

if ((get_wettbewerb_code(get_curr_wett()) == "BuLi") && ($id_part == 1)) {
    // Wenn wir im Buli Modus und in der Rückrunde sind, müssen buttons für Hinrunde/Gesamt angezeigt werden.
    
    echo "
        <button type=\"button\" class=\"btn btn-info\" onclick = \"tagessieger_ausblenden(1)\" id=\"tagessieger_button1\">Hinrunde</button>

        <button type=\"button\" class=\"btn btn-info focus\" onclick = \"tagessieger_ausblenden(2)\" id=\"tagessieger_button2\">R&uuml;ckrunde</button>

        <!--<button type=\"button\" class=\"btn btn-info\" onclick = \"tagessieger_ausblenden(3)\" id=\"tagessieger_button3\">Gesamt</button>-->
        <br><br>";
        
    echo "<div class=\"container\" id=\"tagessieger1\" style=\"display: none;\">";        
        print_tagessieger_liste(get_hinrunde(get_curr_wett()));
        print_tagessieger_geld(get_hinrunde(get_curr_wett()));
    echo "</div>";

    echo "<div class=\"container\" id=\"tagessieger2\" style=\"display: block;\">";
        print_tagessieger_liste(get_curr_wett());
        print_tagessieger_geld(get_curr_wett());    
    echo "</div>";
    

} else {
    print_tagessieger_liste(get_curr_wett());
    print_tagessieger_geld(get_curr_wett());
}


?>
</div>
<br>
