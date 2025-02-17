<?php


function tabelle($modus, $beginn, $ende=NULL){
global $g_pdo;

$ort = "";
$heim = "";
$endzeit = "";
if ($modus == "Heim"){
  $ort = "AND (heim = 1)";
}

if ($modus == "Auswaerts"){
  $ort = "AND (heim = 0)";
}

if ($modus == "Hinrunde"){
  $heim = "AND (spieltag <= 17)";
}

$bereich_choosen = 0;
if ($modus == "Bereich"){
    list($beginn, $ende, $spieltag_bereich_form) = spieltag_start_ende();
    if (($beginn != 0) && ($ende != 0)){
        $bereich_choosen = 1;
    }
    $beginn--;
} else {
    $spieltag_bereich_form = "";   
}

if ($ende !== NULL){
  $endzeit = "AND (spieltag <= $ende)";
}


$sql = "SELECT sum(tore) as tore, sum(gegentore) as gegentore, sum(punkte) as punkte,
               sum(sieg) as sieg, sum(niederlage) as niederlage, sum(unentschieden) as unentschieden,
               team_name, Teams.team_nr
        FROM `Tabelle`, Teams 
        WHERE ((Teams.team_nr = Tabelle.team_nr)  AND (spieltag > $beginn) $ort $heim $endzeit)
        GROUP BY Teams.team_nr";



  foreach ($g_pdo->query($sql) as $row) {
    $team_nr = $row['team_nr'];
    $tore[$team_nr] = $row['tore'];
    $gegentore[$team_nr] = $row['gegentore'];
    $diff[$team_nr] = $row['tore'] - $row['gegentore'];
    $punkte[$team_nr] = $row['punkte'];
    $sieg[$team_nr] = $row['sieg'];
    $niederlage[$team_nr] = $row['niederlage'];
    $unentschieden[$team_nr] = $row['unentschieden'];
    $team_name[$team_nr] = $row['team_name'];
    $nummer[$team_nr] = $team_nr;

  }

if (!isset($punkte)){ 
  ## Falls die Rückrunde noch nicht gestartet ist, sind die arrays nicht vorhanden..
  ## Deshalb Abbruch, damit array_multisort nicht fehlschlägt
  
  ##return schlägt hier fehl beim Tabellen Bereich.. deswegen Versuch!
    return(array([],[],[],[],[],[],[],[],$modus,[],$spieltag_bereich_form,$bereich_choosen));
}
array_multisort($punkte, SORT_DESC, $diff, SORT_DESC, $tore, SORT_DESC, $gegentore, SORT_ASC, $team_name, $niederlage, $sieg, $unentschieden, $nummer);


return (array($punkte, $tore, $gegentore, $diff, $team_name, $sieg, $unentschieden, $niederlage, $modus, $nummer, $spieltag_bereich_form, $bereich_choosen));


}


function wm_tabelle($gruppe){
   global $g_pdo;

   $sql = "SELECT sum(tore) as tore, sum(gegentore) as gegentore, sum(punkte) as punkte,
                  sum(sieg) as sieg, sum(niederlage) as niederlage, sum(unentschieden) as unentschieden,
                  team_name, Teams.team_nr AS team_nr, gruppe, position
           FROM `Tabelle`, Teams 
           WHERE ((Tabelle.team_nr = Teams.team_nr) AND gruppe = '$gruppe')
           GROUP BY Teams.team_nr";

   foreach ($g_pdo->query($sql) as $row) {
      $team_nr = $row['team_nr'];
      $tore[$team_nr] = $row['tore'];
      $gegentore[$team_nr] = $row['gegentore'];
      $diff[$team_nr] = $row['tore'] - $row['gegentore'];
      $punkte[$team_nr] = $row['punkte'];
      $sieg[$team_nr] = $row['sieg'];
      $niederlage[$team_nr] = $row['niederlage'];
      $unentschieden[$team_nr] = $row['unentschieden'];
      $team_name[$team_nr] = $row['team_name'];
      $team_nr_a[$team_nr] = $team_nr;
      $position[$team_nr] = $row['position'];
   }
   


   array_multisort($punkte, SORT_DESC, $position, SORT_ASC, $diff, SORT_DESC, $tore, SORT_DESC, $gegentore, $team_name, $niederlage, $sieg, $unentschieden, $team_nr_a);
    
    
    //tie breaking.. 
    // hier ist die tabelle sortiert hier muss noch gecheckt werden was bei punktgleichheit ist.
    
    
    //tie_breaking(array($punkte, $tore, $gegentore, $diff, $team_name, $sieg, $unentschieden, $niederlage, $gruppe, $team_nr_a));
    // ok hab eingesehen, der scheiß ist zu schwer...
    
    
   return (array($punkte, $tore, $gegentore, $diff, $team_name, $sieg, $unentschieden, $niederlage, $gruppe, $team_nr_a));
}



function tie_breaking($args){

    list($punkte, $tore, $gegentore, $diff, $team_name, $sieg, $unentschieden, $niederlage, $gruppe, $team_nr_a) = $args;

    $equal = array(0 => array());
    $alt_punkt = $punkte[0];
    $i = 0;
    for ($key = 0; $key < count($punkte); $key+= 1){
        $poi = $punkte[$key];
        echo "punkte: $poi, key: $key<br>";
        
        //Abteilung hässlich....
        if ($poi == $alt_punkt){
            array_push($equal[$i], $key);
        } else {
            $i += 1;
            array_push($equal, array());
            array_push($equal[$i], $key);
        }
        $alt_punkt = $poi;
    }

    foreach ($equal as $key => $teams){       
        if (count($teams) > 1){
            echo "schlüssel: $key, $teams <br>";
            direkter_vergleich($teams, $team_nr_a);
        }
    }
    
    print_r($equal);



    return array($punkte, $tore, $gegentore, $diff, $team_name, $sieg, $unentschieden, $niederlage, $gruppe, $team_nr_a);

}

function direkter_vergleich($teams, $team_nr_a){

echo "punktgleiche Teams: ";
    foreach ($teams as $key){
        echo "$key (".$team_nr_a[$key]."), ";
        
    }
echo "<br>";
}


function print_tabelle($args, $id, $show){

list($punkte, $tore, $gegentore, $diff, $team_name, $sieg, $unentschieden, $niederlage, $modus, $nummer, $spieltag_bereich_form, $bereich_choosen) = $args;

echo "<div class=\"container\" id=\"$id\" style=\"display: $show;\">";

if ($modus == "Heim") {
  echo "<b>Heimtabelle</b>";
}
 elseif ($modus == "Auswaerts"){
  echo "<b>Auswärtstabelle</b>";
}
 elseif ($modus == "Tendenz"){
  echo "<b>Tendenz</b>";
}
 elseif ($modus == "Hinrunde"){
  echo "<b>Hinrunde</b>";
}
 elseif ($modus == "Rückrunde"){
  echo "<b>R&uuml;ckrunde</b>";
}
 elseif ($modus == "Bereich"){
  echo "<b>Bereich</b>";
}
 elseif ($modus == "") {
  echo "<b>Tabelle</b>";
}

echo $spieltag_bereich_form;

if (!isset($punkte)){
  echo "<br><b>Die Tabelle ist noch nicht vorhanden</b></div>";
  return;
}

echo "<div class=\"table-responsive\">";
echo "<table class=\"table table-sm  table-hover text-center center\">";
echo "<tr bgcolor=\"#B6B6B4\"><th>Pl</th><th>Team</th><th>Sp</th><th>S</th><th>U</th><th>N</th><th>Tore</th><th class=\"d-none d-sm-table-cell\">Diff</th><th>Pkt</th></tr>";

$a = 1;

foreach ($team_name AS $i => $team){

  if ($i < 4) {
    $color = "class=\"table-success\"";
  }
  if ($i == 4) {
    $color = "class=\"table-info\"";
  }
  if ($i == 5) {
    $color = "class=\"table-primary\"";
  }
  if ($i == 6) {
   $color = "";
   }
  if ($i > 6) {
    $color = "";
  }
  if ($i == 15) {
    $color = "class=\"table-warning\"";
  }
  if ($i > 15) {
    $color = "class=\"table-danger\"";
  }

  $spiele = $sieg[$i] + $unentschieden[$i] + $niederlage[$i];

   echo " 
<tr $color> 
<th>$a</th> 
<th nowrap>$team</th>
<th>$spiele</th> 
<th>$sieg[$i]</th> 
<th>$unentschieden[$i]</th> 
<th>$niederlage[$i]</th> 
<th>$tore[$i]:$gegentore[$i]</th> 
<th class=\"d-none d-sm-table-cell\">$diff[$i]</th> 
<th>$punkte[$i]</th> 
</tr>";
$a++;
}


echo "</table></div></div>";


    if ($bereich_choosen){
        // Schalte direkt auf die Bereich Tabelle um!
        echo "<script>rank_ausblenden($id);</script>";
    }

}


function print_wm_tabelle($args){
    list($punkte, $tore, $gegentore, $diff, $team_name, $sieg, $unentschieden, $niederlage, $gruppe) = $args;

    //echo "<div class=\"container-fluid bg-warning\">";
    //echo "<b>Gruppe $gruppe</b>";

    echo "<div class=\"table-responsive\">";
    echo "<table class=\"table table-sm  table-hover text-center center\">";
    echo "<tr class=\"thead-dark\"><th>Pl</th><th>Team</th><th>Sp</th><th>S</th><th>U</th><th>N</th><th>Tore</th><th>Diff</th><th>Pkt</th></tr>";

    $a = 1;

    foreach ($team_name AS $i => $team){

        if ($i < 2) {
            $color = " class = \"table-success\"";
        } else {
            $color = "";
        }

        $spiele = $sieg[$i] + $unentschieden[$i] + $niederlage[$i];

        echo " 
            <tr$color> 
            <td>$a</td> 
            <td>$team</td>
            <td>$spiele</td> 
            <td>$sieg[$i]</td> 
            <td>$unentschieden[$i]</td> 
            <td>$niederlage[$i]</td> 
            <td>$tore[$i]:$gegentore[$i]</td> 
            <td>$diff[$i]</td>
            <td>$punkte[$i]</td> 
            </tr>";
    
        $a++;
    }

    echo "</table><br>";


    echo "</div>";
}



function tabelle_verlauf(){
    global $g_pdo;
    $sql = "SELECT DISTINCT team_nr, team_name FROM `Teams`, Spieltage WHERE team_nr = team1 ORDER BY `Teams`.`team_nr` ASC";
    
    foreach ($g_pdo->query($sql) as $row) {
        $team_nr    = $row['team_nr'];
        $team_name  = $row['team_name'];
        $names[$team_nr] = $team_name;
        $logos[$team_nr] = "<img src=\"images/Vereine/$team_nr.gif\" width=\"30px\">";
        $nums[$team_nr] = $team_nr;
    }

    $today = akt_spieltag();
    $sql = "SELECT team_nr, platz, spieltag FROM `Tabelle` WHERE spieltag > 0 AND spieltag <= $today ORDER BY `team_nr` ASC, `spieltag` ASC";
    
    foreach ($g_pdo->query($sql) as $row) {
        $team_nr    = $row['team_nr'];
        $spieltag    = $row['spieltag'];
        $platz[$spieltag][$team_nr] = $row['platz'];
        
        ## TODO: erste Idee: nur beim aktuellem Spieltag == today..
        ## Problem: bei laufendem Spieltag ist Array nicht voll
        ## Einfach Alles immer überschreiben?
        ## TODO: könnte Problem bei Spieltag 1 werden!
        $my_curr_platz[$team_nr] = $row['platz'];
    }
    
    ## Falls Team Nummern noch nicht sortiert sind:
    #array_multisort($nums);
    
    ## Sortiere Die Zahlen nach Reihenfolge der Tabelle
    array_multisort($my_curr_platz, SORT_ASC, $nums);
    
    return array($nums, $logos, $platz, $names);
}

function print_tabelle_verlauf($args, $id, $show){
    list($nums, $logos, $platz) = $args;
    echo "<div class=\"container\" id=\"$id\" style=\"display: $show;\">";

    echo "<b>Tabellenverlauf</b>";

    echo "
    <div class=\"table-responsive\">
    
    <table data-toggle=table data-order=1 data-sort-order=\"asc\"
           class=\"table table-sm   table-hover text-center center text-nowrap\" align=\"center\">
        
    <thead style=\"position: sticky;top: 100;\" class=\"thead-dark data-sticky-header\">
    <tr>
        <th data-fixed-columns=\"true\"></th>";
    
    foreach ($platz as $index => $abcd){
        echo "<th data-field=$index  data-sort-order=asc data-sortable=true>$index</th>";
    }
    echo "<th></th>";
    echo "</tr></thead>";

    foreach ($nums as $index1 => $i){
        echo "<td >".$logos[$i]."</td>";
        
        foreach ($platz as $index => $abcd){
            if ($platz[$index][$i] == 1){
                $color = "class=\"table-success\"";
            } elseif (($platz[$index][$i] == 2) || ($platz[$index][$i] == 3) || ($platz[$index][$i] == 4)){
                $color = "class=\"table-primary\"";
            } elseif ($platz[$index][$i] == 5){
                $color = "class=\"table-info\"";
            } elseif ($platz[$index][$i] == 6){
                $color = "class=\"table-secondary\"";
            } elseif ($platz[$index][$i] == 16){
                $color = "class=\"table-warning\"";
            } elseif (($platz[$index][$i] == 17) || ($platz[$index][$i] == 18)){
                $color = "class=\"table-danger\"";
            } else {
                $color = "";                
            }
            
            echo "<td $color>".$platz[$index][$i]."</td>";
        }
        
        echo "<td>".$logos[$i]."</td>";
        echo "</tr>";
    }
    
    echo "</table></div></div>";
}


function print_tabelle_verlauf_chart($args, $id, $show){
    list($nums, $logos, $platz, $names) = $args;
    
    #list($nums, $logos, $platz, $names) = tabelle_verlauf();
    
    foreach ($nums as $index => $team_id){
        $my_pos[$team_id] = "[";
    }
    
    $spieltage = "[";
    
    foreach ($platz as $index => $team_id){
        $spieltage .= $index. ",";
        
        foreach ($team_id as $team_nr => $platz){
            $my_pos[$team_nr] .= "".$platz. ",";
        }
    }
    
    foreach ($nums as $index => $team_id){
        $my_pos[$team_id] = substr($my_pos[$team_id], 0, -1);
        $my_pos[$team_id] .= "]";
    }
    
    $spieltage = substr($spieltage, 0, -1);
    $spieltage .= "]";

    
$colors = [
    "rgb(0, 200, 0)",    // 1 → Kräftiges Grün (Sehr gut)
    "rgb(40, 180, 0)",   // 2 → Sattes Grün
    "rgb(80, 160, 0)",   // 3 → Dunkleres Grün
    "rgb(120, 220, 0)",  // 4 → Frisches Gelbgrün
    "rgb(180, 255, 0)",  // 5 → Intensiveres Gelbgrün (statt 160,255,0)
    "rgb(230, 255, 70)", // 6 → Satteres Gelb mit Grünanteil (statt 200,255,50)
    "rgb(255, 255, 0)",  // 7 → Standard-Gelb (kräftiger)
    "rgb(255, 220, 0)",  // 8 → Kräftiges Sonnen-Gelb
    "rgb(255, 190, 0)",  // 9 → Sattes Goldgelb
    "rgb(255, 160, 0)",  // 10 → Dunkleres Goldgelb
    "rgb(255, 130, 0)",  // 11 → Stärkeres Orange
    "rgb(255, 100, 0)",  // 12 → Intensives Orange
    "rgb(255, 70, 0)",   // 13 → Kräftiges Rot-Orange
    "rgb(255, 50, 20)",  // 14 → Leuchtendes Rot-Orange
    "rgb(255, 30, 40)",  // 15 → Knalliges Rot
    "rgb(220, 0, 0)",    // 16 → Dunkleres Rot
    "rgb(180, 0, 0)",    // 17 → Sehr dunkles Rot
    "rgb(140, 0, 0)"     // 18 → Tiefrot (Letzter Platz)
];

    
    $dataset = "";
    foreach ($nums as $index => $team_id){            
        $color = $colors[$index % count($colors)];
        
        $dataset .= "{";
        $dataset .= "data: " . $my_pos[$team_id]. ", ";
        $dataset .= "borderColor: \"$color\", ";
        $dataset .= "fill: false,";
        $dataset .= "lineTension: 0, "; // Set lineTension to 0 for "sharp" edges
        $dataset .= "label: \"".$names[$team_id]."\"";
        $dataset .= "},";
    }
    $dataset = substr($dataset, 0, -1);        
  
    $chart_div =  "
        <script src=\"https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js\"></script>
      
        <div class=\"container container-fluid\" id=\"".$id."\" style=\"display: ".$show.";\">
          <div class=\"chart-container\" style=\"overflow-x: auto; width: 100%; height: 500px;\">
            <div style=\"width: 900px; height: 100%;\">
              <canvas id=\"myChart\"></canvas>
            </div>
        </div>
    </div>
      ";
    
    $chart_js = "
      <script>
        const xValues = ".$spieltage."
  
        new Chart(\"myChart\", {
          type: \"line\",

          data: {
            labels: xValues,
            datasets: [".$dataset."]
          },
          
          options: {
            legend: {
              display: true,
              position: 'bottom',
              onClick: function(e, legendItem) {
                var index    = legendItem.datasetIndex;
                var chart    = this.chart;
                var datasets = chart.data.datasets;
                
                // Originalfarben speichern, falls noch nicht geschehen
                if (!chart.originalColors) {
                  chart.originalColors = datasets.map(ds => ds.borderColor);
                }
                
                // Falls das gleiche Team erneut angeklickt wird → Zurücksetzen auf Originalfarben
                if (chart.highlightedIndex === index) {
                  datasets.forEach((dataset, i) => {
                    dataset.borderColor = chart.originalColors[i];
                    dataset.borderWidth = 2; // Standardbreite
                  });
                  
                  chart.highlightedIndex = null; // Kein Team mehr hervorgehoben
                } else {
                  // Neues Team hervorheben
                  datasets.forEach((dataset, i) => {
                    if (i === index) {
                      dataset.borderColor = \"rgb(0, 0, 0)\"; // Schwarze Hervorhebung
                      dataset.borderWidth = 4; // Dickere Linie
                    } else {
                      dataset.borderColor = chart.originalColors[i]; // Abgeschwächte Farbe
                      dataset.borderWidth = 1;
                      dataset.opacity = 0.3;
                    }
                  });
                  
                  chart.highlightedIndex = index; // Speichern, welches Team aktiv ist
                }
                
                chart.update();
              }
            },
            
            title: {
              display: true,
              text: 'Tabellenverlauf',
              color: '#911'
            },
            
            scales: {
              y: {
                min: 1, 
                max: 18,
              },
              
              yAxes: [{
                ticks: {
                  reverse: true,
                  stepSize: 1,
                }
              }]
            }
          }
        });
      </script>
    ";

    echo $chart_div;
    echo $chart_js;
}



function print_kreuztabelle($args, $id, $show){
    list($nums, $logos, $platz) = $args;
    
    $ergebnisse = get_all_ergebnissse();
    echo "<div class=\"container\" id=\"$id\" style=\"display: $show;\">";

    echo "<b>Kreuztabelle</b>";

    echo "
    <div class=\"table-responsive\">
    
    <table class=\"table table-sm table-bordered text-center center text-nowrap\" align=\"center\">";
        
    
    ## Erste Zeile: Team Logos
    echo "<tr><th class=\"table-active\"></th>";
    $spalte = 1;
    foreach ($nums as $index => $my_team_id){
        echo "<th class=\"kreuztab_col$spalte align-middle table-active\" onmouseover=\"highlight_kreuztabelle(0, $spalte)\">".$logos[$my_team_id]."</th>";
        $spalte ++;
    }
    echo "<th class=\"table-active\"></th></tr>";
    
    ## Jetzt Alle Ergebnisse/Daten ausgeben
    $zeile = 1;
    foreach ($nums as $index1 => $heim){
        $spalte = 1;
        echo "<tr id=\"kreuztab_row$zeile\" class=\"align-middle\">";
        
        // Linkeste Spalte: Team Logos!
        echo "<td class=\"table-active align-middle\" onmouseover=\"highlight_kreuztabelle($zeile, 0)\">".$logos[$heim]."</td>";
        
        foreach ($nums as $index => $ausw){
            if ($heim != $ausw){
                ## Nicht auf der Diagonalen!
                echo "<td class=\"kreuztab_col$spalte align-middle\" onmouseover=\"highlight_kreuztabelle($zeile, $spalte)\">".$ergebnisse[$heim][$ausw]."</td>";
            } else {
                ## Diagonale leer lassen
                echo "<td class=\"kreuztab_col$spalte table-secondary\" onmouseover=\"highlight_kreuztabelle($zeile, $spalte)\"></td>";
            }
            $spalte ++;
        }

        // Rechteste Spalte: Team Logos!
        echo "<td class=\"table-active\" onmouseover=\"highlight_kreuztabelle($zeile, 0)\">".$logos[$heim]."</td>";
        
        echo "</tr>";
        $zeile ++;
    }
    
    ## Letzte Zeile: Team Logos
    echo "<tr><th class=\"table-active\"></th>";
    $spalte = 1;
    foreach ($nums as $index => $abcd){
        echo "<th class=\"kreuztab_col$spalte align-middle table-active\"  onmouseover=\"highlight_kreuztabelle(0, $spalte)\">".$logos[$abcd]."</th>";
        $spalte ++;
    }
    echo "<th class=\"table-active\"></th></tr>";
    
    echo "</table></div></div>";
}


?>
