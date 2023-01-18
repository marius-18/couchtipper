<script>
function programm_ausblenden(wert) {
    if (document.getElementById(wert).style.display == ""){
        document.getElementById(wert).style.display = "none";
        document.getElementById("label" + wert).innerHTML = "einblenden";
        //window.scrollTo({top: document.getElementById('aktuell').offsetTop, left:document.getElementById('aktuell').offsetLeft, behavior:"smooth"});
    } else {
        document.getElementById(wert).style.display = "";
        document.getElementById("label" + wert).innerHTML = "ausblenden";
        //window.scrollTo({top: document.getElementById('aktuell').offsetTop, left:document.getElementById('aktuell').offsetLeft, behavior:"smooth"});
    }
}

function toggle_modus(wert){
    if (document.getElementById(wert).style.display == ""){
        document.getElementById("modus").innerHTML = "Hinrunde";
    } else {
        document.getElementById("modus").innerHTML = "R&uuml;ckrunde";
    }
}

</script>


<div class="container">

<?php

require_once("src/include/code/programm.inc.php");
require_once('src/include/lib/forms.inc.php');


    $vorschau = 4;

    $team = select_team();
    
    $spieltag_select = spt_select();

if ($team != ""){
    ### Nur anzeigen, wenn ein Team ausgewählt wurde
    
    list($spieltag, $team_nr1, $team_nr2, $team_name, $datum, $zeitraum, $tore1, $tore2) = programm($team,1,34);

    if ($spieltag_select > 1){
        // Am ersten Spieltag gibt es keine vorherigen Spiele..
        echo "<br><button type=\"button\" class=\"btn btn-info\" onclick=\"programm_ausblenden(1025); toggle_modus(1025)\"><b>vorherige Spiele <span id=\"label1025\">einblenden</span></b></button><br><br>";
    }
    echo "<div class=\"table-responsive\">
            <table class=\"table table-sm text-center center text-nowrap\" align=\"center\" >";

                
        if ($spieltag_select <= 17){
            $modus = "Hinrunde";
        } else {
            $modus = "R&uuml;ckrunde";
        } 
        
        echo "<tr class=\"table-active\"> <td colspan = \"4\"><span style = \" font-size:150%\"><b><span id=\"modus\">$modus</span></b></span></td></tr>";
        
            // vorherige Spiele
            print_programm ($spieltag, $team_nr1, $team_nr2, $team_name, $datum, $zeitraum, $tore1, $tore2, 1, $spieltag_select-1,1025, False);
            
            // nächste Spiele
            print_programm ($spieltag, $team_nr1, $team_nr2, $team_name, $datum, $zeitraum, $tore1, $tore2, $spieltag_select, $spieltag_select+$vorschau, "aktuell", True);

            //Spiele danach
            print_programm ($spieltag, $team_nr1, $team_nr2, $team_name, $datum, $zeitraum, $tore1, $tore2, $spieltag_select + $vorschau + 1,  34, 1026, False); //34 = max spiele
        
        echo "</table></div>";
        
        if ($spieltag_select < 34 - $vorschau) {
            // Am Ende gibt es auch keine Spiele mehr
            echo "<br><button type=\"button\" class=\"btn btn-info\" onclick=\"programm_ausblenden(1026)\"><b>n&auml;chste Spiele <span id=\"label1026\">einblenden</span></b></button><br><br>";
        }
        
}
        
?>

</div>
<br>



