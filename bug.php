<?php
require_once("src/mobil_header.php");

?>

<div class="login">Bug Report</div><br>
<div class="content">

<?php
$message=$_GET['message'];
$name=$_GET['name'];

echo"
<form action=\"\" method=\"get\" autocomplete=\"off\" />
Dein Name:<br> 
<input type=\"text\" name=\"name\"><br><br>
Nachricht:<br>  <textarea name=\"message\"  cols=\"30\" rows=\"10\"></textarea><br>


<input type=\"Submit\" value=\"Enter\"><br><br>
  </form><br>";



$empfaenger = "marius@ing-hagemann.de";
$betreff = "BugReport von: $name";
$from = "From: Admin <marius@ing-hagemann.de>";
$text = $message;



if ($message!="" && $name=!""){
mail($empfaenger, $betreff, $text, $from);
echo "<script language='javascript'>alert ('Die Nachricht wurde verschickt!')</script>";

	echo"<script type=\"text/javascript\"> // WEITERLEITUNG
	<!--
	setTimeout(\"self.location.href='index.php'\",1);
	//-->
	</script>";

}

echo"</div><br><div class=\"news\"><a href=\"index.php\"><-- Zur&uuml;ck</a></div>";
?>

</div>
</body>
</html>
