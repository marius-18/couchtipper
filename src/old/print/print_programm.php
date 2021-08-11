<?php

function print_programm($modus, $spieltag, $team_nr1, $team_nr2, $team_name, $datum, $tore1, $tore2){

   $akt_spt = akt_spieltag();
   $farbe = "EE9900";

   $id = rand(200,299);

   if ((($modus == "Hinrunde") && ($akt_spt > 17)) || (($modus != "Hinrunde") && ($akt_spt <= 17))){
      $visible = "none";
   }

   echo "<tr onclick = \"programm_ausblenden($id)\" align = \"center\" bgcolor = \"$farbe\">
         <td><span style = \" font-size:150%\">
         <b>$modus</b>
         </span></td></tr>";

   echo "<tr id = \"$id\" style = \"display: $visible;\"><td>

         <table border = \"0\"  align = \"center\">";

   foreach ( $team_nr1 as $spt => $team1) {

      if (($modus == "Hinrunde") && ($spt>17)){
         break;
      }

      if (($modus != "Hinrunde") && ($spt<=17)){
         continue;
      }


      echo "<tr>
            <td><b>$spt.</b> Spieltag</td>

            <td rowspan = \"3\"><img src = \"/images/Vereine/$team1.gif\" width = \"50\" height = \"50\"></td>
            <td rowspan = \"3\" align = \"center\"><span style = \" font-size:200%\"><b>";

      if ($tore1[$spt] == "") {
         echo "- : -";
      } else {
         echo $tore1[$spt]." : ".$tore2[$spt];
      }

      echo "</b></span></td>";

      echo "<td rowspan = \"3\"><img src = \"/images/Vereine/".$team_nr2[$spt].".gif\" width = \"50\" height = \"50\"></td></tr>

            <tr><td>&nbsp;&nbsp;<b>".$team_name[$spt]."</b></td></tr>";
      echo "<tr><td>";

      if ($datum[$spt] != 0) {
         echo stamp_to_date_programm($datum[$spt]);
      } else { 
         echo "&nbsp;";
      }

      echo "</td></tr>
            <tr><td><br></td></tr>
           ";


   }


   echo "</table></td></tr>";



}



?>
