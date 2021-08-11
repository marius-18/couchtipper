<?php

function select_spieltag ($spieltag) {

   echo"

   <form method=\"post\">
   <select name=\"spieltag\" onchange=\"this.form.submit()\">

   ";
   $select = "";

   for ($i = 1; $i < 35; $i++){

      if ($spieltag == $i){
         $select = "selected";
      }

      echo "  <option value=\"$i\" $select>$i. Spieltag</option>
      ";
      $select = "";
   }

   echo"
   </select>
   </form>

   ";

}

function select_team($my_team){
   global $g_pdo;


   echo"<form method=\"post\"><select name=\"team\" onchange=\"this.form.submit()\">";

   $sql = "SELECT team_nr, team_name FROM `Teams` WHERE 1";
   foreach ($g_pdo->query($sql) as $row) {
      $team_nr = $row['team_nr'];
      $team_name = $row['team_name'];

      if ($team_nr == $my_team){
         $abc = " selected";
      } else { $abc = "";}

      echo"  <option value=\"$team_nr\" $abc>$team_name</option>";
   }

   echo"
   </select>
   </form>

   ";
}

function spiele_term($main_datum, $sp_nr, $time) {


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


function stamp_to_date($timestamp){

$tage = array("Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag");
$tag = date("w", $timestamp);

$wochentag = $tage[$tag];

$datum = date("d.m.Y - H:i", $timestamp);

return $wochentag.", ".$datum."<br>";


}

function stamp_to_date_programm($timestamp){

$tage = array("So","Mo","Di","Mi","Do","Fr","Sa");
$tag = date("w", $timestamp);

$wochentag = $tage[$tag];

$datum = date("d.m. - H:i", $timestamp);

return $wochentag.", ".$datum."<br>";

}


?>
