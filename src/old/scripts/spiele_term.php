<?php

require_once('src/functions/main.inc.php');
require_once('src/functions/template.inc.php');


if (!allow_date()){
echo "Dieser Bereich ist nur f&uuml;r Administratoren!<br>
Frage beim Administrator nach, um Rechte zum &Auml;ndern von Ergebnissen zu bekommen.";
exit;

}


$spieltag = $_POST['spieltag'];
if ($spieltag == ""){
   $spieltag = spt_select();
}

select_spieltag($spieltag);

$teil1 = "1";
$teil2 = "2";
$real_spieltag = $spieltag;

if ($spieltag > 17){
   $teil1 = "2";
   $teil2 = "1";
   $spieltag = $spieltag - 17;
}

echo "<div class = \"content\"><br>";


?>


<?php


$show_formular = true;
$error_count = 0;

$spiel[1] = $_POST['spiel1'];
$spiel[2] = $_POST['spiel2'];
$spiel[3] = $_POST['spiel3'];
$spiel[4] = $_POST['spiel4'];
$spiel[5] = $_POST['spiel5'];
$spiel[6] = $_POST['spiel6'];
$spiel[7] = $_POST['spiel7'];
$spiel[8] = $_POST['spiel8'];
$spiel[9] = $_POST['spiel9'];




for ($sp_nr = 1; $sp_nr <10; $sp_nr++) {
   if ($spiel[$sp_nr] != "") {

      $eingabe = $spiel[$sp_nr];
      $sql = "UPDATE Spieltage SET datum$teil1 = $eingabe WHERE sp_nr = $sp_nr AND spieltag = $spieltag";
      $result = $g_pdo->query($sql);
      if ($result != true){
         $error_count++;
      }
  }
}









$sql = "SELECT datum FROM Datum WHERE spieltag = $real_spieltag";

foreach ($g_pdo->query($sql) as $row) {
   $main_datum = $row['datum'];
}



$sql = "SELECT datum$teil1,sp_nr FROM Spieltage WHERE spieltag = $spieltag";

foreach ($g_pdo->query($sql) as $row) {
   $sp_nr = $row['sp_nr'];
   if (isset($row['datum1'])){
      $spiel[$sp_nr] = $row['datum1'];
   } else {
      $spiel[$sp_nr] = $row['datum2'];

   }
}





if ($main_datum == "") {
   $show_formular = false;
   $error = true;
   $error_msg = "Es muss erst ein Termin f&uuml;r den Spieltag ausgew&auml;hlt werden!";

}










if ($show_formular){

   $tag = date("N",$main_datum);

   echo "<form action=\"\" method=\"post\">

   <table border = \"1\" align = \"center\" width = \"100%\">";



   if ($tag == 5) {

      echo "
      <tr>
      <td></td>
      <td align = \"center\">Fr</td>
      <td colspan = \"2\" align = \"center\">Sa</td>
      <td colspan = \"3\" align = \"center\">So</td>
      <td align = \"center\">Mo</td>
      </tr>

      <tr>
      <td></td>
      <td align = \"center\">20</td>
      <td align = \"center\">15</td>
      <td align = \"center\">18</td>
      <td align = \"center\">13</td>
      <td align = \"center\">15</td>
      <td align = \"center\">18</td> 
      <td align = \"center\">20</td>

      </tr>

      <tr>
      <td></td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>
      <td align = \"center\">00</td> 
      <td align = \"center\">30</td>

      </tr>
      ";


   }

   if ($tag == 6) {

      echo "
      <tr>
      <td></td>
      <td colspan = \"3\" align = \"center\">Sa</td>
      <td colspan = \"3\" align = \"center\">So</td>
      <td align = \"center\">Mo</td>
      </tr>

      <tr>
      <td></td>
      <td align = \"center\">15</td>
      <td align = \"center\">18</td>
      <td align = \"center\">20</td>
      <td align = \"center\">13</td>
      <td align = \"center\">15</td>
      <td align = \"center\">18</td> 
      <td align = \"center\">20</td>

      </tr>

      <tr>
      <td></td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>
      <td align = \"center\">00</td> 
      <td align = \"center\">30</td>

      </tr>
      ";


   }


   if ($tag == 2) {

      echo "
      <tr>
      <td></td>
      <td align = \"center\" colspan = \"2\">Dienstag</td>
      <td align = \"center\" colspan = \"2\">Mittwoch</td>
      </tr>

      <tr>
      <td></td>
      <td align = \"center\">18:30</td>
      <td align = \"center\">20:30</td>
      <td align = \"center\">18:30</td>
      <td align = \"center\">20:30</td>


      </tr>
      ";

   }



   $sql = "SELECT sp_nr, t1.team_name AS Team_name$teil1, t2.team_name AS Team_name$teil2,sp_nr
   FROM `Spieltage`,Teams t1, Teams t2
   WHERE (`spieltag` = $spieltag) AND (t1.team_nr = team1) AND (t2.team_nr = team2)";


   foreach ($g_pdo->query($sql) as $row) {

      echo "<tr>
      ";  
      $sp_nr = $row['sp_nr'];
      $t = $spiel[$sp_nr];
      echo "<td align = \"center\">".$row['Team_name1']."-".$row['Team_name2']."</td>";
      echo spiele_term($main_datum, $sp_nr, $t);


      echo "</tr>
      ";

   }


   echo "</table><br>
   <input type=\"hidden\" name =\"spieltag\" value=\"$real_spieltag\" visible=\"false\">
   <input type=\"Submit\" value=\"Enter\">
   </form>
   ";

}


if ($error) {
   echo "<font color = \"red\"><b> $error_msg</b> </font><br><br>";
} else {
   echo "Es gab ".$error_count." Fehler beim Speichern";
}

?>

</div>
<br>
