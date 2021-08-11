/// NICHT FERTIG EDIT<br>

/// Ergebnisse ändern fehlt
// schon ganz schnuggelisch
<?php
require_once('src/functions/main.inc.php');
require_once('src/functions/template.inc.php');
require_once('src/functions/input_template.inc.php');


if (!allow_erg()){
  echo "Dieser Bereich ist nur f&uuml;r Administratoren!<br>
  Frage beim Administrator nach, um Rechte zum &Auml;ndern von Ergebnissen zu bekommen.";
  exit;

}

?> 


<?php

$show_formular = true;
$error = false;
$error_msg = "";


$spieltag = $_POST['spieltag'];

if ($_POST['change'] == "1") {
  $change = true;
} else {
  $change = false;
}



if ($spieltag == ""){ 
   $spieltag = spt_select();
}



$e[1]=$_POST['erg11'];
$e[2]=$_POST['erg12'];
$e[3]=$_POST['erg21'];
$e[4]=$_POST['erg22'];
$e[5]=$_POST['erg31'];
$e[6]=$_POST['erg32'];
$e[7]=$_POST['erg41'];
$e[8]=$_POST['erg42'];
$e[9]=$_POST['erg51'];
$e[10]=$_POST['erg52'];
$e[11]=$_POST['erg61'];
$e[12]=$_POST['erg62'];
$e[13]=$_POST['erg71'];
$e[14]=$_POST['erg72'];
$e[15]=$_POST['erg81'];
$e[16]=$_POST['erg82'];
$e[17]=$_POST['erg91'];
$e[18]=$_POST['erg92'];




if (!empty(array_filter($e))){ //es wurde was übergeben

  for($i=1; $i<=18; $i = $i + 2){

    // $i ist HeimTeam, $a Auswärts
    $a = $i+1;

    if (($e[$i] != "") && ($e[$a] != "") ) {
      $input_nr = get_sp_nr($i);
      $input_spt = $spieltag;
      $input_user = get_usernr();
      $input_ip = $_SERVER['REMOTE_ADDR'];
      $input_tore1 = $e[$i];
      $input_tore2 = $e[$a];

      if ((is_pos_int($input_tore1)) && (is_pos_int($input_tore2))){

        if (allow_erg()){

          $statement = $g_pdo->prepare("
          INSERT INTO Ergebnisse (spieltag, sp_nr, tore1, tore2, debug_user, debug_ip) VALUES (:spieltag, :sp_nr, :tore1, :tore2, :user, :ip)
          ON DUPLICATE KEY UPDATE tore1 = :tore1, tore2 = :tore2, debug_user = :user, debug_ip = :ip");

          $result = $statement->execute(array('spieltag' => $input_spt, 'sp_nr' => $input_nr, 'tore1' => $input_tore1, 
          'tore2' => $input_tore2, 'user' => $input_user, 'ip' => $input_ip)); // VLLT NOCH DATUM ÄNDERN DER EINGABE

          if ($result == true) {
            $error_msg = "Die Ergebnisse wurden fehlerlos eingegeben.";
          } 	

        } else {
          $error = true;
          $error_msg =  "Du bist nicht berechtigt Ergebnisse einzugeben! <br> Frage einen Administrator nach den Rechten";

        }
 
      } else {
        $error = true;
        $error_msg =  "Die Eingaben sind nicht korrekt. Bitte nur positive Zahlen <100 verwenden!";
      }

    }

  }

} // END OF INPUT




if (show_formular){

  // SelectFormular zur Eingabe des Spieltages

  select_spieltag($spieltag); 


  echo "<br><div class = \"content\">";

  // Hinrunde / Rückrunde

  $teil1 = "1";
  $teil2 = "2";
  $real_spieltag = $spieltag;

  if ($spieltag > 17){
     $teil1 = "2";
     $teil2 = "1";
     $spieltag = $spieltag - 17;
  }

  // init (sonst schlägt sort() fehl)

  for ($i=1; $i<=9; $i++){
    $tore_heim[$i] = "";
    $tore_aus[$i] = "";
  }   


  // Hole Spiele aus DB
  $sql = "SELECT sp_nr, t1.team_name AS Team_name$teil1, t2.team_name AS Team_name$teil2, 
  datum$teil1 AS datum, t1.team_nr AS Team_nr$teil1, t2.team_nr AS Team_nr$teil2
  FROM `Spieltage`,Teams t1, Teams t2
  WHERE (`spieltag` = $spieltag) AND (t1.team_nr = team1) AND (t2.team_nr = team2)";


  foreach ($g_pdo->query($sql) as $row) {
    $sp_nr = $row['sp_nr'];
    $team_heim [$sp_nr] = $row['Team_name1'];
    $team_aus [$sp_nr] = $row['Team_name2'];
    $datum [$sp_nr] = $row['datum'];
    $team_heim_nr [$sp_nr] = $row['Team_nr1'];
    $team_aus_nr [$sp_nr] = $row['Team_nr2'];
    $real_sp_nr [$sp_nr] = $sp_nr;

  }


  // Hole Ergebnisse aus DB
  $sql = "SELECT sp_nr, tore1, tore2 FROM Ergebnisse
  WHERE spieltag = $real_spieltag";

  foreach ($g_pdo->query($sql) as $row) {
    $sp_nr = $row['sp_nr'];
    $tore_heim [$sp_nr] = $row['tore1'];
    $tore_aus [$sp_nr] = $row['tore2'];

    // falls eine Änderung gewählt wurde, ergebnisse in Textfelder schreiben
    if ($change){
      $help_tore1 = $row['tore1'];
      $help_tore2 = $row['tore2'];
      $tore_heim[$sp_nr] = "<input type=\"number\" name=\"erg".$sp_nr."1\" size=\"2\" max=\"99\" min=\"0\" style=\"width: 3em\" value =\"$help_tore1\">";
      $tore_aus [$sp_nr] = "<input type=\"number\" name=\"erg".$sp_nr."2\" size=\"2\" max=\"99\" min=\"0\" style=\"width: 3em\" value =\"$help_tore2\">";
    }

  }

  // falls keine Tore eingetragen sind, werden Textfelder angezeigt
  for ($i=1; $i<=9; $i++){
    if (($tore_heim[$i] == "") || ($tore_aus[$i] == "")){
      $tore_heim[$i] = "<input type=\"number\" name=\"erg".$i."1\" size=\"2\" max=\"99\" min=\"0\" style=\"width: 3em\">";
      $tore_aus [$i] = "<input type=\"number\" name=\"erg".$i."2\" size=\"2\" max=\"99\" min=\"0\" style=\"width: 3em\">";
      $aenderung = true;
    }

  }


  // Die Spiele des Spieltages werden nach Datum sortiert
  array_multisort($datum, SORT_ASC, $team_heim, $team_aus, $tore_heim, $tore_aus, $team_heim_nr, $team_aus_nr, $real_sp_nr);


  // RETURN ARRAY($datum, $team_heim, ..... 

 // AB HIER NUR NOCH AUSGABE FÜR BROWSERVERSION (MOBIL/DESKTOP)

  $help = $datum[0];

  echo "<form method=\"POST\" action =\"#8\">";

  echo "<table align=\"center\" border=\"0\" width=\"100%\"  style =\"border-radius: 10px\">";

  echo "<tr>";
  echo "<td colspan=\"5\" bgcolor = \"lightgrey\"><font size=\"3\">"; // FARBE muss auch AUS DB
  echo stamp_to_date($datum[0]);
  echo "</font></td></tr>";

  for ($i = 0; $i <= 8; $i++){
    echo "<tr>";

    if ($help != $datum[$i]){    // fuegt Datums-Zeile ein
      echo "<td colspan=\"5\" bgcolor = \"#CC9999\"><font size=\"3\">";
      echo stamp_to_date($datum[$i]);
      echo "</font></td></tr>";

      $help = $datum[$i];
    }

    echo "<td align=\"right\"><b>$team_heim[$i]</b></td>
    ";
    //echo "<td align=\"right\"><img src=\"images/Vereine/$team_heim_nr[$i].gif\" height=\"22\"></td>
    //";
    echo "<td align=\"center\"><b>$tore_heim[$i] : $tore_aus[$i]</b></td>
    ";
    //echo "<td align=\"left\"><img src=\"images/Vereine/$team_aus_nr[$i].gif\" height=\"22\"></td>
    //";
    echo "<td align=\"left\"><b>$team_aus[$i]</b></td>
    ";

    echo "</tr>
    ";
  }


  echo "</table><br>
  <input type=\"hidden\" value =\"$real_spieltag\" name=\"spieltag\"><input type=\"Submit\" value=\"Enter\"></form>";


  if (!$change) {
  echo "<form method = \"POST\" action =\"#8\">
  <input type=\"hidden\" value =\"$real_spieltag\" name=\"spieltag\">
  <input type=\"hidden\" value =\"1\" name=\"change\">
  <input type = \"Submit\" value = \"Ergebnisse &auml;ndern\"></form>";
  }


  echo $error_msg;

} // end of if show formular


?>


</div>
<br>
