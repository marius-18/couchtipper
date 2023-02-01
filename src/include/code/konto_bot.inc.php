<div class="alert alert-info">
Du besitzt einen Telegram-Account und m&ouml;chtest keine Spiele mehr vergessen zu tippen? 
Dann kannst du hier deinen Account verkn&uuml;pfen und erh&auml;ltst zuk&uuml;nftig Benachrichtigungen vor anstehenden Spielen.
</div>
<?php
require_once("../auth/include/bot.inc.php");


list($chat_id, $notify_game, $notify_gameday, $silent) = bot_liste();

  
echo "
<table class=\"table table-md table-striped  table-hover text-center center\"> 
<thead class=\"thead-dark\">
<tr> <th></th> 
<th>Spieltag <i class=\"fas fa-info-circle\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Erhalte eine Benachrichtigung, wenn ein neuer Spieltag beginnt\"></i></th> 
<th>Spiel    <i class=\"fas fa-info-circle\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Erhalte eine Benachrichtigung, wenn das n&auml;chste Spiel noch nicht getippt wurde\"></i></th>
<th>Stumm    <i class=\"fas fa-info-circle\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Schaltet die Benachrichtigungen stumm.\"></i></th> 
<th><i class=\"fas fa-info-circle\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Sendet eine Testnachricht an das entsprechende Ger&auml;t.\"></i></th>
</tr></thead>";

$i = 1;
foreach ($chat_id as $bot){
    if ($bot == "-123"){
        $bot = "Neues Gerät angefragt.";
        continue;
    }
    
    echo "<tr>";        
    echo "<td class=\"text-nowrap\">Ger&auml;t $i"; ## Vllt zulassen dass der Name geändert wird? <i class=\"far fa-edit\"></i></td>";

    if ($notify_gameday[$bot]){
        echo "<td><input type=\"checkbox\" id=\"".$bot."gameday\" onchange=\"toggle_bot($bot,'gameday')\" checked></td>";
    }  else {
        echo "<td><input type=\"checkbox\" id=\"".$bot."gameday\" onchange=\"toggle_bot($bot,'gameday')\"  ></td>";
    }
    
    if ($notify_game[$bot]){
        echo "<td><input type=\"checkbox\" id=\"".$bot."game\" onchange=\"toggle_bot($bot,'game')\" checked></td>";
    }  else {
        echo "<td><input type=\"checkbox\" id=\"".$bot."game\" onchange=\"toggle_bot($bot,'game')\"></td>";
    }

    if ($silent[$bot]){
        echo "<td><span id=\"silent$bot\"  onclick=\"toggle_bot($bot, 'unmute')\" style=\"color:red\"><i class=\"fas fa-bell-slash\"></i> </span></td>";
    }  else {
        echo "<td><span id=\"silent$bot\"  onclick=\"toggle_bot($bot, 'mute')\"> <i class=\"fas fa-bell\"></i> </span></td>";
    }
    
    echo "<td><i class=\"fas fa-search fa-lg\" onclick=\"identify_bot($bot)\"></i>";
    echo "</tr>";
    $i++;
}

echo "</table>";

echo "<div class=\"alert alert-success\" id=\"bot_infobox\" style=\"display:none\">Stummschaltung aktiviert!</div>";


### FEHLT NOCH: DELET BOT.. dabei vllt auch ne Nachricht an den Bot.. ? und halt einfach aus der DB rauslöschen..

### Registrierung für ein neues Gerät! 

if (!user_has_hello_id(get_usernr())){
    ### Noch wurde kein Request gestellt.. Also Button anzeigen zum Erstellen
    echo "<button type=\"button\" class=\"btn btn-info\" onclick = \"register_bot()\">Neues Gerät hinzufügen!</button>";
} else {
    ### Eine Anfrage wurde schon erstellt.. Jetzt fehlt nur der Link zu Telegram um sich da anzumelden..
    $hello_id = get_hello_id(get_usernr());
    echo "<div class=\"alert alert-success\">
            Die Vorbereitungen sind abgeschlossen!<br>
            Du musst nur noch auf dem entsprechenden Gerät auf folgenden Link Klicken!
          </div>";
    echo "<button type=\"button\" class=\"btn btn-success\" onclick = \"window.location.href='https://t.me/Couchtipper_bot?start=$hello_id'\">Zu Telegram wechseln!</button>";
}


?>

<br>
