<?php



// HIER FEHLT DIE ÜBERSICHTVON ANDEREN TIPPS

// WENN NICHT ANGEMELDET TIPPS LEER



require_once("src/functions/get_tipps.php");

function print_games($args, $modus, $change){

  list ($datum, $team_heim, $team_aus, $tore_heim, $tore_aus, $team_heim_nr, 
  $team_aus_nr, $real_sp_nr, $real_spieltag) = $args;


  echo "<form method=\"POST\" action =\"\">";

  echo "<table align=\"center\" border=\"0\" width=\"100%\"  style =\"border-radius: 10px\">";

  echo "<tr>";
  echo "<td colspan=\"5\" bgcolor = \"lightgrey\"><font size=\"3\">"; // FARBE muss auch AUS DB
  
  if ($datum[0] == 0) {
    echo "<p align=\"center\">Dieser Spieltag ist noch <b>nicht</b> terminiert worden. ";
    if ($modus == "Tipps") {
      echo "<br>Du kannst deshalb noch <b>nicht</b> tippen!</p>";
    }
  } else {
    echo stamp_to_date($datum[0]);
  }  

  echo "</font></td></tr>";

  $help = $datum[0];


  for ($i = 0; $i <= 8; $i++){

    $i1 = $i + 100 ;

    if ($help != $datum[$i]){    // fuegt Datums-Zeile ein
      echo "<tr><td colspan=\"5\" bgcolor = \"#CC9999\"><font size=\"3\">";
      echo stamp_to_date($datum[$i]);
      echo "</font></td></tr>";

      $help = $datum[$i];
    }
    
    echo "<tr>";

    echo "<td align=\"right\"  onclick=\"myFunction($i1)\"><b>$team_heim[$i]</b></td>
    ";
    if ($modus == "Spieltag") {
      echo "<td align=\"right\"  onclick=\"myFunction($i1)\"><img src=\"images/Vereine/$team_heim_nr[$i].gif\" height=\"22\"></td>
      ";
    }
    echo "<td align=\"center\"><b>$tore_heim[$i] : $tore_aus[$i]</b></td>
    ";
    
    if ($modus == "Spieltag") {
      echo "<td align=\"left\"  onclick=\"myFunction($i1)\"><img src=\"images/Vereine/$team_aus_nr[$i].gif\" height=\"22\"></td>
      ";
    }

    echo "<td align=\"left\"  onclick=\"myFunction($i1)\"><b>$team_aus[$i]</b></td>
    ";

    echo "</tr>
    ";


    if (($modus == "Spieltag") || ($modus == "Tipps")){

// DAS SOLL NUR GEHEN WENN DAS SPIEL SCHON BEGONNEN HAT
// TROTZDEM ÜBERSICHT DER ANDEREN TIPPS ?
// FUNKTION WER HAT NOCH NICHT GETIPPT?

      echo "<tr id=\"$i1\" style=\"display: none;\">";

      echo "<td colspan = \"5\">
            <table align = \"center\" bgcolor =\"green\" width = \"75%\" padding-left = \"10em\">";


      list ($user_nr, $user_name, $tipp, $vorname, $nachname) = get_other_tipps($real_spieltag, $real_sp_nr[$i],$modus);

      foreach ($user_nr as $nr){
        echo "<tr><td align=\"right\" width=\"33%\">".$user_name[$nr]." ". $nachname[$nr][-1] ."</td><td align=\"center\" width=\"33%\">".$tipp[$nr]."</td><td width=\"33%\"></td></tr>
        ";
      }

      echo "</table></td>";
      echo "</tr>";

    }


  }




  echo "</table><br>";
  if (($modus != "Spieltag") && (get_usernr() != "")){
    echo "<input type=\"hidden\" value =\"$real_spieltag\" name=\"spieltag\">
          <input type=\"Submit\" value=\"Enter\"></form>";
  }






  if ((!$change) && (get_usernr() != "")) {
    echo "<form method = \"POST\" action =\"\">
    <input type=\"hidden\" value =\"$real_spieltag\" name=\"spieltag\">
    <input type=\"hidden\" value =\"1\" name=\"change\">
    <input type = \"Submit\" value = \"$modus &auml;ndern\"></form>";
  }

}

?>


<script>
function myFunction(wert) {
   if ( document.getElementById(wert).style.display==""){
      document.getElementById(wert).style.display = "none";
   } else {
      
      for (var i=100;i<=108; i++){
            document.getElementById(i).style.display = "none";

      }
            document.getElementById(wert).style.display = "";

   }
}

</script>

