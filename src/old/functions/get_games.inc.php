<?php
/*
// Holt Spiele und Ergebnisse aus der Datenbank
*/



//$modus =  "Spieltag", "Tipps", "Ergebnisse"

function get_games ($spieltag, $modus, $change, $user_nr) {
global $g_pdo;


  // Initialisiere Arrays wegen sort()
  for ($i=1; $i<=9; $i++){
    $tore_heim[$i] = "";
    $tore_aus[$i] = "";
  } 


  // Hinrunde / Rückrunde
  $teil1 = "1";
  $teil2 = "2";
  $real_spieltag = $spieltag;
  $add = "0";

  if ($spieltag > 17){
     $teil1 = "2";
     $teil2 = "1";
     $spieltag = $spieltag - 17;
  }



$sql = "
  SELECT sp_nr, t1.team_name AS Team_name$teil1, t2.team_name AS Team_name$teil2, 
  datum$teil1 AS datum, t1.team_nr AS Team_nr$teil1, t2.team_nr AS Team_nr$teil2
  FROM Spieltage,Teams t1, Teams t2
  WHERE (spieltag = $spieltag) AND (t1.team_nr = team1) AND (t2.team_nr = team2)";

  foreach ($g_pdo->query($sql) as $row) {
    $sp_nr = $row['sp_nr'];
    $team_heim [$sp_nr] = $row['Team_name1'];
    $team_aus [$sp_nr] = $row['Team_name2'];
    $datum [$sp_nr] = $row['datum'];
    $team_heim_nr [$sp_nr] = $row['Team_nr1'];
    $team_aus_nr [$sp_nr] = $row['Team_nr2'];
    $real_sp_nr [$sp_nr] = $sp_nr;

  }


  if ($modus == "Tipps"){ // HIER MUSS NOCH DIE USERNR eingebene werden

    $sql = "SELECT sp_nr, tore1, tore2 FROM Tipps
    WHERE ((spieltag = $real_spieltag) AND (user_nr = $user_nr))";

  } 
  else {

    if (($modus == "Ergebnisse") || ($modus == "Spieltag")) {

      $sql = "SELECT sp_nr, tore1, tore2 FROM Ergebnisse
      WHERE ((spieltag = $real_spieltag))";

    }
  }


  foreach ($g_pdo->query($sql) as $row) {
    $sp_nr = $row['sp_nr'];
    $tore_heim [$sp_nr] = $row['tore1'];
    $tore_aus [$sp_nr] = $row['tore2'];

    // falls eine Änderung gewählt wurde, Ergebnisse in Textfelder schreiben
    if (($change) && ($modus != "Spieltag")){
      $help_tore1 = $row['tore1'];
      $help_tore2 = $row['tore2'];


      if ((check_game_date($real_spieltag, $sp_nr) && ($modus == "Tipps") && (get_usernr() != "")) || ((allow_erg()) && ($modus == "Ergebnisse")) || (allow_tipps() && ($modus == "Tipps")) ) {
        $disable = "";
        $farbe = "";
      } else {

        $disable = "disabled";
        $farbe = "background-color: darkgray;";
      }


      $tore_heim[$sp_nr] = "<input type=\"number\" name=\"erg".$sp_nr."1\" size=\"2\" max=\"99\" min=\"0\" style=\"width: 3em; $farbe\" value =\"$help_tore1\" $disable>";
      $tore_aus [$sp_nr] = "<input type=\"number\" name=\"erg".$sp_nr."2\" size=\"2\" max=\"99\" min=\"0\" style=\"width: 3em; $farbe\" value =\"$help_tore2\" $disable>";
    }

  }



  // falls keine Tore eingetragen sind, werden Textfelder angezeigt
  for ($i=1; $i<=9; $i++){
      if ((check_game_date($real_spieltag, $i)  && ($modus == "Tipps") && (get_usernr() != "")) || ((allow_erg()) && ($modus == "Ergebnisse")) || (allow_tipps() && ($modus == "Tipps"))   ) { 
// sperrt nummernfelder wenn zu spät // VLLT ZÄHLER wenn keine spiele verfügbar ?

        $disable = "";
        $farbe = "";
      } else {
        $disable = "disabled";
        $farbe = "background-color: darkgray;";
      }


    if (($tore_heim[$i] == "") || ($tore_aus[$i] == "")){
      if (($modus == "Tipps") || ($modus == "Ergebnisse")) {
      $tore_heim[$i] = "<input type=\"number\" name=\"erg".$i."1\" size=\"2\" max=\"99\" min=\"0\" style=\"width: 3em; $farbe\" $disable>";
      $tore_aus [$i] = "<input type=\"number\" name=\"erg".$i."2\" size=\"2\" max=\"99\" min=\"0\" style=\"width: 3em; $farbe\" $disable>";
      $aenderung = true; // HM WAS IS DAS
      } else {
        if ($modus == "Spieltag"){
          $tore_heim[$i] = " - ";
          $tore_aus[$i] = " - ";
        }
      }
    }

  }


  array_multisort($datum, SORT_ASC, $team_heim, $team_aus, $tore_heim, $tore_aus, $team_heim_nr, $team_aus_nr, $real_sp_nr);

return array($datum, $team_heim, $team_aus, $tore_heim, $tore_aus, $team_heim_nr, $team_aus_nr, $real_sp_nr, $real_spieltag);


// warum nicht gleich ausgeben ?
// Wegen mobil/desktop/ipad?


}




?>

