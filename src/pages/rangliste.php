<?php

##############################################################
######    Hinrunde / Rückrunde Tabelle!!
##############################################################

require_once('src/include/code/rangliste.inc.php');

function print_rangliste($begin, $ende, $modus){

    list($punkte1, $spiele1, $akt_punkte1, $schnitt1, $letzte_punkte1, $user1, $platz_alt) = rangliste($begin, $ende-1, $modus);
    list($punkte, $spiele, $akt_punkte, $schnitt, $letzte_punkte, $user, $platz) = rangliste($begin, $ende, $modus);

    echo "
    <div class=\"table-responsive\">
        <table class=\"table table-sm table-striped  table-hover text-center center text-nowrap\" align=\"center\">
        <tr class=\"thead-dark\"><th>Pl</th><th>Spieler</th><th>&#931</th><th>Spt.</th><th>&#216;</th>";

    echo "<th><i class=\"fas fa-arrow-down\"></th><th><i class=\"fas fa-arrow-left\"></th><th></th><tr>";

    foreach ($user as $i => $nr){
        if ($user[$i] == get_usernr()){
            $logged=" class=\"table-success\"";
        } else {
            $logged ="";
        }
        $dif[$i] = $platz_alt[$nr] - $platz[$nr] ;
        if ($platz_alt[$nr] < $platz[$nr]){
            $aenderung = "<span class=\"badge badge-pill badge-danger\"><i class=\"fas fa-arrow-down\"></i> " . -$dif[$i] ." </span>";
        }

        if ($platz_alt[$nr] == $platz[$nr]){
            $aenderung = "";
        }   
  
        if ($platz_alt[$nr] > $platz[$nr]){
            $aenderung = "<span class=\"badge badge-pill badge-success\"><i class=\"fas fa-arrow-up\"></i> " . $dif[$i] . "</span>";
        }

        echo "  <tr $logged>
                <td>$platz[$nr].</td>
                <td>".get_username_from_nr($user[$i])."</td>
                <td>$punkte[$i]</td> 
                <td>$spiele[$i]</td>
                <td>$schnitt[$i]</td>
                <td>$akt_punkte[$i]</td>
                <td>$letzte_punkte[$i]</td>"; 

        echo "<th>$aenderung</th>";
        echo "
            </tr>";
   }
   
   echo "</table></div>";
}


?>

<div class="container">

<script>
function programm_ausblenden(wert) {
   if (document.getElementById(wert).style.display == ""){
      document.getElementById(wert).style.display = "none";
   } else {
      document.getElementById(wert).style.display = "";
   }
}
</script>

<?php


$farbe = "EE9900";

$spieltag = akt_spieltag();

if($spieltag <= 17) {
$visible_h = "";
$visible_r = "none";
} else {
$visible_h = "none";
$visible_r = "";
}



#echo "<table align=\"center\">";

#########
#Hinrunde
#########
#echo "   <tr onclick = \"programm_ausblenden(400)\" align = \"center\" bgcolor = \"$farbe\">
#         <td><span style = \" font-size:150%\">
#         <b>Hinrunde</b>
#         </span></td></tr>";

#echo "<tr id = \"400\" style = \"display: $visible_h;\"><td>";
print_rangliste(1,akt_spieltag(),1);
#echo "</td></tr>";

/*
##########
#Rückrunde
##########
echo "   <tr onclick = \"programm_ausblenden(401)\" align = \"center\" bgcolor = \"$farbe\">
         <td><span style = \" font-size:150%\">
         <b>R&uuml;ckrunde</b>
         </span></td></tr>";

echo "<tr id = \"401\" style = \"display: $visible_r;\"><td>";
print_rangliste(18,34,1);
echo "</td></tr>";

#######
#Gesamt
#######
echo "   <tr onclick = \"programm_ausblenden(402)\" align = \"center\" bgcolor = \"$farbe\">
         <td><span style = \" font-size:150%\">
         <b>Gesamt</b>
         </span></td></tr>";

echo "<tr id = \"402\" style = \"display: none;\"><td>";
print_rangliste(1,34,1);
echo "</td></tr>";
*/

#echo "</table>";

?>

</div>
