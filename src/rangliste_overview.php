<h2> Rangliste Overview:</h2>


<?php
require_once('src/include/code/rangliste.inc.php');

$seasons = [-5,-3,-2,-1,0,1,2,3,4,5,6];   
$seasons = select_season($seasons);


function gesamt_rangliste($seasons, $modus){
    rsort($seasons);
    
    foreach ($seasons as $saison){
        $bewerb = array($saison,0);
        new_db_connection($bewerb);    
        
        list ($punkte, $spiele, $akt_punkte, $schnitt, $letzte_punkte, $user, $platz, $spieltagssieger, $spieltagssieger_last) = rangliste(0,34, "", $bewerb);
    
    
        foreach ($user as $index => $uid){
            
            $saison_punkte[$uid][$saison] = $punkte[$index];
            $platz_r[$uid][$saison] = $platz[$uid];
            $ges_user[$uid] = $uid;
            
            
            if (!isset($ges_punkte[$uid] )){
                $ges_punkte[$uid] = $punkte[$index];
            } else {
                $ges_punkte[$uid] += $punkte[$index];
            }
            
            if (!isset($ges_platz[$uid] )){
                $ges_platz[$uid] = $platz[$uid];
                $anz_platz[$uid] = 1;
            } else {
                $ges_platz[$uid] += $platz[$uid];
                $anz_platz[$uid] += 1;

            }
        }
    }
    
    foreach ($ges_platz as $index => $uid){
        $schnitt[$index] = round($ges_platz[$index] / $anz_platz[$index], 2);
    }

       
    
    return array($saison_punkte, $ges_punkte, $ges_user, $platz_r, $schnitt);
}



list ($punkte, $gesamt_punkte, $user, $platz, $schnitt) = gesamt_rangliste($seasons, "");
print_rangliste_overview($punkte, $gesamt_punkte, $user, $platz, $seasons, "punkte");
echo "<br><br><br>";
print_rangliste_overview($punkte, $schnitt, $user, $platz, $seasons, "platz");
?>






