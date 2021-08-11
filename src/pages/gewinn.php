<?php
//include_once("src/functions/main.inc.php");

function gewinn($E, $S, $G, $d){

// Das sollte irgendwo in ein template.. die funktion braucht man nochmal für die startseite!


// E = Einsatz, S = Spieler, G = Gewinner, d = Dämpfung [1.5]
   $sum = 0;
   for ($i = 0; $i < $G; $i++){
      $sum += pow($i,$d);  
   }

   $help = $E * ($S-$G)/$sum;

   for ($i = 1; $i <= $G; $i++){
      $gewinn[$i] = round(pow(($G-$i),$d) * $help + $E -0.1, 0); //-0.1 damit 0.5 abgerundet wird. --> gewinn geht immer auf!
      $prozent[$i] = round($gewinn[$i]/($E*$S)*100,1);
   }

  return array($gewinn,$prozent);
}
?>

<div class="container">
<div class="alert alert-danger">
<strong>Achtung:</strong> der Gewinn hängt davon ab, wie viele User mittippen. Damit kann sich die Verteilung noch ändern.</div>
Der Gewinn für Platz x berechnet sich mittels: <br><br> 
<img src="images/gewinn.png" width = "300px" max-width = "200px"><br><br>

<?php

$sql = "SELECT einsatz, anz_user,gewinner,daempfung FROM Saison WHERE jahr = '2017' AND runde = '1'";
foreach ($g_pdo->query($sql) as $row) {
   $E = $row['einsatz'];
   //$S = $row['anz_user'];
   $g = $row['gewinner'];
   $d = $row['daempfung'];
}

$S = anz_user();
$G = round($S*$g); // Anzahl der Gewinner


list($gewinn,$prozent) =  gewinn($E,$S,$G,$d);


echo "
wobei <br>
E = Einsatz (=$E&#8364;)<br>
G = Anzahl gewinnender Spieler (=$G)<br>
S = Anzahl der Spieler (=$S)
";

echo"
<br><br>
Damit ergibt sich bisher folgende Verteilung

<table align = \"center\">
<tr bgcolor=\"#B6B6B4\"><th>Platz</th><th>%</th><th> &euro; (gerundet) </th></tr>

";

for ($i = 1; $i <= $G; $i++){
echo "
<tr>
<th> $i </th>
<th>". $prozent[$i]."</th>
<th>". $gewinn[$i]."</th>
</tr>";
}

echo " </table>
(Alle Angaben sind ohne Gew&auml;hr ;))";



?>
</div>
<br>
