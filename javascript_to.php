<?php
session_start();

### Hiermit kann von javacript (ajax) auf php skripts zugegriffen werden (über POST)

/*
Beispiel funktion

<script>
function xyz(PARAMETER)
{
var result = "default";
result = $.ajax({
    type: 'POST',
    async: false,   // WICHTIG! 
    url: 'javascript_to.php',
    data: ({
        identifier: PARAMETER
    })
}).responseText;
 
alert(result);
}
</script>

*/

// ACHTUNG.... DAS IST PRINZIPIELL GEFÄHRLICH.. HIER KÖNNTE MAN ANGREIFEN... GENERELL BEI POST, weil der User alles eingeben kann

require_once("../auth/include/bot.inc.php"); 
require_once("../auth/include/security.inc.php");

if (isset($_POST['bot_identifier'])){
 
    $bot = $_POST['bot_identifier'];
    identify_bot($bot, "Das ist eine Testnachricht!");
}

if (isset($_POST['bot_register'])){
    $hello_id = request_bot(get_usernr());
}

if (isset($_POST['toggle_bot_mode'])){

    $chat_id = $_POST['chat_id'];
    $mode = $_POST['mode'];
    $bool = $_POST['bool'];
    update_notify_mode($chat_id, $mode, $bool);

}
?>
