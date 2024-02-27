<?php
require_once('src/include/code/tabelle.inc.php');
require_once('src/include/code/get_games.inc.php');
?>

<script>
function rank_ausblenden(wert) {
    a = Math.floor(wert/100);
    
    for (i=a*100; i<a*100+9; i++){
        if (i == wert){
            document.getElementById(wert).style.display = "";
            document.getElementById(wert+10).className = "btn btn-info focus m-1";
        } else{
            document.getElementById(i).style.display = "none";
            document.getElementById(i+10).className = "btn btn-info m-1";
        }
    }
}

function clear_all_kreuztabelle(){
    elems = document.getElementsByClassName("hovertd");
    
    /* TODO: HIER MUSS NOCH DIE 19 ersetz werden*/
    for (let col = 1; col < 19; col++) {
        name = "kreuztab_col" + col;
        elems = document.getElementsByClassName(name);
        for (let i = 0; i < elems.length; i++) {
            elems[i].classList.remove("hovertd");   
        }
    }
    
    /* TODO: HIER MUSS NOCH DIE 19 ersetz werden*/
    for (let row = 1; row < 19; row++) {
        var name1 = "kreuztab_row" + row;
        document.getElementById(name1).classList.remove("hovertd");
    }
}

function highlight_kreuztabelle(row, col){
    clear_all_kreuztabelle();
    
    if (col != 0){
        var name = "kreuztab_col" + col;
        elem = document.getElementsByClassName(name);
        
        for (let i = 0; i < elem.length; i++) {
            elem[i].classList.add("hovertd");
        }
    }
    
    if (row != 0){
        var name1 = "kreuztab_row"+row;
        document.getElementById(name1).classList.add("hovertd");
    }
}


</script>


<style>
.hovertd{
    background-color:rgba(0, 0, 0, 0.175);   
}
</style>


<?php
echo "  <div class=\"btn-group\">
            <button type=\"button\" class=\"btn btn-info focus m-1\" onclick = \"rank_ausblenden(500)\" id=\"510\">Gesamt</button>
            <button type=\"button\" class=\"btn btn-info m-1 \" onclick = \"rank_ausblenden(501)\" id=\"511\">Heim</button>
            <button type=\"button\" class=\"btn btn-info m-1\"  onclick = \"rank_ausblenden(502)\" id=\"512\">Ausw&auml;rts</button>
        </div>
        
        <br>
        
        <div class=\"btn-group\">
            <button type=\"button\" class=\"btn btn-info m-1\" onclick = \"rank_ausblenden(503)\" id=\"513\">Tendenz</button>";

if ((get_wettbewerb_code(get_curr_wett()) == "BuLi") && (get_curr_wett()[1] == 1)) {
    echo "      
            <button type=\"button\" class=\"btn btn-info m-1\" onclick = \"rank_ausblenden(504)\" id=\"514\">Hinrunde</button>
            <button type=\"button\" class=\"btn btn-info m-1\" onclick = \"rank_ausblenden(505)\" id=\"515\">R&uuml;ckrunde</button>
        </div>";
}

if (get_wettbewerb_code(get_curr_wett()) == "BuLi") {
    echo "
        <div class=\"btn-group\">
            <button type=\"button\" class=\"btn btn-info m-1\" onclick = \"rank_ausblenden(506)\" id=\"516\">Verlauf</button>
            <button type=\"button\" class=\"btn btn-info m-1\" onclick = \"rank_ausblenden(507)\" id=\"517\">Bereich</button>
            <button type=\"button\" class=\"btn btn-info m-1\" onclick = \"rank_ausblenden(508)\" id=\"518\">Kreuz</button>
        </div>";
}

echo "<br><br>";


## Ab hier noch die einzelnen Tabellen Berechnen und Ausgeben:

## Gesamt Tabelle
print_tabelle(tabelle("", 0), 500,"");

## Heimtabelle
print_tabelle(tabelle("Heim", 0), 501, "none");

## Auswärtstabelle
print_tabelle(tabelle("Auswaerts", 0), 502, "none");

## Tendenztabelle
print_tabelle(tabelle("Tendenz", akt_spieltag()-5), 503, "none");

## Hinrundentabelle
print_tabelle(tabelle("Hinrunde", 0), 504, "none");
    
## Rückrundentabelle
print_tabelle(tabelle("Rückrunde", 17), 505, "none");

## Verlaufstabelle
print_tabelle_verlauf(tabelle_verlauf(), 506, "none");

## Teiltabelle
print_tabelle(tabelle("Bereich", 0), 507, "none");

## Kreuztabelle
print_kreuztabelle(tabelle_verlauf(), 508, "none"); 

      

