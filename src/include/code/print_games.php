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
    $zeitraum = get_zeitraum_of_all_spt();

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
            $tipp_error_msg = "Dieser Spieltag ist noch <strong>nicht</strong> terminiert worden. <br>Er findet im folgenden Zeitraum statt:<br>";
            $tipp_error_msg .= "<strong>".print_interval_not_scheduled("games",$zeitraum[$real_spieltag][0], $zeitraum[$real_spieltag][1])."</strong>";

            
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
    $alle_tore = get_tore($real_spieltag, $modus);
    list ($user_nr, $user_name, $tipp, $vorname, $nachname, $punkte) = get_other_tipps($real_spieltag,$modus);

    if (!is_big_tournament(get_curr_wett())) {
        $img_folder = "Vereine"; 
    } elseif (get_wettbewerb_code(get_curr_wett()) == "EM")  {
        $img_folder = "Nations/EM";   
    } else {
        $img_folder = "Nations/WM";           
    }
    for ($i = 0; $i < $anz_spiele; $i++){

        $i1 = $i + 100 ;

        if ($help != $datum[$i]){    // fuegt Datums-Zeile ein
            echo "<tr class=\"thead-dark\"><th colspan=\"5\" >";
            echo stamp_to_date($datum[$i]);
            echo "</th></tr>";

            $help = $datum[$i];
        }
        
        if (isset($punkte[$real_sp_nr[$i]][get_usernr()])){
            $mypoints = $punkte[$real_sp_nr[$i]][get_usernr()];
        } else {
            $mypoints = "";
        }
        
        if (($mypoints == "+3") && (($modus == "Tipps") || ($modus == "Spieltag"))){
            $tipp_anzeiger_start = "<span class=\"badge badge-pill threepoints\">";
            $tipp_anzeiger_ende  = "</span>";
        } elseif (($mypoints == "+2") && (($modus == "Tipps") || ($modus == "Spieltag"))){
            $tipp_anzeiger_start = "<span class=\"badge badge-pill twopoints\">";
            $tipp_anzeiger_ende  = "</span>";
        } elseif (($mypoints == "+1") && (($modus == "Tipps") || ($modus == "Spieltag"))){
            $tipp_anzeiger_start = "<span class=\"badge badge-pill onepoint\"><strong>";
            $tipp_anzeiger_ende  = "</strong></span>";
        } else {
            $tipp_anzeiger_start = "";
            $tipp_anzeiger_ende  = "";
        }
    
        echo "<tr class=\"align-middle\">";
        if ($modus == "Tipps"){
            #echo "<td class=\"align-middle\" align=\"left\"  onclick=\"myFunction($i1)\"><span class=\"badge badge-pill badge-success\">".$punkte[$real_sp_nr[$i]][get_usernr()]."</span></td>";
        }
        echo "<td class=\"align-middle\" align=\"right\"  onclick=\"myFunction($i1)\"><b>$team_heim[$i]</b></td>";
    
        if (($modus == "Spieltag") && ($team_heim_nr[$i] <= 32) ) {
            echo "<td  class=\"align-middle\" align=\"right\"  onclick=\"myFunction($i1)\"><img src=\"images/$img_folder/$team_heim_nr[$i].$endung\" width=\"30\"></td>";
        }
    
        echo "<td align=\"center\" class=\"align-middle text-nowrap\">$tipp_anzeiger_start<b>$tore_heim[$i] : $tore_aus[$i]</b>$tipp_anzeiger_ende</td>";
    
        if (($modus == "Spieltag") && ($team_aus_nr[$i] <= 32)) {
            echo "<td  class=\"align-middle\" align=\"left\"  onclick=\"myFunction($i1)\"><img src=\"images/$img_folder/$team_aus_nr[$i].$endung\" width=\"30\"></td>";
        }

        echo "<td class=\"align-middle\" align=\"left\"  onclick=\"myFunction($i1)\"><b>$team_aus[$i]</b>";
        

        if ($modus == "Tipps"){
            #echo "<td class=\"align-middle\" align=\"right\"  onclick=\"myFunction($i1)\"><span class=\"badge badge-pill badge-success\">".$punkte[$real_sp_nr[$i]][get_usernr()]."</span></td>";
        }
        
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
                echo $alle_tore[$real_sp_nr[$i]];
                echo "</div>";
            }

            echo "<table class=\"table\" align = \"center\" width = \"75%\" padding-left = \"10em\">";

            if (isset($user_nr[$real_sp_nr[$i]])){
                ## Wenn der Spieltag noch nicht begonnen hat, werden die Tipps noch nicht angezeigt (dann Fehler in foreach)
                foreach ($user_nr[$real_sp_nr[$i]] as $nr){
                    if ($nr == get_usernr()){
                        $active = "class = \"table-success\"";
                    } else{
                        $active = "";
                    }

                    if (isset($punkte[$real_sp_nr[$i]][$nr])){
                        $mypoints = $punkte[$real_sp_nr[$i]][$nr];
                    } else {
                        $mypoints = "";
                    }

                    if ($mypoints == "+3"){
                        $badgecolor = "threepoints_sm";
                    } elseif ($mypoints == "+2"){
                        $badgecolor = "twopoints_sm";
                    } elseif ($mypoints == "+1"){
                        $badgecolor = "onepoint_sm";
                    } else {
                        $badgecolor = "";
                    }
                    
                    echo "<tr $active>
                    <td align=\"right\" width=\"33%\">".$user_name[$real_sp_nr[$i]][$nr]." ". $nachname[$real_sp_nr[$i]][$nr] ."</td>
                    <td align=\"center\" width=\"33%\">".$tipp[$real_sp_nr[$i]][$nr]."</td>
                    <td width=\"33%\"><span class=\"badge badge-pill $badgecolor\">".$mypoints."</span></td>
                    </tr>
                            ";
                }
            }
            
            echo "</table></td>";
            echo "</td>";
            echo "</tr>";

        }


    }

    echo "</table></div>";
    
    
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
 
    
    if (($modus == "Tipps") || ($modus == "Spieltag")){
        echo "<br><div class=\"alert alert-secondary rounded\"><div class=\"row\">
          <div class=\"col-6\">Punkte Legende: </div>
          <div class=\"col-2\"><span class=\"badge badge-pill threepoints\">+3</span></div>
          <div class=\"col-2\"><span class=\"badge badge-pill twopoints\">+2</span></div>
          <div class=\"col-2\"><span class=\"badge badge-pill onepoint\">+1</span></div></div></div><br>";
    }
 
 
 echo "</div>";
}

?>




