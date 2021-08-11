<?php


function update_tabelle($spieltag) {
  global $g_pdo;

  if ($spieltag > 17) {
    $hilfe = 17;
  } else {
    $hilfe = 0;
  }

  $sql = "SELECT Spieltage.spieltag AS spt, tore1, tore2, team1, team2 
        FROM `Ergebnisse`, Spieltage 
        WHERE (Spieltage.spieltag = Ergebnisse.spieltag - $hilfe) 
        AND (Ergebnisse.sp_nr = Spieltage.sp_nr) AND (Ergebnisse.spieltag = $spieltag)";

  foreach ($g_pdo->query($sql) as $row) {  
    $tore1 = $row['tore1'];
    $tore2 = $row['tore2'];
  
    if ($spieltag <= 17){
      $team1 = $row['team1'];
      $team2 = $row['team2'];
    } else {
      $team1 = $row['team2'];
      $team2 = $row['team1'];
    }

    if ($tore1 > $tore2) {
      $sieg = 1;
      $unentschieden = 0;
      $niederlage = 0;
      $punkte = 3;
    }
    if ($tore1 == $tore2) {
      $sieg = 0;
      $unentschieden = 1;
      $niederlage = 0;
      $punkte = 1;
    }
    if ($tore1 < $tore2) {
      $sieg = 0;
      $unentschieden = 0;
      $niederlage = 1;
      $punkte = 0;
    }

    $sql1 = "INSERT INTO Tabelle (team_nr, spieltag, sieg, unentschieden, niederlage, punkte, tore, gegentore, heim)
             VALUES (:team1, :spieltag, :sieg, :unentschieden, :niederlage, :punkte, :tore1, :tore2, true)
             ON DUPLICATE KEY UPDATE sieg = :sieg, unentschieden= :unentschieden, niederlage = :niederlage, punkte = :punkte, tore = :tore1, gegentore = :tore2, heim = true";

    $statement = $g_pdo->prepare($sql1);
    $result = $statement->execute(array('team1' => $team1, 'spieltag' => $spieltag, 'sieg' => $sieg, 'unentschieden' => $unentschieden, 'niederlage' => $niederlage, 'punkte' =>
    $punkte, 'tore1' => $tore1, 'tore2' => $tore2));

    if ($result) {
      $error = 0;
    } else {
      $error = 1;
    }

    if ($tore1 < $tore2) {
      $sieg = 1;
      $unentschieden = 0;
      $niederlage = 0;
      $punkte = 3;
    } 
    if ($tore1 == $tore2) {
      $sieg = 0;
      $unentschieden = 1;
      $niederlage = 0;
      $punkte = 1;
    }
    if ($tore1 > $tore2) {
      $sieg = 0;
      $unentschieden = 0;
      $niederlage = 1;
      $punkte = 0;
    }

    $sql2 = "INSERT INTO Tabelle (team_nr, spieltag, sieg, unentschieden, niederlage, punkte, tore, gegentore, heim)
             VALUES (:team1, :spieltag, :sieg, :unentschieden, :niederlage, :punkte, :tore1, :tore2, false)
             ON DUPLICATE KEY UPDATE sieg = :sieg, unentschieden= :unentschieden, niederlage = :niederlage, punkte = :punkte, tore = :tore1, gegentore = :tore2, heim = false";

    $statement = $g_pdo->prepare($sql2);
    $result = $statement->execute(array('team1' => $team2, 'spieltag' => $spieltag, 'sieg' => $sieg, 'unentschieden' => $unentschieden, 'niederlage' => $niederlage, 'punkte' =>  
    $punkte,'tore1' => $tore2, 'tore2' => $tore1));

    if ($result) {
      $error = 0;
    } else {
      $error = 1;
    }

  }

  if ($error != 0) {
    echo "Es gab einen Fehler beim Aktualisieren";
  }
}



function update_rangliste($spieltag) {

  global $g_pdo;


  $sql = "SELECT user_nr, Tipps.tore1 as tipp1, Tipps.tore2 as tipp2, Ergebnisse.tore1, Ergebnisse.tore2 
          FROM `Tipps`, Ergebnisse WHERE (Tipps.spieltag = $spieltag) AND (Tipps.sp_nr = Ergebnisse.sp_nr) 
          AND (Tipps.spieltag = Ergebnisse.spieltag)";


  foreach ($g_pdo->query($sql) as $row) {
    $user_nr = $row['user_nr'];
    $user[$user_nr] = $user_nr;
    $tore1 = $row['tore1'];
    $tore2 = $row['tore2'];
    $tipp1 = $row['tipp1'];
    $tipp2 = $row['tipp2'];

    if (($tore1 == $tipp1) && ($tore2 == $tipp2)){
      $richtig[$user_nr] += 1;
      $differenz[$user_nr] += 0;
      $tendenz[$user_nr] += 0;
      $punkte[$user_nr] += 3;
    }
    
    elseif ($tore1 - $tore2 == $tipp1 - $tipp2){
      $differenz[$user_nr] += 1;
      $tendenz[$user_nr] += 0;
      $richtig[$user_nr] += 0;   
      $punkte[$user_nr] += 2;
    }

    elseif ((($tore1 - $tore2 > 0) && ($tipp1 - $tipp2 > 0)) || (($tore1 - $tore2 < 0) && ($tipp1 - $tipp2 < 0)) ){
    $tendenz[$user_nr] += 1;
    $differenz[$user_nr] += 0;
    $richtig[$user_nr] += 0;
    $punkte[$user_nr] += 1;
    }
    
  }

foreach ($user as $user_nr) {

    $sql2 = "INSERT INTO Rangliste (user_nr, spieltag, richtig, tendenz, differenz, punkte)
             VALUES (:user, :spieltag, :richtig, :tendenz, :differenz, :punkte)
             ON DUPLICATE KEY UPDATE richtig = :richtig, tendenz= :tendenz, differenz = :differenz, punkte = :punkte";

    $statement = $g_pdo->prepare($sql2);
    $result = $statement->execute(array('user' => $user_nr, 'spieltag' => $spieltag, 'richtig' => $richtig[$user_nr], 'differenz' => $differenz[$user_nr], 'tendenz' => $tendenz[$user_nr], 'punkte' => $punkte[$user_nr]));

}





}


?>
