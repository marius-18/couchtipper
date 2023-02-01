<div class="container-fluid">
<?php

require_once('src/include/lib/forms.inc.php');

if (!allow_date()){
   echo "Dieser Bereich ist nur f&uuml;r Administratoren!<br>
   Frage beim Administrator nach, um Rechte zum &Auml;ndern von Ergebnissen zu bekommen.";
   exit;
}


$spieltag = spt_select();
if (isset($_POST['spieltag'])){
   $spieltag = $_POST['spieltag'];
}

select_spieltag($spieltag);

$teil1 = "1";
$teil2 = "2";
$real_spieltag = $spieltag;

if (($spieltag > 17) && (get_wettbewerb_code(get_curr_wett()) == "BuLi") ){
    $teil1 = "2";
    $teil2 = "1";
    $spieltag = $spieltag - 17;
}

echo "<div class = \"content\"><br>";

?>


<?php


$show_formular = true;
$error_count = 0;
$spiel = array();

if (isset($_POST['spiel1'])){
   $spiel[1] = $_POST['spiel1'];
}
if (isset($_POST['spiel2'])){
   $spiel[2] = $_POST['spiel2'];
}
if (isset($_POST['spiel3'])){
   $spiel[3] = $_POST['spiel3'];
}
if (isset($_POST['spiel4'])){
   $spiel[4] = $_POST['spiel4'];
}
if (isset($_POST['spiel5'])){
   $spiel[5] = $_POST['spiel5'];
}
if (isset($_POST['spiel6'])){
   $spiel[6] = $_POST['spiel6'];
}
if (isset($_POST['spiel7'])){
   $spiel[7] = $_POST['spiel7'];
}
if (isset($_POST['spiel8'])){
   $spiel[8] = $_POST['spiel8'];
}
if (isset($_POST['spiel9'])){
   $spiel[9] = $_POST['spiel9'];
}



for ($sp_nr = 1; $sp_nr <10; $sp_nr++) {
    if (isset($spiel[$sp_nr])) {

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




$error = false;
$error_msg = "";
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
      </tr>

      <tr>
      <td></td>
      <td align = \"center\">20</td>
      <td align = \"center\">15</td>
      <td align = \"center\">18</td>
      <td align = \"center\">15</td>
      <td align = \"center\">17</td>
      <td align = \"center\">19</td> 

      </tr>

      <tr>
      <td></td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>
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
      </tr>

      <tr>
      <td></td>
      <td align = \"center\">15</td>
      <td align = \"center\">18</td>
      <td align = \"center\">20</td>
      <td align = \"center\">15</td>
      <td align = \"center\">17</td> 
      <td align = \"center\">19</td>

      </tr>

      <tr>
      <td></td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td> 

      </tr>
      ";


   }
   
   if ($tag == 2) {

      echo "
      <tr>
      <td></td>
      <td colspan = \"2\" align = \"center\">Di</td>
      <td colspan = \"2\" align = \"center\">Mi</td>
      </tr>

      <tr>
      <td></td>
      <td align = \"center\">18</td>
      <td align = \"center\">20</td>
      <td align = \"center\">18</td>
      <td align = \"center\">20</td>

      </tr>

      <tr>
      <td></td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>
      <td align = \"center\">30</td>

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





<?php

function spiele_term($main_datum, $sp_nr, $time){
   global $g_modus;

   if (get_wettbewerb_code(get_curr_wett()) == "WM"){
      wm_spiele_term($main_datum, $sp_nr, $time);
   }
   if (get_wettbewerb_code(get_curr_wett()) == "EM"){
      em_spiele_term($main_datum, $sp_nr, $time);
   }
   if (get_wettbewerb_code(get_curr_wett()) == "BuLi"){
      buli_spiele_term($main_datum, $sp_nr, $time);
   }
}


function wm_spiele_term($main_datum, $sp_nr, $time){

   $anstoss1 = $main_datum + 12*60*60;  // 12 Uhr
   $anstoss2 = $main_datum + 14*60*60;  // 14 Uhr
   $anstoss3 = $main_datum + 15*60*60;  // 15 Uhr
   $anstoss4 = $main_datum + 16*60*60;  // 16 Uhr
   $anstoss5 = $main_datum + 17*60*60;  // 17 Uhr
   $anstoss6 = $main_datum + 18*60*60;  // 18 Uhr
   $anstoss7 = $main_datum + 20*60*60;  // 20 Uhr
   $anstoss8 = $main_datum + 21*60*60;  // 21 Uhr

   $checked1 = "";
   $checked2 = "";
   $checked3 = "";
   $checked4 = "";
   $checked5 = "";
   $checked6 = "";
   $checked7 = "";
   $checked8 = "";

   if ($time == $anstoss1) {
      $checked1 = "checked";
   }
   if ($time == $anstoss2) {
      $checked2 = "checked";
   }
   if ($time == $anstoss3) {
      $checked3 = "checked";
   }
   if ($time == $anstoss4) {
      $checked4 = "checked";
   }
   if ($time == $anstoss5) {
      $checked5 = "checked";
   }
   if ($time == $anstoss6) {
      $checked6 = "checked";
   }
   if ($time == $anstoss7) {
      $checked7 = "checked";
   }
   if ($time == $anstoss8) {
      $checked8 = "checked";
   }

   echo "
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$anstoss1\" $checked1></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$anstoss2\" $checked2></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$anstoss3\" $checked3></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$anstoss4\" $checked4></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$anstoss5\" $checked5></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$anstoss6\" $checked6></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$anstoss7\" $checked7></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$anstoss8\" $checked8></td>
   ";
}

function em_spiele_term($main_datum, $sp_nr, $time){

   $anstoss1 = $main_datum + 15*60*60;  // 15 Uhr
   $anstoss2 = $main_datum + 18*60*60;  // 18 Uhr
   $anstoss3 = $main_datum + 21*60*60;  // 21 Uhr

   $checked1 = "";
   $checked2 = "";
   $checked3 = "";
   
   if ($time == $anstoss1) {
      $checked1 = "checked";
   }
   if ($time == $anstoss2) {
      $checked2 = "checked";
   }
   if ($time == $anstoss3) {
      $checked3 = "checked";
   }

   echo "
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$anstoss1\" $checked1></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$anstoss2\" $checked2></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$anstoss3\" $checked3></td>
   ";
}


function buli_spiele_term($main_datum, $sp_nr, $time) {
   $tag = date("N",$main_datum);
   
   if (($tag == 5) || ($tag == 6)) { // FReitag oder Samstag

      if ($tag == 5){ //Samstag Beginn
         $sp1  = strtotime(date("d.m.Y 20:30:59", $main_datum));
         $sp2 = strtotime(date("d.m.Y 15:30:59", $main_datum + 60*60*24));
         $sp3 = strtotime(date("d.m.Y 18:30:59", $main_datum + 60*60*24));
         $sp4 = strtotime(date("d.m.Y 15:30:59", $main_datum + 60*60*24*2));
         $sp5 = strtotime(date("d.m.Y 17:30:59", $main_datum + 60*60*24*2));
         $sp6 = strtotime(date("d.m.Y 19:30:59", $main_datum + 60*60*24*2));
      } else{
         $sp1 = strtotime(date("d.m.Y 15:30:59", $main_datum));
         $sp2 = strtotime(date("d.m.Y 18:30:59", $main_datum));
         $sp3 = strtotime(date("d.m.Y 20:30:59", $main_datum));
         $sp4 = strtotime(date("d.m.Y 15:30:59", $main_datum + 60*60*24));
         $sp5 = strtotime(date("d.m.Y 17:30:59", $main_datum + 60*60*24));
         $sp6 = strtotime(date("d.m.Y 19:30:59", $main_datum + 60*60*24));
      }
      
      $checked1 = "";
      $checked2 = "";
      $checked3 = "";
      $checked4 = "";
      $checked5 = "";
      $checked6 = "";
   
      if ($time == $sp1) {
         $checked1 = "checked";
      }
      if ($time == $sp2) {
         $checked2 = "checked";
      }
      if ($time == $sp3) {
         $checked3 = "checked";
      }
      if ($time == $sp4) {
         $checked4 = "checked";
      }
      if ($time == $sp5) {
         $checked5 = "checked";
      }
      if ($time == $sp6) {
         $checked6 = "checked";
      }


      echo "
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$sp1\" $checked1></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$sp2\" $checked2></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$sp3\" $checked3></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$sp4\" $checked4></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$sp5\" $checked5></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$sp6\" $checked6></td>
      ";

   }


   if ($tag == 2) {

      $sp1 = strtotime(date("d.m.Y 18:30:59", $main_datum));
      $sp2 = strtotime(date("d.m.Y 20:30:59", $main_datum));
      $sp3 = strtotime(date("d.m.Y 18:30:59", $main_datum + 60*60*24));
      $sp4 = strtotime(date("d.m.Y 20:30:59", $main_datum + 60*60*24));

      $checked1 = "";
      $checked2 = "";
      $checked3 = "";
      $checked4 = "";
   
      
      if ($time == $sp1) {
         $checked1 = "checked";
      }
      if ($time == $sp2) {
         $checked2 = "checked";
      }
      if ($time == $sp3) {
         $checked3 = "checked";
      }
      if ($time == $sp4) {
         $checked4 = "checked";
      }

      echo "
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$sp1\" $checked1></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$sp2\" $checked2></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$sp3\" $checked3></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$sp4\" $checked4></td>
      ";
   }

}

?>
</div>
