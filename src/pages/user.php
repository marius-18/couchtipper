<br>
<div class = "content">

<?php
//require_once('src/user.inc.php');

if (!allow_verwaltung()){
    echo "<div class=\"alert alert-danger\"> Dieser Bereich ist <strong>nur f&uuml;r Administratoren</strong>!<br>
        Frage beim Administrator nach, um Rechte zum &Auml;ndern von Rechten zu bekommen.</div>";
    exit;
}



rechte_update();

pay_update();




echo "<div style=\"text-align:left\"><font size =\"+2\"><u>Einsatz</u></font></div><br>";

$out_einsatz = paid();
echo "<form action=\"?index=9#main\" method=\"post\">";
echo "<center><table class=\"table table-striped table-hover text-center\">";

echo "<tr class=\"thead-dark\">
      <th>Username</th>
      <th>bezahlt</th>
      <tr>
      ";



foreach ($out_einsatz as $output){
    echo $output;
}

      $sql = "SELECT sum(hin) as sumhin, sum(rueck) as sumrueck FROM `User` WHERE 1";
      foreach ($g_pdo->query($sql) as $allow){
          $sumhin = $allow['sumhin'];
          $sumrueck = $allow['sumrueck'];
          }

//echo "<tr align = \"center\"><td></td><td>$sumhin</td><td>$sumrueck</td></tr>";

echo "</table>";


echo "<input type=\"hidden\" value = \"1\" name =\"paysecure\"><input type = \"submit\" value = \"&Auml;ndern\" ></form></center>";


echo "<hr>";


$out = rechte();

echo "<div style=\"text-align:left\"><font size =\"+2\"><u>Rechte-Verwaltung</u></font></div><br>";

echo "<form action=\"?index=9#main\" method=\"post\">";
echo "<center><div class=\"table-responsive\"><table class=\"table table-striped table-hover text-center\">";

echo "<tr class=\"thead-dark\">
      <th>Username</th>
      <th>Tipps &auml;ndern</th>
      <th>Erg. &auml;ndern</th>
      <th>Datum &auml;ndern</th>
      <th>User</th></tr>";


foreach ($out as $output){
echo $output;

}

echo "</table></div><input type=\"hidden\" value = \"1\" name =\"secure\"><input type = \"submit\" value = \"&Auml;ndern\" ></form></center>";



?>



</div>
<br>
