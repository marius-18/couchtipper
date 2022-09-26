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


if ($_GET['mode'] == "user") {
    $user = 1;
} else {
    $user = 0;
}

if ($_GET['mode'] == "email") {
    $email = 1;
} else {
    $email = 0;
}

if ($_GET['mode'] == "pw") {
    $pw = 1;
} else {
    $pw = 0;
}




if ($_POST['name']){
    $name = $_POST['name'];
    $name = str_replace(' ','',$name); 
    
    list($user_error, $user_error_msg) = change_name($name);


}

if ($_POST['mail']){
    $mail = $_POST['mail'];

    list($mail_error, $mail_error_msg) = change_mail($mail);

}

if ($_POST['password'] && $_POST['password1'] && $_POST['password_old']){
    $password_old = $_POST['password_old'];
    $password = $_POST['password'];
    $password1 = $_POST['password1'];
     list($pw_error, $pw_error_msg) = change_pw($password, $password1, $password_old);

}

if ($_POST['team']) {
    $team = $_POST['team'];

    $insert = $g_pdo->prepare("UPDATE `User` SET `team`=:team WHERE user_nr = ".get_usernr());
    $params = array('team' => $team);
    $insert->execute($params);

}





echo "<div style=\"text-align:left\"><font size =\"+2\"><u>Pers&ouml;nliche Daten</u></font></div><br>";

echo "<font size=\"+1\">Benutzername: <br> <b>".name($user)."</b></font>";

if ($_GET['mode'] != "user"){
    echo "&nbsp;<a href = \"index.php?index=7&mode=user#7\"><img src =\"/images/edit.png\" width=\"15\" height = \"15\"></a></font><br>";
}

if ($user_error){
    echo "<br><div class=\"alert alert-danger\"><strong>Fehler:</strong> $user_error_msg </div><br>";
}

echo "<br>";

echo "<font size=\"+1\">E-Mail Adresse:<br> <u>".email($email)."</u></font>"; 

if ($_GET['mode'] != "email"){
echo "&nbsp;<a href = \"index.php?index=7&mode=email#7\"><img src =\"/images/edit.png\" width=\"15\" height = \"15\"></a></font><br>";
}

if ($mail_error){
    echo "<br><div class=\"alert alert-danger\"><strong>Fehler:</strong> $mail_error_msg </div><br>";
}

echo "<br>";
echo "<font size=\"+1\">Passwort ändern:<br>".password($pw)."</font>"; 

if ($_GET['mode'] != "pw"){
echo "&nbsp;<a href = \"index.php?index=7&mode=pw#7\"><img src =\"/images/edit.png\" width=\"15\" height = \"15\"></a></font><br>";
}


if ($pw_error){
    echo "<br><div class=\"alert alert-danger\"><strong>Fehler:</strong> $pw_error_msg </div><br>";
}

if ((!$pw_error) && ($pw_error_msg != "")) {
    echo "<br><div class=\"alert alert-success\"> $pw_error_msg </div><br>";
}


echo "<font color = \"red\"><b>$password_error</b></font>";

echo "<br><br>";
//echo "<font size=\"+1\">Lieblingsteam:<br>".team()."</font>"; 

echo "<hr>";
echo "<div style=\"text-align:left\"><font size =\"+2\"><u>Tippgruppen</u></font></div><br>";

echo "<font size=\"+1\">".get_wettbewerb_name(get_hinrunde(get_curr_wett()))." ".get_wettbewerb_jahr(get_curr_wett()).":<br><br>";


$hin = check_cash(get_hinrunde(get_curr_wett()));
if ($hin){
echo "Du hast schon bezahlt&nbsp; <img src = \"images/check.png\" width=\"20\" height=\"20\"><br>";
} else {
echo "Du hast noch <b>nicht</b> bezahlt&nbsp; <img src = \"images/remove.svg\" width=\"20\" height=\"20\"><br>";
}

########## DAS HIER NUR; WENN WIR AUCH IM BULI MODUS SIND....
if (user_is_in_wettbewerb(get_rueckrunde(get_curr_wett()))){
echo "<br><br>";
echo "<font size=\"+1\">".get_wettbewerb_name(get_rueckrunde(get_curr_wett()))." ".get_wettbewerb_jahr(get_curr_wett()).":<br><br>";

$hin = check_cash(get_rueckrunde(get_curr_wett()));
if ($hin){
echo "Du hast schon bezahlt&nbsp; <img src = \"images/check.png\" width=\"20\" height=\"20\"><br>";
} else {
echo "Du hast noch <b>nicht</b> bezahlt&nbsp; <img src = \"images/remove.svg\" width=\"20\" height=\"20\"><br>";
}

}


echo "</font>";

echo "<br><br>";

echo "<hr>";

echo "<div style=\"text-align:left\"><font size =\"+2\"><u>Verbundene Ger&auml;te</u></font></div><br>";
include_once("src/newbot.php");

echo "<br><br>";

echo "<hr>";

echo "<div style=\"text-align:left\"><font size =\"+2\"><u>Sitzungen</u></font></div><br>";

sitzungen();



?>



</div>
<br>
