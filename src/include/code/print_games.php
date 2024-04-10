<?php


function print_games($args, $modus, $change){

    if (get_wettbewerb_code(get_curr_wett()) == "BuLi"){
        $endung = "gif";
    } else {
        $endung = "png";
    }
    list ($datum, $team_heim, $team_aus, $tore_heim, $tore_aus, $team_heim_nr, 
            $team_aus_nr, $real_sp_nr, $real_spieltag, $anz_spiele) = $args;
    
    $zeitraum = get_zeitraum_of_all_spt(); 
    
    if ($datum[0] == 0) {
        ## Wenn noch kein Datum eingetragen wurde
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
    
    
    $help = "";
    $alle_tore = get_tore($real_spieltag, $modus);
    list($other_tipps_args, $punkte) = get_other_tipps($real_spieltag,$modus);
    
    
    if (!is_big_tournament(get_curr_wett())) {
        $img_folder = "Vereine"; 
    } elseif (get_wettbewerb_code(get_curr_wett()) == "EM")  {
        $img_folder = "Nations/EM";   
    } else {
        $img_folder = "Nations/WM";           
    }
    
    for ($i = 0; $i < $anz_spiele; $i++){
        $visible_index = $i + 100 ;

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
        
        ## Heim Team Name
        echo "<td class=\"align-middle\" align=\"right\"  onclick=\"game_details_ausblenden($visible_index, $anz_spiele,'')\"><b>$team_heim[$i]</b></td>";
    
        if (($modus == "Spieltag") && ($team_heim_nr[$i] <= 32) ) {
            ## Heim Team Logo
            echo "<td  class=\"align-middle\" align=\"right\"  onclick=\"game_details_ausblenden($visible_index, $anz_spiele,'')\">
                  <img src=\"images/$img_folder/$team_heim_nr[$i].$endung\" width=\"30\"></td>";
        }
        
        ## Ergebnis
        echo "<td align=\"center\" class=\"align-middle text-nowrap\">$tipp_anzeiger_start<b>$tore_heim[$i] : $tore_aus[$i]</b>$tipp_anzeiger_ende</td>";
    
        if (($modus == "Spieltag") && ($team_aus_nr[$i] <= 32)) {
            ## Auswärts Team Logo
            echo "<td  class=\"align-middle\" align=\"left\"  onclick=\"game_details_ausblenden($visible_index, $anz_spiele,'')\">
                  <img src=\"images/$img_folder/$team_aus_nr[$i].$endung\" width=\"30\"></td>";
        }
        
        ## Auswärts Team Name
        echo "<td class=\"align-middle\" align=\"left\"  onclick=\"game_details_ausblenden($visible_index, $anz_spiele,'')\"><b>$team_aus[$i]</b></td>";
        
        
        echo "</tr>";
        
        if (($modus == "Spieltag") || ($modus == "Tipps")){
            ## Zeige die Details des Spiels an
            print_game_details($modus, $i, $real_sp_nr, $punkte, $alle_tore, $visible_index, $other_tipps_args, '');
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


function print_gruppe($gruppe){
    ## Print Big Tournament Group Games

    $args = get_group_games($gruppe);
    list ($datum, $team_heim, $team_aus, $tore_heim, $tore_aus, $real_sp_nr) = $args;

    
    echo "<div class=\"table-responsive-sm\">";
    echo "<table class=\"table table-striped table-sm  table-hover text-center center\">";

    $x = 1;
    ## TODO: transform get all tips, alles direkt aus der DB ziehen!
    
    list($other_tipps_args, $punkte) = get_precompute_tipps();

    foreach ($real_sp_nr AS $i){
        $visible_index = $x + 100;
        echo "<tr onclick=\"game_details_ausblenden($visible_index, 9, '$gruppe')\">";

        echo "<td align = \"right\"> $team_heim[$i] </td> 
              <td align = \"center\"> $tore_heim[$i]:$tore_aus[$i] </td> 
              <td align =\"left\"> $team_aus[$i] </td> 
              <td  style=\"border-left: 3px solid #AAAAAA; padding-left: 1px;\">".stamp_to_date_gruppe($datum[$i])."</td>";
        
        echo "</tr>";
        
        list($my_spnr, $my_spieltag) = explode("-", $i);
        
        $alle_tore = array();#get_tore($my_spieltag, "Spieltag");
        
        $identity = [0,1,2,3,4,5,6,7,8,9,10,11,12,13];
        $my_args = array("Spieltag", $my_spnr, $identity, $punkte, $alle_tore, $visible_index, $other_tipps_args, $gruppe);
        
        print_all_game_details($my_args, $my_spieltag);
        
        $x++;
    }

    
    echo "</table>";
    echo "</div>";
}



function print_tore($alle_tore, $real_sp_nr, $sp_nr){
    ## Print Tore for respective game
    if (isset($alle_tore[$real_sp_nr[$sp_nr]])){
        echo "<div class=\"container\" style=\"margin-bottom:5px\">";
        echo $alle_tore[$real_sp_nr[$sp_nr]];
        echo "</div>";
    }
}



function print_game_details($modus, $sp_nr, $real_sp_nr, $punkte, $alle_tore, $visible_index, $args, $prefix){
    ## TODO: Ändern, dass das nach Spielbeginn direkt angezeigt wird 
    list($user_nr, $user_name, $tipp, $vorname, $nachname) = $args;

    echo "<tr id=\"$prefix$visible_index\" style=\"display: none;\">";
    
    echo "<td class=\"center\" colspan = \"5\"  style=\"text-align:center\">";

    if ($modus == "Spieltag"){
        print_tore($alle_tore, $real_sp_nr, $sp_nr);     
    }
    
    echo "<table class=\"table\" align = \"center\" width = \"75%\" padding-left = \"10em\">";
    if (isset($user_nr[$real_sp_nr[$sp_nr]])){
        ## Wenn der Spieltag noch nicht begonnen hat, werden die Tipps noch nicht angezeigt (dann Fehler in foreach)
        foreach ($user_nr[$real_sp_nr[$sp_nr]] as $nr){
            if ($nr == get_usernr()){
                $active = "class = \"table-success\"";
            } else{
                $active = "";
            }
            
            if (isset($punkte[$real_sp_nr[$sp_nr]][$nr])){
                $mypoints = $punkte[$real_sp_nr[$sp_nr]][$nr];
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
                    <td align=\"right\" width=\"33%\">".$user_name[$real_sp_nr[$sp_nr]][$nr]." ". $nachname[$real_sp_nr[$sp_nr]][$nr] ."</td>
                    <td align=\"center\" width=\"33%\">".$tipp[$real_sp_nr[$sp_nr]][$nr]."</td>
                    <td width=\"33%\"><span class=\"badge badge-pill $badgecolor\">".$mypoints."</span></td>
                 </tr>";
        }
    }
            
    echo "</table>";
    echo "</td>";
    echo "</tr>";   
    
}


function print_all_game_details($args, $my_spieltag){
    ## Extract all args
    list ($modus, $my_spnr, $identity, $punkte, $alle_tore, $visible_index, $other_tipps_args, $gruppe) = $args;
    list ($user_nr, $user_name, $tipp, $vorname, $nachname) = $other_tipps_args;
    
    ## Set args to respective matchday
    $other_tipps_args = array($user_nr[$my_spieltag], $user_name[$my_spieltag], $tipp[$my_spieltag], $vorname[$my_spieltag], $nachname[$my_spieltag]);
    
    ## Print the details of the game
    print_game_details($modus, $my_spnr, $identity, $punkte[$my_spieltag], $alle_tore, $visible_index, $other_tipps_args, $gruppe);
}




?>




