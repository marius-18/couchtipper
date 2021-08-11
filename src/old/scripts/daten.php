<br>
<div class = "content">

<?php
require_once('src/functions/daten.inc.php');


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

   if (!user_vorhanden($name)){
      $insert = $g_pdo->prepare("UPDATE `User` SET `user_name`=:username WHERE user_nr = ".get_usernr());
      $params = array('username' => $name);
      $insert->execute($params);
   } else {
      $user_error =  "Den Benutzernamen gibt es schon";
   }
}

if ($_POST['mail']){
   $mail = $_POST['mail'];
   if(filter_var($mail, FILTER_VALIDATE_EMAIL)) {
      $insert = $g_pdo->prepare("UPDATE `User` SET `email`=:email WHERE user_nr = ".get_usernr());
      $params = array('email' => $mail);
      $insert->execute($params);
   } else {
      $mail_error = "Die Email ist nicht gültig!";
   }

}

if ($_POST['password'] && $_POST['password1'] && $_POST['password_old']){
   $password_old = $_POST['password_old'];
   $password = $_POST['password'];
   $password1 = $_POST['password1'];
   $sql = "SELECT passwort FROM User WHERE user_nr = ".get_usernr();

   foreach ($g_pdo->query($sql) as $row) {
      $password_orig = $row['passwort'];
   }

   if (password_verify($password_old, $password_orig)){
      if ($password === $password1){
         $password_hash = password_hash($password, PASSWORD_DEFAULT);
         $statement = $g_pdo->prepare("UPDATE User SET passwort = :password WHERE user_nr = :userid");
         $result = $statement->execute(array('password' => $password_hash, 'userid' => get_usernr()));
         if($result) {		
            $password_error = "Das Passwort wurde erfolgreich geändert!";
         }
      }
   }

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
echo "&nbsp;<a href = \"index.php?index=13&mode=user#13\"><img src =\"/images/edit.png\" width=\"15\" height = \"15\"></a></font><br>";
}

echo "<font color = \"red\"<b>$user_error</b></font>";

echo "<br><br>";

echo "<font size=\"+1\">E-Mail Adresse:<br> <u>".email($email)."</u></font>"; 

if ($_GET['mode'] != "email"){
echo "&nbsp;<a href = \"index.php?index=13&mode=email#13\"><img src =\"/images/edit.png\" width=\"15\" height = \"15\"></a></font><br>";
}

echo "<font color = \"red\"><b>$mail_error</b></font>";

echo "<br><br>";

echo "<font size=\"+1\">Passwort ändern:<br>".password($pw)."</font>"; 

if ($_GET['mode'] != "pw"){
echo "&nbsp;<a href = \"index.php?index=13&mode=pw#13\"><img src =\"/images/edit.png\" width=\"15\" height = \"15\"></a></font><br>";
}

echo "<font color = \"red\"><b>$password_error</b></font>";

echo "<br><br>";

echo "<font size=\"+1\">Lieblingsteam:<br>".team()."</font>"; 

echo "<hr>";
echo "<div style=\"text-align:left\"><font size =\"+2\"><u>Tippgruppen</u></font></div><br>";

echo "<font size=\"+1\">Bundesliga Saison 2017/18:<br><br>";

list($hin, $rueck) = bezahlt();
echo "Hinrunde ";
if ($hin){
echo "bezahlt&nbsp; <img src = \"images/check.png\" width=\"20\" height=\"20\"><br>";
} else {
echo "<b>nicht</b> bezahlt&nbsp; <img src = \"images/remove.svg\" width=\"20\" height=\"20\"><br>";
}
echo "R&uuml;ckrunde ";
if ($rueck){
echo " bezahlt&nbsp; <img src = \"images/check.png\" width=\"20\" height=\"20\"><br>";
} else {
echo "<b>nicht</b> bezahlt&nbsp; <img src = \"images/remove.svg\" width=\"20\" height=\"20\">";
}

echo "</font>";

echo "<br><br>";

echo "<hr>";

echo "<div style=\"text-align:left\"><font size =\"+2\"><u>Sitzungen</u></font></div><br>";

sitzungen();

echo "<hr>";


?>



</div>
<br>
