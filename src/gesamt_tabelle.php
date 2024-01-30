<h2> Gesamt Tabelle:</h2>
<div class="container">

<?php
require_once('src/include/code/rangliste.inc.php');

if (isset($_POST['speicherung'])){
    $seasons = explode(" ", $_POST['speicherung']);
} else {
    $seasons = [-2,0,1,2,4,5,6];   
}


function gesamt_rangliste($seasons, $modus){
    $ges_punkte = array();
    $ges_spiele = array();
    $ges_schnitt = array();
    $ges_user = array();
    $ges_platz = array();
    
    
    foreach ($seasons as $saison){
        $bewerb = array($saison,0);
        new_db_connection($bewerb);    
        
        list ($punkte, $spiele, $akt_punkte, $schnitt, $letzte_punkte, $user, $platz1, $spieltagssieger, $spieltagssieger_last) = rangliste(0,34, "", $bewerb);
    
    
        foreach ($user as $index => $uid){
            if (!isset($ges_punkte[$uid])){
                $ges_punkte[$uid] = $punkte[$index];
            } else {
                $ges_punkte[$uid] += $punkte[$index];
            }
        
            if (!isset($ges_spiele[$uid])){
                $ges_spiele[$uid] = $spiele[$index];
            } else {
                $ges_spiele[$uid] += $spiele[$index];
            }        
        
            if ($ges_spiele[$uid] != 0) {
                $ges_schnitt[$uid] = round($ges_punkte[$uid]/$ges_spiele[$uid], 2);
            }    
            else {
                $ges_schnitt[$uid] = 0;
            }
            $ges_user[$uid] = $uid;
            
        }
    
        unset($punkte);
        unset($spiele);
        unset($schnitt);
        unset($user);
    } 
       
    
    ### SORTIEREN
    
    array_multisort($ges_punkte, SORT_DESC, $ges_spiele, SORT_ASC, $ges_schnitt, $ges_user); //SORTIERUNG HIER MIT GLEICHEN PLÄTZEN


    $platz = 1;
    foreach ($ges_user as $i => $nr){

        if (($i != 0) && ($ges_punkte[$i] == $ges_punkte[$i-1])){
            $platz_r[$nr] = $platz_halten;
            $platz_halten = $platz_r[$nr];

        } else {
            $platz_r[$nr] = $platz;
            $platz_halten = $platz;
        }

        $platz++;

    }
    
    return array($ges_punkte, $ges_spiele, $ges_schnitt, $ges_user, $platz_r);
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
            <label class=\"btn $checked\" id=\"gesamt_button_".$id."\" for=\"btn-check-outlined\" onclick=\"toggle_button('".$id."')\">".$name."</label>    
        </div>";
 
}

$all_wetts = "[" . substr_replace($all_wetts ,"", -2) . "]";
$all_buli = "[" . substr_replace($all_buli ,"", -2) . "]";
$all_tour = "[" . substr_replace($all_tour ,"", -2) . "]";
$checked_wetts = substr_replace($checked_wetts ,"", -1);



?>


<script>

    function all_on(list){
        list.forEach(button_on);
    }
    
    function all_off(list){
        list.forEach(button_off);
    }
    
    function button_on(id){
        element = document.getElementById("gesamt_button_"+id);
        text_element = document.getElementById("speicherung");
        
        element.classList.remove("btn-outline-secondary");
        element.classList.add("btn-success");      
        text_element.classList.add(id);
        text_element.value = text_element.classList;
    }

    function button_off(id){
        element = document.getElementById("gesamt_button_"+id);
        text_element = document.getElementById("speicherung");
        
        element.classList.remove("btn-success");
        element.classList.add("btn-outline-secondary");
        text_element.classList.remove(id);
        text_element.value = text_element.classList;
    }
    
    
    function toggle_button(id){
        element = document.getElementById("gesamt_button_"+id);
        
        if (element.classList.contains("btn-success")){
           // Disable!
           button_off(id);
        } else {
           //Enable!
           button_on(id);
        }
    }

</script>
<br>










<div class="form-check form-check-inline">
    <label class="btn btn-info" for="btn-check-outlined" 
    onclick = "all_on(<?php echo $all_wetts;?>)">Alles</label>
</div>
    
<div class="form-check form-check-inline">
    <label class="btn btn-info" for="btn-check-outlined" 
    onclick = "all_off(<?php echo $all_wetts;?>);all_on(<?php echo $all_buli;?>)">BuLi</label>
</div>

<div class="form-check form-check-inline">
    <label class="btn btn-info" for="btn-check-outlined" 
    onclick = "all_off(<?php echo $all_wetts;?>);all_on(<?php echo $all_tour;?>)">Turniere</label>
</div>
 
<div class="form-check form-check-inline">
    <label class="btn btn-info" for="btn-check-outlined" 
    onclick = "all_off(<?php echo $all_wetts;?>)">Nichts</label>
</div>


<br>

<?php echo $saison_buttons;?>

<form action="" method = "post">
<input type="hidden"  class="<?php echo $checked_wetts;?>" id="speicherung" name="speicherung" value = "<?php echo $checked_wetts;?>">
<button type="submit" class="btn btn-primary">Tabelle Berechnen</button>
</form>

<br>
    

<?php
list ($punkte, $spiele, $schnitt, $user, $platz) = gesamt_rangliste($seasons, "");
print_gesamt_rangliste($punkte, $spiele, $schnitt, $user, $platz);
?>


</div>




