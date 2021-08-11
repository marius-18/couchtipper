<?php

//require_once('src/functions/main.inc.php');
//require_once('src/functions/template.inc.php');
require_once('src/include/lib/forms.inc.php');

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

//if ($spieltag > 17){
//    $teil1 = "2";
//    $teil2 = "1";
//    $spieltag = $spieltag - 17;
//}

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



   if (false) {

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

   if (false) {

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


   if (true) {

      echo "
      <tr>
      <td></td>
      <td align = \"center\">15:00</td>
      <td align = \"center\">18:00</td>
      <td align = \"center\">21:00</td>


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

   if ($g_modus == "WM"){
      wm_spiele_term($main_datum, $sp_nr, $time);
   }
   if ($g_modus == "EM"){
      em_spiele_term($main_datum, $sp_nr, $time);
   }
   if ($g_modus == "BULI"){
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


   if ($tag == 5){ // FReitag oder Samstag

      $fr = $main_datum + (20*60*60) + 30*60;
      $sa1 = $main_datum + (24*60*60) + (15*60*60) + (30*60);
      $sa2 = $sa1 + 3*60*60;
      $so1 = $sa1 + 24*60*60;
      $so2 = $so1 + 2*60*60+30*60;
      $so_frueh = $so1 - 2*60*60;
      $mo = $fr + (3*24*60*60);


      if ($time == $fr) {
         $checked1 = "checked";
      }
      if ($time == $sa1) {
         $checked2 = "checked";
      }
      if ($time == $sa2) {
         $checked3 = "checked";
      }
      if ($time == $so1) {
         $checked4 = "checked";
      }
      if ($time == $so2) {
         $checked5 = "checked";
      }
      if ($time == $so_frueh) {
         $checked6 = "checked";
      }
      if ($time == $mo) {
         $checked7 = "checked";
      }


      echo "
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$fr\" $checked1></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$sa1\" $checked2></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$sa2\" $checked3></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$so_frueh\" $checked6></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$so1\" $checked4></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$so2\" $checked5></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$mo\" $checked7></td>
      ";
   }

   if ($tag == 6){ // FReitag oder Samstag

      $sa1 = $main_datum + (15*60*60) + (30*60);
      $sa2 = $sa1 + 3*60*60;
      $sa3 = $sa2 + 2*60*60;
      $so1 = $sa1 + 24*60*60;
      $so2 = $so1 + 2*60*60+30*60;
      $so_frueh = $so1 - 2*60*60;
      $mo = $fr + (3*24*60*60);


      if ($time == $sa3) {
         $checked1 = "checked";
      }
      if ($time == $sa1) {
         $checked2 = "checked";
      }
      if ($time == $sa2) {
         $checked3 = "checked";
      }
      if ($time == $so1) {
         $checked4 = "checked";
      }
      if ($time == $so2) {
         $checked5 = "checked";
      }
      if ($time == $so_frueh) {
         $checked6 = "checked";
      }
      if ($time == $mo) {
         $checked7 = "checked";
      }


      echo "
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$sa1\" $checked2></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$sa2\" $checked3></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$sa3\" $checked1></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$so_frueh\" $checked6></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$so1\" $checked4></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$so2\" $checked5></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$mo\" $checked7></td>
      ";
   }


   if ($tag == 2) {
      $di1 = $main_datum + 18*60*60 + 30*60;
      $di2 = $di1 + 2*60*60;
      $mi1 = $di1 + 24 * 60*60;
      $mi2 = $di2 + 24*60*60;


      if ($time == $di1) {
         $checked1 = "checked";
      }
      if ($time == $di2) {
         $checked2 = "checked";
      }
      if ($time == $mi1) {
         $checked3 = "checked";
      }
      if ($time == $mi2) {
         $checked4 = "checked";
      }

      echo "
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$di1\" $checked1></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$di2\" $checked2></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$mi1\" $checked3></td>
      <td align = \"center\"><input type=\"radio\" name=\"spiel$sp_nr\" value=\"$mi2\" $checked4></td>
      ";
   }

}


?>
