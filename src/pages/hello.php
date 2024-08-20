<br>
<!--
<div class="alert alert-success">
<span class="badge badge-pill badge-danger">NEW!</span> Die &Uuml;bersicht der Tagessieger ist jetzt verf&uuml;gbar. Einfach im Men&uuml; unter "Tagessieger" oder <a href="?index=12#main">hier</a> klicken!
</div>
-->

<?php
include_once("src/include/code/gewinn.inc.php");

echo "<div class=\"container\">";


if ((!is_checked_in()) && (is_logged()) && (is_active_wettbewerb()) ) {  
    echo "<div class=\"alert alert-warning\"> <strong>Achtung!</strong> Du bist noch nicht für diesen Wettbewerb eingecheckt!
    <br>
    Du kannst also noch keine Tipps abgeben! <br>
    Wenn du dies ändern möchtest, klicke <a href=\"?new_check_in=1\" class=\"alert-link\">hier</a>!</div>";
}
#############################################################################'
#####   DAS MUSS AUCH NOCH BESSER WERDEN
#############################################################################
function get_podium($nr){
    global $g_pdo;
    
    $sql = "SELECT max(spieltag) as spt FROM `Ergebnisse` WHERE 1";
    
    foreach ($g_pdo->query($sql) as $row) {
        $spieltag = $row['spt'];
    }
    
    $sql = "SELECT user_nr FROM `Rangliste` WHERE spieltag = $spieltag AND platz = $nr";

    $ret = "";
    foreach ($g_pdo->query($sql) as $row) {
        $user = $row['user_nr'];
        $ret .= "".get_username_from_nr($user)."<br>";
    }
    
    if (0){
    
        $spieltag = akt_spieltag()-1;
        $sql = "SELECT user_nr FROM `Rangliste` WHERE spieltag = $spieltag AND platz = $nr";
        
        echo $sql;
        $ret = "";
        foreach ($g_pdo->query($sql) as $row) {
            $user = $row['user_nr'];
            $ret .= "".get_username_from_nr($user)."<br>";
        }
    
    }
    
    
    return $ret;

}


function leader(){

   // echo "<h4>Aktueller Stand:</h4>
   // <div align=\"center\">
   //     <div class=\"stockerl pl2\"><h2><b>2.</b></h2>".get_podium(2)."</div>
   //     <div class=\"stockerl pl1\"><h1><b>1.</b></h1>".get_podium(1)."</div>
   //     <div class=\"stockerl pl3\"><h2><b>3.</b></h2>".get_podium(12)."</div></div>";
   
   echo "<h4>Aktueller Stand:</h4>
   <table class=\"table table-borderless\" style=\"table-layout: fixed;\">
   <tr>
   <td class=\"tablestockerl\" > </td>
   <td class=\"platz1 \" rowspan=\"3\"><h1><b>1.</b></h1>".get_podium(1)." </td>   
   <td class=\"tablestockerl\"> </td>   
   </tr>
   
   <tr>
   <td class=\"platz2\" rowspan=\"2\"><h1><b>2.</b></h1>".get_podium(2)." </td>   
   <td class=\"tablestockerl\"> </td>   
   </tr>
   
   <tr> 
   <td class=\"platz3\"><h1><b>3.</b></h1>".get_podium(3)." </td>   
   </tr>
   

   
   </table>
   
   ";
   
}                    
                    

                    
function gewinner($array){
    list($id, $id_part) = $array;
    echo "<div class=\"alert alert-success\">
            <span class=\"badge badge-pill badge-danger\">Die ".get_wettbewerb_name($array)." ist abgeschlossen! </span> 
            <br>Hier seht ihr die Verteilung der Gewinner 
            <br>(erstmal ohne Gew&auml;hr, weil sich das Couchtipper-Team nicht sicher ist, ob das so klappt.. <i class=\"far fa-laugh-squint\"></i>)
        </div>";
    
    if (($id > 3) && (!is_big_tournament(get_curr_wett()))){
    print_gesamt_gewinner($array);
    } else {
        print_gewinner($array);
    }

}


if (!is_big_tournament(get_curr_wett())){
if (!spieltag_running(34) && akt_spieltag() == 34){
           gewinner(get_curr_wett());
}   

if ((!spieltag_running(17) && akt_spieltag() == 17) || (akt_spieltag() == 18)){
    ## Am 18. Spieltag (in der Rückrunde) wollen wir auch noch die Übersicht über die Hinrunden Gewinner
    list($id, $id_part) = get_curr_wett();
    gewinner(array($id,0));
}   

if ((akt_spieltag() != 1) && (akt_spieltag() != 18)){

if (get_curr_wett()[0] > 2){
    ## für die anderen alten saisons muss das noch geändert werden
    leader();
}
}
} else {
    
    if (!spieltag_running(22) && akt_spieltag() == 22){
           gewinner(get_curr_wett());
} 
  if ((akt_spieltag() != 1) ){

if (get_curr_wett()[0] > 2){
    ## für die anderen alten saisons muss das noch geändert werden
    leader();
}
}  
}

?>

<br>

<!--
<div class="alert alert-success h5">
    Bald startet die Bundesliga wieder in eine neue Saison! 
    Wer wieder beim Tippspiel dabei sein will, kann über folgenden Link beitreten:
    <br>
    <br>
    <div class="h4">
    <div class="badge badge-pill badge-success p-3"><a href="?year=8&index=" class="alert-link"> BuLi 2024/25 </a>  </div></div>
    <br>
    <strong>Hinweis:</strong> Oben im Menü kannst du zwischen den verschiedenen Wettbewerben (EM, WM, Bundesliga) wechseln.
</div>
-->


<div class="alert alert-secondary"><h5>Sch&ouml;n, dass du (wieder) bei unserer Tipp-Gruppe dabei bist. <i class="far fa-smile"></i>
<br><br>
Bei Fragen zum Tippspiel, schau mal auf der Seite <a href="?index=11#main">"FAQ"</a> nach. 
<br><br>
<!--
<div class="alert alert-success">
<span class="badge badge-pill badge-danger">NEW!</span> Die Pr&auml;mie f&uuml;r den Spieltagssieger betr&auml;gt ab sofort 6€! <br>
Mehr dazu unter <a href="?index=11#main">"FAQ"</a>.
</div>
-->
<br>
Viel Spa&szlig; beim Tippen! </h5></div>






<?php
    ## Ab hier nur noch für eingeloggte User!
    if (!is_logged()){
        echo "</div>";
        return 0;
    }
?>


<div class="alert alert-success h5">
    Wer der couchtipper WhatsApp Gruppe beitreten will, um immer auf dem neusten Stand zu sein, kann auf den folgenden Link klicken:
    <br>
    <br>
    <div class="h4"><div class="badge badge-pill badge-success p-3"><a href="https://chat.whatsapp.com/BAzcQVzSmso5q79TO0ywUN" class="alert-link"> WhatsApp <i class="fa-brands fa-whatsapp"></i></a>  </div></div>
    <br>
    <strong>Achtung!</strong> Wenn du der Gruppe beitrittst, sehen natürlich alle Mitglieder der Gruppe deine Kontaktdaten. 
</div>


</div>

<?php //leader() ?>
