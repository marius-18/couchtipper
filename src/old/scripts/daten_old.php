<?php 
if(!isset($_COOKIE['username'])) 
   { 
   echo "Bitte erst <a href=\"login.php\">einloggen</a>";
   exit; 
   } 
?> 




<div class="content">
<?php
$pwold=md5($_POST['passwordold']);
$pw=md5($_POST['password']);
$pw1=md5($_POST['password1']);
$pwreal=$_COOKIE['passwort'];
$teamnr=$_POST['Team'];

if ($teamnr!=""){
$sql="UPDATE `user` SET `team`='$teamnr' WHERE `UName`='$uname'";
	  $abfrage=mysql_query($sql);
	if ($abfrage==true){echo "<script language='javascript'>alert (unescape(\"Dein Lieblingsteam wurde erfolgreich ge%E4ndert%21\"))</script>";} else {echo "Fehler";}

}


$abfrage=mysql_query("SELECT email,team FROM user WHERE UName='$uname'");
while ($row=mysql_fetch_object($abfrage)) {
    $email=$row->email; 
    $teamnr=$row->team;
  }

$ip=$_SERVER['REMOTE_ADDR'];

echo "Benutzername: $uname <br>

email-Addresse: $email<br>



IP-Adresse: $ip <br><br>

";

echo"<form method=\"post\">Lieblingsteam:<select name=\"Team\">";
$abc="";
for ($tnummer=1; $tnummer<19; $tnummer++){
 $abfrage=mysql_query("SELECT TName FROM teams WHERE TNr='$tnummer'");

 while ($row=mysql_fetch_object($abfrage)) {
   $team=$row->TName; 
  }
 if ($tnummer==$teamnr){
  $abc=" selected";
 }
 echo"  
<option value=\"$tnummer\" $abc>$team</option>";
 $abc="";
}

echo"
<input type=\"submit\" value=\"Enter\">
</select></form>";





echo "<u>Kennwort &auml;ndern</u><br>

<form action=\"\" method=\"post\">
  altes Passwort:<br><input type=\"password\" name=\"passwordold\"><br>
  neues Passwort:<br><input type=\"password\" name=\"password\"><br>
  neues Passwort wiederholen:<br><input type=\"password\" name=\"password1\"><br>
  <input type=\"Submit\" value=\"Enter\"><br><br>
  </form>
";

if ($pwold!="d41d8cd98f00b204e9800998ecf8427e"){
 if ($pwold!=$pwreal){
  echo "Das Passwort ist falsch!";
 } else {
   if ($pw1!=$pw){
     echo "Die eingegebenen Passw&ouml;rter stimmen nicht &uuml;berein!";
   } else {
      $sql="UPDATE `user` SET `Passwort`='$pw' WHERE `UName`='$uname'";
	  $abfrage=mysql_query($sql);
	if ($abfrage==true){
	  echo "<script language='javascript'>alert (unescape(\"Dein Passwort wurde erfolgreich ge%E4ndert%21\"))</script>";
	  $_COOKIE['pw']=$pw;
        } else {
	  echo "<script language='javascript'>alert (unescape(\"Die %C4nderung hat nicht funktioniert%21\"))</script>";
          }

    }
 }
}

$pwold="";
$pw="";
$pw1="";
$pwreal="";
?>
</div><br>