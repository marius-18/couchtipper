
<script>
function programm_ausblenden(wert) {
   if (document.getElementById(wert).style.display == ""){
      document.getElementById(wert).style.display = "none";
   } else {
      document.getElementById(wert).style.display = "";

   }
}

</script>



<?php
require_once("src/functions/main.inc.php");
require_once("src/functions/programm.php");
require_once("src/functions/template.inc.php");
require_once("src/print/print_programm.php");

$team = $_POST['team'];

if ($team == ""){
   $team = my_team();
   if ($team == ""){
      $team = 12;
   }
} else {
   $team = $team;
}

select_team($team);


$spieltag_select = spt_select();

echo "<div class=\"rest\"><a href='' onclick='slider3.next();return false;'>Wechseln!</a></div><br>";
echo "<br><div class = \"content\">";


echo "
    
<div id='slider3' class='swipe'>
  <ul>
    <li style='display:block'>
     <div>
      ";
         list($spieltag, $team_nr1, $team_nr2, $team_name, $datum, $tore1, $tore2) = programm($team,$spieltag_select,34);

         echo "<table border = \"0\" width = \"100%\">";

         print_programm ("Hinrunde", $spieltag, $team_nr1, $team_nr2, $team_name, $datum, $tore1, $tore2);

         print_programm ("Rückrunde", $spieltag, $team_nr1, $team_nr2, $team_name, $datum, $tore1, $tore2);

         echo"</table>";

      echo "
     </div>
    </li>
    <li style='display:none'>
     <div>
      ";
          list($spieltag, $team_nr1, $team_nr2, $team_name, $datum, $tore1, $tore2) = programm($team,1,$spieltag_select-1);

          echo "<table border = \"0\" width = \"100%\">";

          print_programm ("Hinrunde", $spieltag, $team_nr1, $team_nr2, $team_name, $datum, $tore1, $tore2);

          print_programm ("Rückrunde", $spieltag, $team_nr1, $team_nr2, $team_name, $datum, $tore1, $tore2);

          echo"</table>";

      echo "
     </div>
    </li>
  </ul>
</div>
";
?>


</div>
<br>

<script src='src/swipe.js'></script>

<script>
var slider3 = new Swipe(document.getElementById('slider3'));
</script>


