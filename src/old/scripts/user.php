<br>
<div class = "content">

<?php
require_once('src/functions/user.inc.php');

if (!allow_verwaltung()){
exit;
}



rechte_update();

pay_update();




echo "<div style=\"text-align:left\"><font size =\"+2\"><u>Einsatz</u></font></div><br>";

$out_einsatz = paid();
echo "<form action=\"?index=12#12\" method=\"post\">";
echo "<center><table align = \"center\" border = \"1\">";

echo "<tr align = \"center\">
      <td>Username</td>
      <td>Hinrunde</td>
      <td>R&uuml;ckrunde</td><tr>
      ";



foreach ($out_einsatz as $output){
echo $output;

}

      $sql = "SELECT sum(hin) as sumhin, sum(rueck) as sumrueck FROM `User` WHERE 1";
      foreach ($g_pdo->query($sql) as $allow){
          $sumhin = $allow['sumhin'];
          $sumrueck = $allow['sumrueck'];
          }

echo "<tr align = \"center\"><td></td><td>$sumhin</td><td>$sumrueck</td></tr></table><input type=\"hidden\" value = \"1\" name =\"paysecure\"><input type = \"submit\" value = \"&Auml;ndern\" ></form></center>";


echo "<hr>";


$out = rechte();

echo "<div style=\"text-align:left\"><font size =\"+2\"><u>Rechte-Verwaltung</u></font></div><br>";

echo "<form action=\"?index=12#12\" method=\"post\">";
echo "<center><table align = \"center\" border = \"1\">";

echo "<tr align = \"center\">
      <td>Username</td>
      <td>Tipps &auml;ndern</td>
      <td>Erg. &auml;ndern</td>
      <td>Datum &auml;ndern</td>
      <td>User</td></tr>";


foreach ($out as $output){
echo $output;

}

echo "</table><input type=\"hidden\" value = \"1\" name =\"secure\"><input type = \"submit\" value = \"&Auml;ndern\" ></form></center>";



?>



</div>
<br>
