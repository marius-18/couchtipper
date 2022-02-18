<br>
<!--
<div class="alert alert-success">
<span class="badge badge-pill badge-danger">NEW!</span> Die &Uuml;bersicht der Tagessieger ist jetzt verf&uuml;gbar. Einfach im Men&uuml; unter "Tagessieger" oder <a href="?index=12#main">hier</a> klicken!
</div>
-->

<?php
include_once("src/include/code/gewinn.inc.php");
echo "<div class=\"container\">";

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
                    

                    
function gewinner(){
    
    echo "<div class=\"alert alert-success\">
            <span class=\"badge badge-pill badge-danger\">Die R&uuml;ckrunde ist abgeschlossen! </span> 
            <br>Hier seht ihr die Verteilung der Gewinner 
            <br>(erstmal ohne Gew&auml;hr, weil sich das Couchtipper-Team nicht sicher ist, ob das so klappt.. <i class=\"far fa-laugh-squint\"></i>)
        </div>";
        
    print_gesamt_gewinner(get_curr_wett());


}



if (!spieltag_running(34) && akt_spieltag() == 34){
           gewinner();
}   


leader();

                    
?>

<br>




<div class="alert alert-secondary"><h5>Sch&ouml;n, dass du (wieder) bei unserer Tipp-Gruppe dabei bist. <i class="far fa-smile"></i>
<br><br>
Bei Fragen zum Tippspiel, schau mal auf der Seite <a href="?index=11#main">"FAQ"</a> nach. 
<br><br>
<div class="alert alert-success">
<span class="badge badge-pill badge-danger">NEW!</span> Ab dieser Saison gibt es eine zusätzliche Möglichkeit, etwas zu gewinnen!<br><br>
An jeden Spieltagssieger werden zusätzlich 5€ ausgezahlt! <br>
Mehr dazu unter <a href="?index=11#main">"FAQ"</a>.

</div>
<br>
Viel Spa&szlig; beim Tippen! </h5></div>



</div>


<?php //leader() ?>
