<?php


function print_games($args, $modus, $change){

    
    list ($datum, $team_heim, $team_aus, $tore_heim, $tore_aus, $team_heim_nr, 
            $team_aus_nr, $real_sp_nr, $real_spieltag, $anz_spiele, $stadt, $stadion) = $args;
    
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
    
    ## Hole die Torschützen
    $alle_tore = get_precompute_tore($real_spieltag);
    if (empty($alle_tore)){
        ## Falls Tore noch nicht vorberechnet wurden!
        $alle_tore = get_tore($real_spieltag, $modus);
    }
    
    ## Hole andere Tipps
    list($other_tipps_args, $punkte) = get_precompute_tipps($real_spieltag);
    if (empty($other_tipps_args)){
        ## Falls Tipps noch nicht vorberechnet wurden!
        list($other_tipps_args, $punkte) = get_other_tipps($real_spieltag,$modus);
    }
    
    list($img_folder, $endung) = get_img_details();
    
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
            print_game_details($modus, $i, $real_sp_nr, $punkte, $alle_tore, $visible_index, $other_tipps_args, '', $stadt[$i], $stadion[$i], $team_heim_nr[$i], $team_aus_nr[$i]);
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
    list ($datum, $team_heim, $team_aus, $tore_heim, $tore_aus, $real_sp_nr, $stadt, $stadion) = $args;

    
    echo "<div class=\"table-responsive-sm\">";
    echo "<table class=\"table table-striped table-sm  table-hover text-center center\">";

    $x = 1;
    ## TODO: transform get all tips, alles direkt aus der DB ziehen!
    
    list($other_tipps_args, $punkte) = get_precompute_tipps();
    $tore = get_precompute_tore();

    foreach ($real_sp_nr AS $i){
        $visible_index = $x + 100;
        echo "<tr onclick=\"game_details_ausblenden($visible_index, 9, '$gruppe')\">";

        echo "<td align = \"right\"> $team_heim[$i] </td> 
              <td align = \"center\"> $tore_heim[$i]:$tore_aus[$i] </td> 
              <td align =\"left\"> $team_aus[$i] </td> 
              <td  style=\"border-left: 3px solid #AAAAAA; padding-left: 1px;\">".stamp_to_date_gruppe($datum[$i])."</td>";
        
        echo "</tr>";
        
        list($my_spnr, $my_spieltag) = explode("-", $i);
        
        if (isset($tore[$my_spieltag])){
            $alle_tore = $tore[$my_spieltag];
        } else {
            $alle_tore = array();            
        }
        
        
        ## TODO: was zum henker hab ich hier gemacht?!
        $identity = [0,1,2,3,4,5,6,7,8,9,10,11,12,13];
        $my_args = array("Spieltag", $my_spnr, $identity, $punkte, $alle_tore, $visible_index, $other_tipps_args, $gruppe, $stadt[$i], $stadion[$i]);
        
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

function print_pre_games($team_nr1, $team_nr2){
    ## Gibt die letzten Spiele des Teams zurück
    
    echo "<hr>";
    echo "<div class=\"container mb-2\">";  
    echo "<h6><b>Vergangene Spiele von ".get_team_name($team_nr1).": </b></h6>";
    print_pre_games_badges($team_nr1);
    echo "</div>";
    echo "<hr>";
    echo "<div class=\"container mt-2\">";  
    echo "<h6><b>Vergangene Spiele von ".get_team_name($team_nr2).": </b></h6>";
    print_pre_games_badges($team_nr2);
    echo "</div>";  
    
    
    echo "</div>";
}

function print_pre_games_badges($team_nr){
    
    list($img_folder, $endung) = get_img_details();
    echo "<div class=\"row\">";
    list($team1, $team2, $tore1, $tore2, $result, $gegner_id, $heimspiel, $spieltag) = get_pre_games($team_nr, akt_spieltag()-12);   
    foreach ($team1 AS $id => $team){
        
        $short_result = $team1[$id] . " " . $tore1[$id] . " - " . $tore2[$id] . " " . $team2[$id];

        echo "<div class=\"col p-1  align-middle\">";
        
        echo "<a data-html=\"true\" data-toggle=\"tooltip\" title=\"".$spieltag[$id].". Spieltag<br>$short_result\">";
        
        echo "<div class=\"badge align-middle badge-".$result[$id]."\">";
        
        if ($heimspiel[$id]){
            echo "<i class=\"fa-solid fa-house\"></i> ";
        } else {
            echo "<i class=\"fa-solid fa-plane\"></i> ";
        }
        
        echo "<img src=\"images/$img_folder/".$gegner_id[$id].".$endung\" style=\" max-width: 30px;   max-height:30px;\"> ";
        #echo "<strong class=\"align-middle h5 \">N</strong> ";
        echo "<strong class=\"align-middle h6 \">".$tore1[$id] . ":" . $tore2[$id]."</strong>";

        echo "</div>"; 
        echo "</a>";
        echo "</div>";
    }
    echo "</div>";
}



function print_game_details($modus, $sp_nr, $real_sp_nr, $punkte, $alle_tore, $visible_index, $args, $prefix, $stadt, $stadion, $team1, $team2){
    ## TODO: Ändern, dass das nach Spielbeginn direkt angezeigt wird 
    list($user_nr, $user_name, $tipp, $vorname, $nachname) = $args;

    echo "<tr id=\"$prefix$visible_index\" style=\"display: none;\">";
    
    echo "<td class=\"center\" colspan = \"5\"  style=\"text-align:center\">";
    
    echo "<div class=\"container container-fluid\"  style=\"margin-bottom:5px\">";
    
    echo "<h5><span class=\"badge badge-light p-2\"> 🏟️ $stadion</span></h5>";
    #echo "<hr>";
    echo "</div>";
    
    if (($modus == "Spieltag") || ($modus == "Tipps")){
        print_tore($alle_tore, $real_sp_nr, $sp_nr);     
    }
    
    if (($modus == "Tipps") && !isset($user_nr[$real_sp_nr[$sp_nr]])){ 
        print_pre_games($team1, $team2);   
    }
    
    if (($modus == "Tipps") && isset($user_nr[$real_sp_nr[$sp_nr]])){ 
        # TODO: Hier könnte man noch das tatsächliche Ergebnis anzeigen
        #print_r($alle_tore);
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
    list ($modus, $my_spnr, $identity, $punkte, $alle_tore, $visible_index, $other_tipps_args, $gruppe, $stadt, $stadion) = $args;
    list ($user_nr, $user_name, $tipp, $vorname, $nachname) = $other_tipps_args;
    
    ## Set args to respective matchday
    if (isset($user_nr[$my_spieltag])){
        $other_tipps_args = array($user_nr[$my_spieltag], $user_name[$my_spieltag], $tipp[$my_spieltag], $vorname[$my_spieltag], $nachname[$my_spieltag]);
    } else {
        $other_tipps_args = array([[]], [[]], [[]], [[]], [[]]);        
    }
    
    ## Print the details of the game
    if (isset($punkte[$my_spieltag])){
        print_game_details($modus, $my_spnr, $identity, $punkte[$my_spieltag], $alle_tore, $visible_index, $other_tipps_args, $gruppe, $stadt, $stadion, 0, 0);        
    } else {
        print_game_details($modus, $my_spnr, $identity, [], $alle_tore, $visible_index, $other_tipps_args, $gruppe, $stadt, $stadion, 0, 0);                
    }
}





?>




