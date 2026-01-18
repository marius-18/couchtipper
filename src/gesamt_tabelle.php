<div class="container-fluid">

<?php
require_once('src/include/code/rangliste.inc.php');

#$seasons = [-5,-3,-2,0,1,2,4,5,6,8,9];
$seasons = [-2,0,1,2,4,5,6,8,9];
$seasons = select_season($seasons);

echo "<br>";
    
if ($index == 1){
    list ($punkte, $spiele, $schnitt, $user, $platz) = alltime_rangliste($seasons, "");
    print_alltime_rangliste($punkte, $spiele, $schnitt, $user, $platz);
}

if ($index == 2){
    list ($punkte, $gesamt_punkte, $user, $platz, $schnitt) = rangliste_seasons($seasons, "");
    print_rangliste_seasons($punkte, $gesamt_punkte, $user, $platz, $seasons, "punkte");
}


if ($index == 3){
    list ($punkte, $gesamt_punkte, $user, $platz, $schnitt) = rangliste_seasons($seasons, "");
    print_rangliste_seasons($punkte, $schnitt, $user, $platz, $seasons, "platz");
}

?>


</div>




