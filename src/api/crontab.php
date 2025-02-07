<?php
echo "begin<br>";

### Für Spieltag und Tipps reminder
require_once("../auth/include/bot.inc.php");
require_once("src/include/code/get_games.inc.php");

### Fürs Ergebnisse aus der DB holen (get_games wird auch benutzt)
include_once("src/include/code/input_template.inc.php");
include_once("src/include/code/refresh.php");

### Spieltage Terminieren
require_once("src/include/code/spiele_terminieren.inc.php");

###########################################################
###########################################################
####### U S E R  -  B E N A C H R I C H T I G U N G E N
###########################################################
###########################################################

### Erinnert, dass ein neuer Spieltag startet
reminder_spieltag();


### Erinnert ans Tippen
reminder_tipps();

### Schickt aktuelle Ergebnisse raus
#notfiy_result();

### Weitere Ideen: Tore anzeigen.. (dazu braucht man natürlich die DB zu den aktuellen Ergebnissen..
### Tippspiel wochen ergebnisse.. vllt eigene Punkte, Tagessieger, paar Statistiken..?

###########################################################
###########################################################
####### S Y S T E M  -  U P D A T E
###########################################################
###########################################################

### Löscht Anfragen, die nach >2h nicht beantwortet wurden..
delete_hello_id();

### Ergebnisse aus der offenen DB holen 
input_results();

### Wöchentliche Updates ausführen
weekly_update("Thursday", "10:00", "11:00");


### Do Some precomputation
if (is_big_tournament(get_curr_wett())){
    ## Nur bei laufenden Wettbewerben
    if (is_active_wettbewerb()){
        echo "Update KO Runde!<br>";
        update_gruppenbeste();
        update_all_ko_spiele();
        
        ## TODO: vielleicht nicht immer machen?
        precompute_all_tipps_to_db(0, "Spieltag");
        precompute_all_tore_to_db(NULL, 15);
    }
} else  {
    ## Normale Bundesliga Saison
    if (spieltag_running() && is_active_wettbewerb()){
        precompute_all_tipps_to_db(akt_spieltag(), "Spieltag");   
        precompute_all_tore_to_db(akt_spieltag(), NULL);  
    }
}

# define functions that will be called..

function reminder_spieltag(){
    ### Dann immer schauen ob aktueller Spieltag oder das nächste Spiel schon getippt sind!
    
    ### Zetiliche Prüfung... Das soll natürlich nicht 1 mal die Minute geschickt werden...
    ### vllt 2h vor anpfiff und dann nochmal 10 min vorher?
    ### Und am Spieltags Beginn: (meistens ja Freitag) schon morgens eine Nachricht! (10 Uhr vllt)
    
    ### checke ob Zeit in pocket.. beim Senden Hinweis mit Uhrzeit in DB speichern.. Dann prüfen, dass der letzte Hinweis mindestens 15 ? (oder 30) min her ist
    
    ### Zweite Nachricht: Die nächsten Spiele sind...
    
    ### Uhrzeit, wann über Spieltag informiert werden soll und wie groß das Fenster ist. 
    ### Uhrzeit kann natürlich auch aus Datenbank geholt werden...
    $h   = 10;
    $min = 00;
    $fenster = 2 * 60;
    
    ### Welches Zeitintervall darf bei Benachrichtigungen nicht unterschritten werden?
    $min_interval = 3 * 60 - 10;
    
    ### Gehe durch alle User durch die sich hierfür angemeldet haben.. und nicht auf silent gestellt haben..
    foreach (get_notify_list("gameday") as $chat => $user_id){
        
        ## Haben wir Spieltag zur gewissen Uhrzeit?
        if (check_gameday_bot_time($h,$min, $fenster)){
            
            ##  Prüfe ob letztes Send entsprechend lange her ist..
            if (time() - check_notify_last_send($chat, "gameday") > $min_interval){
                
                list($spiele, $tipped) = get_bot_spiele($user_id, 0, "gameday");

                ## Update last send
                update_notify_last_send($chat, "gameday");
                
                ## Sende die Nachricht
                
                ### Deine bisherigen Tipps sind...: ?
                identify_bot($chat,"<b>Heute beginnt ein neuer Spieltag!\nVergesse nicht zu Tippen!</b>\n\nDiese Spiele stehen an:\n".$spiele."");                
            }
        }
    }
}



function reminder_tipps(){
    
    ## Wenn der Spieltag nicht läuft, muss auch keine Erinnerung geschickt werden...
    if (!spieltag_running()){
        return 0;
    }
    
    ## Zu welchen Zeitpunkten soll gemeldet werden? (in min) (+1min, weil in DB :59 gespeichert ist)
    $timelist = array(121, 61, 31, 16, 11, 6);

    ## Wie groß soll das Melde Fenster sein? 
    $fenster = 2 * 60;

    ### Welches Zeitintervall darf bei Benachrichtigungen nicht unterschritten werden?
    $min_interval = 3 * 60 - 10;
    

    ## Liste der nächsten Spiele
    list($next_games, $date) = get_next_games();



    ### Gehe durch alle User durch die sich hierfür angemeldet haben.. und nicht auf silent gestellt haben..
    foreach (get_notify_list("tipps") as $chat => $user_id){
        
        ## Haben wir Spieltag zur gewissen Uhrzeit?
        if (check_game_time($date,$timelist, $fenster)){
            
            ##  Prüfe ob letztes Send entsprechend lange her ist..
            if (time() - check_notify_last_send($chat, "tipps") > $min_interval){
                
                list($spiele, $tipped, $time) = get_bot_spiele($user_id, $next_games, "tipps");
                
                ## Checke ob überhaupt Tipps vergessen wurden
                if ($tipped){
                    
                    ## Update last send
                    update_notify_last_send($chat, "tipps");
                    
                    ## Sende die Nachricht
                    identify_bot($chat,"<b>Achtung!</b>\nIn $time beginnen die folgenden Spiele, die du noch nicht getippt hast:\n\n$spiele\nGehe rechtzeitig auf couchtipper.de um die Tipps einzutragen!");
                    
                }

            }
        }
    }
    
}


function weekly_update($allowed_day, $start_time, $end_time){
    ## Führt Wöchtentliche Updates aus. 
    ## Bisher: Prüfen ob Spiele zu terminieren sind..
    
    // Aktuellen Wochentag und Zeit holen
    $current_day = date("l"); // "Monday", "Tuesday", etc.
    $current_time = date("H:i");
        
    $lock_file = "weekly_update.lock";
    
    // Prüfen, ob wir im richtigen Zeitfenster sind
    if ($current_day === $allowed_day && $current_time >= $start_time && $current_time <= $end_time) {
        // Datei als "Lock" verwenden, um zu verhindern, dass die Funktion mehrfach in diesem Zeitfenster ausgeführt wird
        if (!file_exists($lock_file)) {
            touch($lock_file); // Lock-Datei erstellen
            
            #######
            ### Hier können die Funktionen eingetragen werden, die ausgeführt werden sollen!
            #######
            echo "Do weekly update! <br>";
            
            list($error, $msg) = set_all_spieltag_date();
            
            if (!$error){
                send_bot_message(2, "Das wöchentliche Update wurde erfolgreich durchgeführt!\n<b>Log-Output:</b>\n". str_replace("<br>", "\n", $msg));
                echo $msg;
                echo "Weekly Update Done.<br>";
            } else {
                send_bot_message(2, "Das wöchentliche Update ist fehlgeschlagen!\nLogs\n". str_replace("<br>", "\n", $msg));
                echo "Weekly Update Failed.<br>";
            }
            
            #######
            ### Bis hier können die Funktionen eingetragen werden, die ausgeführt werden sollen!
            #######
        }
    } else {
        // Lock-Datei löschen, wenn wir außerhalb des Zeitfensters sind
        if (file_exists($lock_file)) {
            unlink($lock_file);
        }
    }
}


function input_results(){
    ## Holt die Ergebnisse des aktuellen Spieltags aus OpenLigaDB
    global $g_nachholspiel;
    if (spieltag_running()){
        echo "Spieltag läuft!<br>";
        input_cronjob(akt_spieltag());
    }
    if ($g_nachholspiel !== NULL){
        input_cronjob($g_nachholspiel);
    }
}

function notify_paid(){
    ### Schicke eine Nachricht, wenn der bezahlt Status geändert wird! :)
    ### Zahlungseingang Bestätigung.. ;)
    ### Das ja eher nicht hier, sondern eher direkt beim Update..

}


function notfiy_result(){
    ### Ergebnisse verschicken? 
    ### Von den Spielen und auch den Tipps
}


echo "alles ok";
?>
