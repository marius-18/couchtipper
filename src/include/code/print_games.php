<?php



// HIER FEHLT DIE ÜBERSICHTVON ANDEREN TIPPS

// WENN NICHT ANGEMELDET TIPPS LEER


//\\Æ
// achtung png statt gif //BITTE DIREKT IN DIE DB


//require_once("src/get_tipps.php");

function print_games($args, $modus, $change){

    if (get_wettbewerb_code(get_curr_wett()) == "BuLi"){
        $endung = "gif";
    } else {
        $endung = "png";
    }
    list ($datum, $team_heim, $team_aus, $tore_heim, $tore_aus, $team_heim_nr, 
            $team_aus_nr, $real_sp_nr, $real_spieltag, $anz_spiele) = $args;

    $zufall = 100 + $anz_spiele -1 ;
  // das muss ausgelagert werden
    echo "
    <script>
    function myFunction(wert) {
      if ( document.getElementById(wert).style.display==\"\"){
        document.getElementById(wert).style.display = \"none\";
      } else {
        for (var i=100;i<= $zufall; i++){
            document.getElementById(i).style.display = \"none\";
        }
        document.getElementById(wert).style.display = \"\";
      }
    }
    </script>";

        if ($datum[0] == 0) {
            $tipp_error_msg = "Dieser Spieltag ist noch <strong>nicht</strong> terminiert worden.";
            
            if ($modus == "Tipps") {
                $tipp_error_msg .= "<br>Du kannst deshalb noch <strong>nicht</strong> tippen!</p>";
            }
            
            echo "<div class=\"alert alert-danger\"> $tipp_error_msg </div>";
        }
        

    // Starte mit dem Formular
    echo "<div class=\"form-group\"> 
          <form method=\"POST\" action =\"\">";

    echo "<div class=\"table-responsive-sm\"><table class=\"table table-sm table-striped table-hover\">";

    //echo "<tr>";
   // echo "<td colspan=\"5\" class=\"table-info\">"; // FARBE muss auch AUS DB
  

    //} else {
    //    echo stamp_to_date($datum[0]);
    //}  

    //echo "</td></tr>";

  
    $help = "";//$datum[0];// Wozu ?!


    for ($i = 0; $i < $anz_spiele; $i++){

        $i1 = $i + 100 ;

        if ($help != $datum[$i]){    // fuegt Datums-Zeile ein
            echo "<tr class=\"thead-dark\"><th colspan=\"5\" >";
            echo stamp_to_date($datum[$i]);
            echo "</th></tr>";

            $help = $datum[$i];
        }
    
        echo "<tr>";

        echo "<td class=\"align-middle\" align=\"right\"  onclick=\"myFunction($i1)\"><b>$team_heim[$i]</b></td>";
    
        if (($modus == "Spieltag") && ($team_heim_nr[$i] <= 32) ) {
            echo "<td align=\"right\"  onclick=\"myFunction($i1)\"><img src=\"images/Vereine/$team_heim_nr[$i].$endung\" width=\"30\"></td>";
        }
    
        echo "<td align=\"center\" class=\"text-nowrap\"><b>$tore_heim[$i] : $tore_aus[$i]</b></td>";
    
        if (($modus == "Spieltag") && ($team_aus_nr[$i] <= 32)) {
            echo "<td align=\"left\"  onclick=\"myFunction($i1)\"><img src=\"images/Vereine/$team_aus_nr[$i].$endung\" width=\"30\"></td>";
        }

        echo "<td class=\"align-middle\" align=\"left\"  onclick=\"myFunction($i1)\"><b>$team_aus[$i]</b></td>";

        echo "</tr>";
        

        if (($modus == "Spieltag") || ($modus == "Tipps")){

            // DAS SOLL NUR GEHEN WENN DAS SPIEL SCHON BEGONNEN HAT
            // TROTZDEM ÜBERSICHT DER ANDEREN TIPPS ?
            // FUNKTION WER HAT NOCH NICHT GETIPPT?
            
            // Das sollte migriert werden

            echo "<tr id=\"$i1\" style=\"display: none;\">";

            echo "<td class=\"center\" colspan = \"5\"  style=\"text-align:center\">";
            
            if ($modus == "Spieltag"){
                
                // GET_TORE
                echo "<div class=\"container\"  style=\"margin-bottom:5px\">";
                echo get_tore($real_spieltag, $real_sp_nr[$i],$modus);
                echo "</div>";
            }

            echo "<table class=\"table\" align = \"center\" width = \"75%\" padding-left = \"10em\">";

            list ($user_nr, $user_name, $tipp, $vorname, $nachname, $punkte) = get_other_tipps($real_spieltag, $real_sp_nr[$i],$modus);

            foreach ($user_nr as $nr){
                if ($nr == get_usernr()){
                    $active = "class = \"table-success\"";
                } else{
                    $active = "";
                }
                
                echo "<tr $active>
                <td align=\"right\" width=\"33%\">".$user_name[$nr]." ". $nachname[$nr][-1] ."</td>
                <td align=\"center\" width=\"33%\">".$tipp[$nr]."</td>
                <td width=\"33%\"><span class=\"badge badge-pill badge-success\">".$punkte[$nr]."</span></td>
                </tr>
                        ";
            }

            echo "</table></td>";
            echo "</tr>";

        }


    }

    echo "</table></div><br>";
    
    
    
    if (($modus != "Spieltag") && (get_usernr() != "")){
    
        echo "<input type=\"hidden\" value =\"$real_spieltag\" name=\"spieltag\">
              <input type=\"Submit\" class=\"btn btn-success\" value=\"Enter\"></form><br>";
    }


    if ((!$change) && (get_usernr() != "")) {
        echo "<form method = \"POST\" action =\"\">
                <input type=\"hidden\" value =\"$real_spieltag\" name=\"spieltag\">
                <input type=\"hidden\" value =\"1\" name=\"change\">
                <input type = \"Submit\"  class=\"btn btn-primary\" value = \"$modus &auml;ndern\"></form><br>";
    }
 
 echo "</div>";
}

?>




