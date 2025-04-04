 

<?php
echo "<script src=\"https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js\"></script>
";
function get_table_verlauf_data($mode){
    global $g_pdo;
    if ($mode == "Hinrunde"){
        $spt_bound = "< 18";
    } else {
        $spt_bound = ">= 18";
    }
    try {
        // SQL-Abfrage zum Abrufen der Daten
        $sql = "SELECT user_nr, spieltag, platz FROM Rangliste WHERE spieltag $spt_bound";
        $stmt = $g_pdo->prepare($sql);
        $stmt->execute();

        // Initialisiere das Array
        $my_array = [];

        // Daten abrufen und ins Array speichern
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = $row['user_nr'];
            $spieltag = $row['spieltag'];
            $platz = $row['platz'];
        
            // Speichere die Platzierung im Array
            $my_array[$spieltag][$user] = $platz;
            $names[$user] = get_full_name_from_nr($user)[2];
            $nums[$user] = $user;
        
        }

    } catch (PDOException $e) {
        echo "Fehler: " . $e->getMessage();
    }
    
    return array($nums, [], $my_array, $names);
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
        <div class=\"container container-fluid\" id=\"".$id."\" style=\"display: ".$show.";\">
          <div class=\"chart-container\" style=\"overflow-x: auto; width: 100%;\">
            <div style=\"width: 900px; height:700px\">
              <canvas id=\"myChart".$id."\"></canvas>
            </div>
        </div>
    </div>
      ";
    
    $chart_js = "
      <script>
        xValues = ".$spieltage."
  
        new Chart(\"myChart".$id."\", {
          type: \"line\",

          data: {
            labels: xValues,
            datasets: [".$dataset."]
          },
          
          options: {
	    maintainAspectRatio: false, // Deaktiviert automatisches Seitenverhältnis
	    
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
              yAxes: [
	      {
		id: \"yLeft\",
		position: \"left\",
                ticks: {
                  reverse: true,
                  stepSize: 1,
		  beginAtZero: false,
		  callback: function(value, index, values) {
                        return value; // Erzwingt das Anzeigen jedes einzelnen Wertes
		  },
		  min: 1,
		  max: ".count($names)."
		}
              },
	      {
	        id: \"yRight\",
                position: \"right\",
                ticks: {
                  reverse: true,
                  stepSize: 1,
		  beginAtZero: false,
		  callback: function(value, index, values) {
                        return value; // Erzwingt das Anzeigen jedes einzelnen Wertes
		  },
		  min: 1,
		  max: ".count($names)."
                }
              },
	      ],
            }
          }
        });
      </script>
    ";

    echo $chart_div;
    echo $chart_js;
}

?>
