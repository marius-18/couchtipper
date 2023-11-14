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
        $max = 72;
    }
    if (get_wettbewerb_code(get_curr_wett())  == "EM"){
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
    
}

?>
