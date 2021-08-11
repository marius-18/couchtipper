<?php


function select_spieltag ($spieltag) { // ACHTUNG WM EDITION

    if (get_wettbewerb_code() == "Buli"){
        $max_spieltage = 34;
    }
    else {
        $max_spieltage = 22; // Aus der datenbank!!!
    }

    if ($spieltag > 1){
        $left = $spieltag-1;
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
        
        if (get_wettbewerb_code() == "EM"){
        
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


?>
