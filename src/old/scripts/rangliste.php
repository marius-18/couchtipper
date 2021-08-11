<?php
function print_rangliste($begin, $ende, $modus, $id, $show){

   list($punkte, $spiele, $akt_punkte, $schnitt, $user_name, $letzte_punkte, $user, $platz, $change) = rangliste($begin, $ende, $modus);

   echo "
      <table class=\"table table-sm table-responsive-sm text-center center\" id = \"$id\" style=\"display: $show;\">
   <tr><th>Pl</th><th>Spieler</th><th>&#931</th><th>Spt.</th><th>&#216;</th><th>Akt.</th><th>letzter</th><th></th><tr>";

   foreach ($user_name as $i => $nr){
      if ($user[$i] == get_usernr()){
         $logged=" bgcolor=\"orange\"";
      } else {
         $logged ="";
      }

      echo "<tr$logged>
      <th>$platz[$i].</th>
      <th>$user_name[$i]</th>
      <th>$punkte[$i]</th>
      <th>$spiele[$i]</th>
      <th>$schnitt[$i]</th>
      <th>$akt_punkte[$i]</th>
      <th>$letzte_punkte[$i]</th>
      <th>$change[$i]</th>
      </tr>";
   }
   
   echo "</table>";
}
?>



<?php
require_once('src/functions/main.inc.php');
require_once('src/functions/rangliste.inc.php');

$spieltag = akt_spieltag();

if($spieltag <= 17) {
    $visible_h = "";
    $visible_r = "none";
    $focus_h = "focus";
    $focus_r = "";
} else {
    $visible_h = "none";
    $visible_r = "";
    $focus_h = "";
    $focus_r = "focus";
}


echo "<button type=\"button\" class=\"btn btn-info $focus_h\" onclick = \"rank_ausblenden(400)\" id=\"410\">Hinrunde</button>
<button type=\"button\" class=\"btn btn-info $focus_r\" onclick = \"rank_ausblenden(401)\" id=\"411\">R&uuml;ckrunde</button>
<button type=\"button\" class=\"btn btn-info\" onclick = \"rank_ausblenden(402)\" id=\"412\">Gesamt</button><br><br>";

print_rangliste(1,17,1,400,$visible_h);
print_rangliste(18,34,1,401,$visible_r);
print_rangliste(1,34,1,402,"none");


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
