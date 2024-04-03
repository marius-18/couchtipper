<br>
<div class = "container">

<?php
if (!is_logged()){
    echo "<div class=\"alert alert-danger\"><span class=\"badge badge-pill badge-danger\">Fehler!</span> Dieser Bereich steht nur für eingeloggte User zur Verfügung!</div>";
    exit;
}

?>

<?php
require_once('src/include/code/konto.inc.php');


if (isset($_GET['mode']) && ($_GET['mode'] == "user")) {
    $user = 1;
} else {
    $user = 0;
}

if (isset($_GET['mode']) && ($_GET['mode'] == "email")) {
    $email = 1;
} else {
    $email = 0;
}

if (isset($_GET['mode']) && ($_GET['mode'] == "pw")) {
    $pw = 1;
} else {
    $pw = 0;
}


$user_error = false;
$user_error_msg = "";

if (isset($_POST['name']) && ($_POST['name'])) {
    $name = $_POST['name'];
    $name = str_replace(' ','',$name); 
    
    list($user_error, $user_error_msg) = change_name($name);
}


$mail_error = false;
$mail_error_msg = "";

if (isset($_POST['mail']) && ($_POST['mail'])) {
    $mail = $_POST['mail'];

    list($mail_error, $mail_error_msg) = change_mail($mail);

}


$pw_error = false;
$pw_error_msg = "";

if (isset($_POST['password']) && isset($_POST['password1']) && isset($_POST['password_old']) && $_POST['password'] && $_POST['password1'] && $_POST['password_old']){
    $password_old = $_POST['password_old'];
    $password = $_POST['password'];
    $password1 = $_POST['password1'];
    list($pw_error, $pw_error_msg) = change_pw($password, $password1, $password_old);

}



###########################################################
###########################################################
### P E R S Ö N L I C H E    D A T E N                                               
###########################################################
###########################################################

echo "<div style=\"text-align:left\"><font size =\"+2\"><u>Pers&ouml;nliche Daten</u></font></div><br>";

echo "<font size=\"+1\">Benutzername: <br> <b>".name($user)."</b></font>";

if (!isset($_GET['mode']) || ($_GET['mode'] != "user")){
    echo "&nbsp;<a href = \"index.php?index=7&mode=user#7\"><i class=\"far fa-edit fa-1x\"></i></a><br>";
}

if ($user_error){
    echo "<br><div class=\"alert alert-danger\"><strong>Fehler:</strong> $user_error_msg </div><br>";
}
echo "<br>";



echo "<font size=\"+1\">E-Mail Adresse:<br> <u>".email($email)."</u></font>"; 

if (!isset($_GET['mode']) || ($_GET['mode'] != "email")) {
    ### Bearbeiten Knopf anzeigen
    echo "&nbsp;<a href = \"index.php?index=7&mode=email#7\"><i class=\"far fa-edit fa-1x\"></i></a><br>";
}

if ($mail_error){
    echo "<br><div class=\"alert alert-danger\"><strong>Fehler:</strong> $mail_error_msg </div><br>";
}
echo "<br>";



echo "<font size=\"+1\">Passwort ändern:<br>".password($pw)."</font>"; 

if (!isset($_GET['mode']) || ($_GET['mode'] != "pw")){
    ### Bearbeiten Knopf anzeigen
    echo "&nbsp;<a href = \"index.php?index=7&mode=pw#7\"><i class=\"far fa-edit fa-1x\"></i></a><br>";
}


if ($pw_error){
    ## Fehler ausgeben
    echo "<br><div class=\"alert alert-danger\"><strong>Fehler:</strong> $pw_error_msg </div><br>";
}

if ((!$pw_error) && ($pw_error_msg != "")) {
    ## Erfolg ausgeben
    echo "<br><div class=\"alert alert-success\"> $pw_error_msg </div><br>";
}


echo "<br><br>";

###########################################################
###########################################################
### T I P P G R U P P E N                                               
###########################################################
###########################################################


echo "<hr>";
echo "<div style=\"text-align:left\"><font size =\"+2\"><u>Tippgruppen</u></font></div><br>";

## Hier vielleicht noch einfügen, dass alle (aktiven) Wettbewerbe gelistet werden


if (user_is_in_wettbewerb(get_hinrunde(get_curr_wett()))){
    ## Hinrunde (bzw. bei anderen Wettbewerben der Hauptteil
    $hin = check_cash(get_hinrunde(get_curr_wett()));
    if ($hin){
        $color = "success";
    } else {
        $color = "danger";
    }
    echo "<div class=\"alert alert-$color\">";
        echo "<font size=\"+1\">".get_wettbewerb_name(get_hinrunde(get_curr_wett()))." ".get_wettbewerb_jahr(get_curr_wett()).":<br><br>";
        if ($hin){
            echo "<strong>Du hast schon bezahlt&nbsp; <i class=\"fas fa-check warning text-success\"></i></strong>";
        } else {
            echo "<strong>Du hast noch <b>nicht</b> bezahlt&nbsp; <i class=\"fas fa-times\"></i></strong>";
        }
    
    echo "</font></div>";

}
    

if (user_is_in_wettbewerb(get_rueckrunde(get_curr_wett()))){
    ## Wenn es noch eine Rückrunde gibt:
    $rueck = check_cash(get_rueckrunde(get_curr_wett()));
    if ($rueck){
        $color = "success";
    } else {
        $color = "danger";
    }
    echo "<br>";
    
    echo "<div class=\"alert alert-$color\">";
        echo "<font size=\"+1\">".get_wettbewerb_name(get_rueckrunde(get_curr_wett()))." ".get_wettbewerb_jahr(get_curr_wett()).":<br><br>";

        if ($rueck){
            echo "<strong>Du hast schon bezahlt&nbsp; <i class=\"fas fa-check warning text-success\"></i></strong>";
        } else {
            echo "<strong>Du hast noch <b>nicht</b> bezahlt&nbsp; <i class=\"fas fa-times\"></i></strong>";
        }

    echo "</font></div>";
}


echo "<br><br>";


###########################################################
###########################################################
### L I E B L I N G S T E A M                                               
###########################################################
###########################################################

echo "<hr>";
echo "<div style=\"text-align:left\"><font size =\"+2\"><u>Lieblingsteam auswählen</u></font></div><br>";

echo "<font size =\"+1\"><div class=\"alert alert-info\"> Hier kannst du ein Lieblingsteam auswählen. Das hat z.B. Auswirkungen darauf, welches Team unter <a href=\"?index=3\">\"Restprogramm\"</a> zuerst erscheint.
<br>Weiter unten kannst du dir den Spielplan deines Lieblingsteams in deinen Kalender importieren</div>";


$my_team = select_team();
set_fav_team($my_team);


echo "</font><br><br>";


###########################################################
###########################################################
### B O T  -  G E R Ä T E                                             
###########################################################
###########################################################

echo "<hr>";
echo "<font size =\"+1\"><div style=\"text-align:left\"><font size =\"+2\"><u>Verbundene Ger&auml;te</u></font></div><br>";
include_once("src/include/code/konto_bot.inc.php");

echo "</font><br><br>";

###########################################################
###########################################################
### S I T Z U N G E N                                               
###########################################################
###########################################################

echo "<hr>";
echo "<font size =\"+1\"><div style=\"text-align:left\"><font size =\"+2\"><u>Angemeldete Sitzungen</u></font></div><br>";

sitzungen();

echo "</font><br><br>";

###########################################################
###########################################################
###  K A L E N D E R                                              
###########################################################
###########################################################

echo "<hr>";
echo "<div style=\"text-align:left\"><font size =\"+2\"><u>Kalender exportieren</u></font></div><br>";
echo "<font size =\"+1\">";

if (get_fav_team() != ""){
    $calendar_year = get_curr_wett()[0];
    echo "<div class=\"alert alert-info\"> Wenn du keine Spiele deiner Lieblingsmannschaft <strong>".get_team_open_db_name(get_fav_team())."</strong> mehr verpassen willst,
    kannst du dir durch einen Klick auf den Button, den Kalender mit allen Spielen auf dein Gerät exportieren!
    </div>    
    <button type=\"button\" class=\"btn btn-info\" onclick=\"window.location.href='webcal://couchtipper.de?index=cal&team=$my_team&year=$calendar_year'\">Kalender exportieren!</button>
    ";
    
} else {
    echo "<div class=\"alert alert-danger\"><strong>Achtung!</strong> Du hast noch kein Lieblingsteam ausgew&auml;hlt. Die Funktion steht dir deshalb noch nicht zur Verf&uuml;gung.
    Weiter oben kannst du ein Lieblingsteam ausw&auml;hlen!</div>";
}

echo "</font>";



?>



</div>
<br>
