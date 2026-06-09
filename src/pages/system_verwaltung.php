<?php
global $global_wett_id;
## Wenn die Datenbank komplett leer ist, wurde gerade eine neue Saison erstellt.
## Dann springen wir auf die Seite, zum weiteren Erstellen der Saison
if (check_if_db_empty() || (isset($_GET["setup"]) &&  $_GET["setup"] == 1)) {
    if (allow_main_verwaltung()){
        ## Nur mit Verwaltungsrechten aufrufbar!
        include("src/setup/setup.php");
    } else {
        ## Fehler anzeigen, falls hier jemand landet..
        echo $error_msg;
    }
}

if (get_wettbewerb_code(get_curr_wett()) == "Verwaltung") {
    if (allow_main_verwaltung()){
        ## Nur mit Verwaltungsrechten aufrufbar!
        include_once("src/setup/new_wettbewerb.php");
        ## TODO: im Verwaltungsmenü hinzufügen, welche Wettbewerbe aktiv sind usw.
        ### set_global_wett_id(8);
    } else {
        ## Fehler anzeigen, falls hier jemand landet..
        echo $error_msg;
    }
}



$error_msg =
    "<div class=\"alert alert-danger\">
        <strong>Hier ist etwas schiefgelaufen..</strong>
        Du hast keinen Zugriff auf diese Seite!.
        Du solltest hier eigentlich nicht sein! Durch den folgenden Button kommst du wieder zurück!
        <br>
        <a href=\"?year=reset\" class=\"btn btn-primary\">Zurück!</a>
        </div>";

?>
