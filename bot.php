<?php
require_once("../auth/include/bot.inc.php");

?>


<?php
$bot_id = get_bot_id();

$json_out = json_decode(file_get_contents('php://input'), true);
$chat_id = $json_out['message']['chat']['id'];
$first_name = trim(str_replace('?', '', preg_replace('/[^A-Za-z0-9 ]/', '', $json_out['message']['chat']['first_name'])));
$type = $json_out['message']['chat']['type'];
$message = $json_out['message']['text'];
$message_id = $json_out['message']['message_id'];


// Generell immer prüfen ob man schon registriert ist!!!

//speichert die letzte Nachricht in die DB..
save_message($chat_id, $message);

if(stripos($message, '/start') === 0 && $type == 'private')
{
    // checken ob User schon für Bot registriert
    // wenn ja: mach nichts und sage "schon registriert
    // wenn nein: prüfe ob die ID dabei ist und gültig ist
    // dann registrieren!
    
    $code = substr($message,7);

    
    if (bot_is_registered($chat_id)){
        // Wir sind schon registriert..
        $sent = true;
        sendMessage($bot_id,$chat_id,false,"Gude ".$first_name."! Du bist schon im System registriert! \nSende /help um alle Befehle angezeigt zu bekommen!");
    } else {
        if(bot_check_hello_id($code)){
            // ID stimmt überein, ==> Checke ein, damit die CHat_id im System ist
            $check = bot_check_in($code, $chat_id);
            
            if ($check){
                // Einchecken hat geklappt! :)
                $sent = true;
                sendMessage($bot_id,$chat_id,false,"Gude ".$first_name."! Du wurdest erfolgreich registriert! \u{1F973} \nSende /help um alle Befehle angezeigt zu bekommen!");
            } else {
                // Es gab einen Fehler beim einchecken.. 
                $sent = true;
                sendMessage($bot_id,$chat_id,false,"Gude ".$first_name."! Bei der Registrierung ist ein Fehler passiert. Versuche es nochmal..");
            }
        } else {
            // Der Hello-Code stimmt nicht überein.. Du wirst abgewiesen..
            $sent = true;
            sendMessage($bot_id,$chat_id,false,"Gude ".$first_name."! Dieser Code ist nicht bekannt! Für die Registrierung musst du auf couchtipper.de unter \"Mein Konto\" ein neues Gerät hinzufügen");
        }
    }
}


if (!bot_is_registered($chat_id) && !isset($sent)){
    ### fängt alles ab, was nicht angemeldet
    $sent = true;
    $msg = "Du bist nicht für den Couchtipper-Bot registriert! \u{1F974} \nMelde dich auf couchtipper.de an und füge unter \"Mein Konto\" ein neues Gerät hinzu";
    sendMessage($bot_id,$chat_id,false,$msg);
}


// Ab hier kann man Befehle abfangen.. z.B. meine Tipps / Ergebnisse / meine Punkte / Rangliste / Tagessieger

if(strpos(strtolower($message), 'help') !== false && !isset($sent))
{
	$sent = true;
	sendMessage($bot_id,$chat_id,false,"Du suchst Hilfe? Hier gibt es aber noch keine.. \u{1F602}");
}



if(stripos($message, '/befehl') === 0 && !isset($sent))
{
	$sent = true;
	sendMessage($bot_id,$chat_id,false,'Du hast <b>/befehl</b> verwendet. Das wäre die Antwort auf diesen Befehl. Ach übrigens, kannst du ganz einfach HTML-Tags verwenden, um beispielsweise den Text <b>fett</b> oder <i>kursiv</i> zu schreiben.');
}

if(stripos($message, '/würfel') === 0 || stripos($message, '/wuerfel') === 0 && !isset($sent))
{
	$sent = true;
	file_get_contents('https://api.telegram.org/bot'.$bot_id.'/senddice?chat_id='.$chat_id);
}

if(strpos(strtolower($message), 'whatsapp') !== false && !isset($sent))
{
	$sent = true;
	sendMessage($bot_id,$chat_id,false,'Habe ich da etwa WhatsApp gehört? Telegram > WhatsApp.');
}

if(strpos(strtolower($message), 'chatid') !== false && !isset($sent))
{
	$sent = true;
	sendMessage($bot_id,$chat_id,false,'Deine Chat-ID lautet: <b>'.$chat_id.'</b>');
}

$array = array('eins','zwei','drei');
if(in_array(strtolower($message), $array) AND !isset($sent))
{
	$sent = true;
	sendMessage($bot_id,$chat_id,false,'Wenn eines dieser Wörter in der Nachricht an den Bot vorkommt, erscheint diese Antwort.');
}

if(!isset($sent) && $type == 'private')
{
    //test($message, $chat_id);
	sendMessage($bot_id,$chat_id,false,'Sorry, aber das habe ich nicht ganz verstanden. '.$message.'');
}

?>
