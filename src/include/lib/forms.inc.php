<?php


function select_spieltag ($spieltag) { // ACHTUNG WM EDITION
    if (get_wettbewerb_code(get_curr_wett()) == "BuLi"){
        $max_spieltage = 34;
    } elseif (get_wettbewerb_code(get_curr_wett()) == "WM"){
        $max_spieltage = 25;
    }
    else {
        $max_spieltage = 22; // Aus der datenbank!!!
    }
    
    $spieltag = min($spieltag, $max_spieltage);
    $aktueller_spieltag = spt_select();

    if ($spieltag > 1){
        $left = $spieltag-1;
    } else {
        $left = 1;
    }
    
    if ($spieltag < $max_spieltage){
        $right = $spieltag+1;
    } else {
        $right = $max_spieltage;
    }
        
   echo "
        <div class=\"form-group\">
            <table class=\"table table-borderless\">
                <tr>
                    <td>
                        <form method=\"post\">
                        <input type=\"hidden\" name=\"spieltag\" value=\"$left\"></input>
                        <button type=\"submit\" class=\"btn btn-link\"><i class=\"fas fa-arrow-left fa-lg\"></i></button>
                        </form>
                    </td>
            
                    <td>                
                        <form method=\"post\">  
                            <select class=\"form-control\" name=\"spieltag\" onchange=\"this.form.submit()\" id=\"selspt\">

        ";
        
    $select = "";

    for ($i = 1; $i <= $max_spieltage; $i++){

        if ($spieltag == $i){
            $select = "selected";
        }

        if ($aktueller_spieltag == $i){
            $select .= " style=\"font-weight:bold\"";
        }
        
        if (get_wettbewerb_code(get_curr_wett()) == "EM"){
        
            // Das ist nur für das KO-System
            if ($i == 14){
                echo "  <option value=\"$i\" $select>Achtelfinale 1,2</option>
                        ";
            } elseif ($i == 15){
                echo "  <option value=\"$i\" $select>Achtelfinale 3,4</option>
                        ";
            } elseif ($i == 16){
                echo "  <option value=\"$i\" $select>Achtelfinale 5,6</option>
                        ";
            } elseif ($i == 17){
                echo "  <option value=\"$i\" $select>Achtelfinale 7,8</option>
                        ";
            } elseif ($i == 18){
                echo "  <option value=\"$i\" $select>Viertelfinale 1,2</option>
                        ";
            } elseif ($i == 19){
                echo "  <option value=\"$i\" $select>Viertelfinale 3,4</option>
                        ";
            } elseif ($i == 20){
                echo "  <option value=\"$i\" $select>Halbfinale 1</option>
                        ";
            } elseif ($i == 21){
                echo "  <option value=\"$i\" $select>Halbfinale 2</option>
                        ";
            } elseif ($i == 22){
                echo "  <option value=\"$i\" $select>Finale</option>
                        ";
            }  else{
                echo "  <option value=\"$i\" $select>$i. Spieltag</option>
                        ";
            }
            
            
            
         }  elseif (get_wettbewerb_code(get_curr_wett()) == "WM"){
        
            // Das ist nur für das KO-System
            if ($i == 16){
                echo "  <option value=\"$i\" $select>Achtelfinale 1,2</option>
                        ";
            } elseif ($i == 17){
                echo "  <option value=\"$i\" $select>Achtelfinale 3,4</option>
                        ";
            } elseif ($i == 18){
                echo "  <option value=\"$i\" $select>Achtelfinale 5,6</option>
                        ";
            } elseif ($i == 19){
                echo "  <option value=\"$i\" $select>Achtelfinale 7,8</option>
                        ";
            } elseif ($i == 20){
                echo "  <option value=\"$i\" $select>Viertelfinale 1,2</option>
                        ";
            } elseif ($i == 21){
                echo "  <option value=\"$i\" $select>Viertelfinale 3,4</option>
                        ";
            } elseif ($i == 22){
                echo "  <option value=\"$i\" $select>Halbfinale 1</option>
                        ";
            } elseif ($i == 23){
                echo "  <option value=\"$i\" $select>Halbfinale 2</option>
                        ";
            } elseif ($i == 24){
                echo "  <option value=\"$i\" $select>Spiel um Platz 3</option>
                        ";
            } elseif ($i == 25){
                echo "  <option value=\"$i\" $select>Finale</option>
                        ";
            }  else{
                echo "  <option value=\"$i\" $select>$i. Spieltag</option>
                        ";
            }
            
            
            
         } else{
                echo "  <option value=\"$i\" $select>$i. Spieltag</option>
                        ";
                }
        
        $select = "";
    }

    echo "
                            </select>
                        </form>
                    </td>
                    <td>
                        <form method=\"post\">
                            <input type=\"hidden\" name=\"spieltag\" value=\"$right\"></input>
                            <button type=\"submit\" class=\"btn btn-link\"><i class=\"fas fa-arrow-right fa-lg\"></i></button>
                        </form>                    
                    </td>
                </tr>
            </table>
        </div>
    ";

}






function select_team(){
    global $g_pdo;

    $my_team = get_fav_team(); #my_team(); // get_my favorite
    if (isset($_POST['team'])){
        $my_team = $_POST['team'];
    }
    
    echo "<form method=\"post\"><select name=\"team\" class=\"form-control\" onchange=\"this.form.submit()\">";
    
    if ($my_team == ""){
        // Falls noch kein Lieblingsteam gewählt wurde
        echo "<option value=\"0\" $selected>W&auml;hle ein Team aus!</option>";
    }
    
    $sql = "SELECT DISTINCT team_nr, team_name FROM `Teams`, Spieltage WHERE team_nr = team1 ORDER BY `Teams`.`team_name` ASC";
    foreach ($g_pdo->query($sql) as $row) {
        $team_nr    = $row['team_nr'];
        $team_name  = $row['team_name'];

        if ($team_nr == $my_team){
            $selected = " selected";
        } else { $selected = "";}

        echo " <option value=\"$team_nr\" $selected>$team_name</option>";
    }

    echo "
        </select>
        </form>

        ";
        
    return $my_team;
}


function select_gruppe(){
    if (get_wettbewerb_code(get_curr_wett())  == "WM"){
        ## ASCII Code bis Gruppe H
        $max = 72;
    }
    if (get_wettbewerb_code(get_curr_wett())  == "EM"){
        ## ASCII Code bis Gruppe F
        $max = 70;
    }
    echo "
        <table class=\"table table-borderless\">
            <tr>
                <td>
                    <button style=\"touch-action: manipulation;\" onclick=\"changeGroupTablePrev(". $max .")\" class=\"btn btn-link\"><i class=\"fas fa-arrow-left fa-lg\"></i></button>
                </td>
        ";
    
    
    echo "
        <td>
        <ul class=\"pagination justify-content-center\">
            <li id=\"LgroupA\" class=\"page-item active big_tournament_group_menu\"><a class=\"page-link\" onclick=\"changeGroupTable('groupA')\">A</a></li>
            <li id=\"LgroupB\" class=\"page-item big_tournament_group_menu\"><a class=\"page-link\" onclick=\"changeGroupTable('groupB')\">B</a></li>
            <li id=\"LgroupC\" class=\"page-item big_tournament_group_menu\"><a class=\"page-link\" onclick=\"changeGroupTable('groupC')\">C</a></li>
            <li id=\"LgroupD\" class=\"page-item big_tournament_group_menu\"><a class=\"page-link\" onclick=\"changeGroupTable('groupD')\">D</a></li>
            <li id=\"LgroupE\" class=\"page-item big_tournament_group_menu\"><a class=\"page-link\" onclick=\"changeGroupTable('groupE')\">E</a></li>
            <li id=\"LgroupF\" class=\"page-item big_tournament_group_menu\"><a class=\"page-link\" onclick=\"changeGroupTable('groupF')\">F</a></li>";
            if (get_wettbewerb_code(get_curr_wett())  == "WM"){
                echo " <li id=\"LgroupG\" class=\"page-item big_tournament_group_menu\"><a class=\"page-link\" onclick=\"changeGroupTable('groupG')\">G</a></li>";
                echo " <li id=\"LgroupH\" class=\"page-item big_tournament_group_menu\"><a class=\"page-link\" onclick=\"changeGroupTable('groupH')\">H</a></li>";
            }
            echo "
        </ul>   
        </td>
    ";

    
    echo "
                <td>
                    <button style=\"touch-action: manipulation;\" onclick=\"changeGroupTableNext(". $max .")\" class=\"btn btn-link\"><i class=\"fas fa-arrow-right fa-lg\"></i></button>
                    <input type=\"hidden\" id=\"curr_group\" value=\"A\"></input>
                </td>
            </tr>
        </table>
    ";

    ## Mit Pfeiltasten durch Gruppen navigieren

    echo "<script>
    document.addEventListener('keydown', function(event) {
    switch (event.keyCode) {
        case 37:
            changeGroupTablePrev(". $max .");
            break;
        case 38:
            //Up function
            break;
        case 39:
            //Right function
            changeGroupTableNext(". $max .");
            break;
    }});
    </script>";
    
}



function select_season($selected_seasons){
    
    if (isset($_POST['seasons_speicherung'])){
        $seasons = explode(" ", $_POST['seasons_speicherung']);
    } else {
        $seasons = $selected_seasons;   
    }


    list ($code, $jahr) = get_all_wettbewerbe();

    $checked_wetts = "";
    $all_wetts = "";
    $all_buli = "";
    $all_tour = "";
    $saison_buttons = "";
    foreach ($code as $id => $wett){
    
        if (in_array($id, $seasons)){
            $checked = "btn-success";
            $check_val = $id;
            $checked_wetts .= "$id ";

        } else {
            $checked = "btn-outline-secondary";  
            $check_val = "";
        }
    
        if ($wett == "BuLi"){
            $name = $jahr[$id];
            $all_buli .= $id.", ";
        } else {
            $name = $wett . "" . $jahr[$id];
            $all_tour .= $id.", ";
        }
        $all_wetts .= $id.", ";
 
        $saison_buttons .= "
            <div class=\"form-check form-check-inline\">
                <label class=\"btn $checked\" id=\"gesamt_button_".$id."\" for=\"btn-check-outlined\" onclick=\"seasons_toggle_button('".$id."')\">".$name."</label>    
            </div>";
 
    }

    $all_wetts = "[" . substr_replace($all_wetts ,"", -2) . "]";
    $all_buli = "[" . substr_replace($all_buli ,"", -2) . "]";
    $all_tour = "[" . substr_replace($all_tour ,"", -2) . "]";
    $checked_wetts = substr_replace($checked_wetts ,"", -1);


    

    $all_button = "<div class=\"form-check form-check-inline\">
                    <label class=\"btn btn-info\" for=\"btn-check-outlined\" 
                        onclick = \"seasons_all_on($all_wetts)\">Alles</label>
                   </div>";


    $buli_button = "<div class=\"form-check form-check-inline\">
                    <label class=\"btn btn-info\" for=\"btn-check-outlined\" 
                        onclick = \"seasons_all_off($all_wetts);seasons_all_on($all_buli)\">BuLi</label>
                   </div>";


    $turn_button = "<div class=\"form-check form-check-inline\">
                    <label class=\"btn btn-info\" for=\"btn-check-outlined\" 
                        onclick = \"seasons_all_off($all_wetts);seasons_all_on($all_tour)\">Turniere</label>
                   </div>";


    $nix_button = "<div class=\"form-check form-check-inline\">
                    <label class=\"btn btn-info\" for=\"btn-check-outlined\" 
                        onclick = \"seasons_all_off($all_wetts)\">Nichts</label>
                   </div>";    

 
    echo $all_button;
    echo $buli_button;
    echo $turn_button;
    echo $nix_button;
    
    echo "<br>";
    
    echo $saison_buttons;


    echo "<form action=\"\" method = \"post\">
            <input type=\"hidden\"  class=\"$checked_wetts\" id=\"seasons_speicherung\" name=\"seasons_speicherung\" value = \"$checked_wetts\">
                <button type=\"submit\" class=\"btn btn-primary\">Tabelle Berechnen</button>
          </form><br>";
    
    return $seasons;
}


function spieltag_start_ende(){
    // Übergabe für Start und Ende
    if (isset($_POST["spieltag_bereich_start"])){
        $spieltag_start = $_POST["spieltag_bereich_start"];
    } else {
        $spieltag_start = 0;
    }
    
    if (isset($_POST["spieltag_bereich_ende"])){
        $spieltag_ende =  $_POST["spieltag_bereich_ende"];
    } else {
        $spieltag_ende = 0;
    }
    
    
    // Spieltage mit Start und Endzeitpunkt
    $ret  = "";
    $ret .= "<div class=\"container\">";
    $ret .= "<form action=\"\" method=\"POST\" name=\"spieltag_bereich_form\">";
    $ret .= "<div class = \"row\">
               <div class = \"col\">
                <div class = \"form-group\">
                <label for=\"spieltag_bereich_start_sel\">Start:</label>
                <select class=\"form-control\" id=\"spieltag_bereich_start_sel\" name=\"spieltag_bereich_start\" onchange=\"disable_spieltag_bereich()\">
                    <option value=\"0\">Auswählen</option>";
                    for ($i=1;$i<=34;$i++){
                        if ($i == $spieltag_start){
                            $sel = "selected";
                        } else {
                            $sel = "";
                        }
                        $ret .= "<option value=\"$i\" $sel>$i</option>";
                    }
                $ret .= "</select>
                </div>
            </div>
        
        <div class=\"col\">
            <div class=\"form-group\">
                <label for=\"spieltag_bereich_ende_sel\">Ende:</label>
                <select class=\"form-control\" id=\"spieltag_bereich_ende_sel\" name=\"spieltag_bereich_ende\" onchange=\"this.form.submit()\">
                    <option value=\"0\">Auswählen</option>";
                    for ($i=1;$i<=34;$i++){
                        if ($i == $spieltag_ende){
                            $sel = "selected";
                        } else {
                            $sel = "";
                        }
                        $ret .= "<option value=\"$i\" id=\"spieltag_bereich_ende_opt$i\" class=\"spieltag_bereich_ende_opt\" $sel>$i</option>";
                    }
                    
                    $ret .= "</select>
                </div>
            </div>
        </div>
    </form>
    </div>";
    
    
    $ret .= "
    <script>
    function disable_spieltag_bereich(rel=0){
        var start_value = Number(document.getElementById(\"spieltag_bereich_start_sel\").value);
        
        var elem_iter = document.getElementsByClassName('spieltag_bereich_ende_opt');
        for (var i = 0; i < elem_iter.length; ++i) {
            elem_iter[i].disabled = false;
        }
        
        for (i = 1; i < start_value; i++){
            var name = \"spieltag_bereich_ende_opt\" + i;
            document.getElementById(name).disabled = true;
        }
        
        // Wenn des andere Select schon einen passenden Wert hat => Reload
        var end_value = Number(document.getElementById(\"spieltag_bereich_ende_sel\").value);
        
        if ((end_value > 0) && (rel == 0) && (end_value >= start_value) ){
            document.spieltag_bereich_form.submit();
        }
    }
    
    // Parameter 1, damit wir nicht direkt wieder reloadens
    disable_spieltag_bereich(1);
    </script>";
    
    
    return array($spieltag_start, $spieltag_ende, $ret);
    
}




function verlauf_hin_rueck(){
    ## Erstellt eine Form zum Auswählen von Hinrunde & Rückrunde für das Verlaufschart
    if (isset($_POST["verlauf_hin_rueck"])){
        $verlauf_hin_rueck = $_POST["verlauf_hin_rueck"];
    } else {
        $verlauf_hin_rueck = 0;
    }
    
    if ($verlauf_hin_rueck == 1){
        $sel1 = "selected";
    } else { $sel1 = ""; }
    
    if ($verlauf_hin_rueck == 2){
        $sel2 = "selected";
    } else { $sel2 = ""; }
    
    
    $ret  = "";
    $ret .= "<div class=\"container\">";
    $ret .= "<form action=\"\" method=\"POST\" name=\"verlauf_hin_rueck_form\">";
    
    $ret .= "<div class = \"row\">
               <div class = \"col\">
                 <div class = \"form-group\">
                   <select class=\"form-control\" id=\"verlauf_hin_rueck_sel\" name=\"verlauf_hin_rueck\" onchange=\"this.form.submit()\">
                     <option value=\"0\">Spielzeit auswählen</option>
                     <option value=\"1\" $sel1>Hinrunde</option>
                     <option value=\"2\" $sel2>R&uuml;ckrunde</option>
                   </select>
                 </div>
               </div>
             </div>";
    
    $ret .= "</form>";
    $ret .= "</div>";
    
    
    return array($verlauf_hin_rueck, $ret);
    
}
?>
