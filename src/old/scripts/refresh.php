<div class= "content"> Die Datenbank wird komplett aktualisiert!

<?php
require_once("src/functions/refresh.php");

###
#Lösche Komplette Rangliste
###
$statement = $g_pdo->prepare("TRUNCATE TABLE Rangliste");
$result = $statement->execute();

###
#Lösche komplette Tabelle
###
$statement = $g_pdo->prepare("TRUNCATE TABLE Tabelle");
$result = $statement->execute();


###
#alles neu aufsetzen
###
for ($a=1; $a<=34; $a++){
   update_tabelle($a);
   update_rangliste($a);
}


?>

</div><br>
