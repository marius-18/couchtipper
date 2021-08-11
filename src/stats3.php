<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<html>
	<head>
	    <title>ApexCharts Testbeispiel-Scatter</title>
	    <style>
	        #chart {
	            margin: 50 auto;
	        }
	    </style>
	</head>
	<body >
	    <div id="chart">
	    </div>
	</body>
</html>
<script>
 var options = {
        <?php echo get_platz();?>
          chart: {
          height: 600,
          type: 'line',
          zoom: {
            enabled: true
          },
          animations:{
            enabled: false
          }
        },
        dataLabels: {
          enabled: true
        },
        stroke: {
          curve: 'straight'
        },
        title: {
          text: 'Saisonverlauf',
          align: 'left'
        },
        grid: {
          row: {
            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5
          },
        },
        xaxis: {
          categories: <?php echo get_date(); ?>
        },
        yaxis:{
            reversed: true,
        }
        
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
        <?php echo hide_anything(); ?>        
</script>
 
 
 <?php
function get_platz(){
    global $g_pdo;
    
    $sql = "SELECT user_nr FROM `Rangliste` WHERE 1 group by user_nr order by user_nr ASC";
    $ret = "series: [";
    foreach ($g_pdo->query($sql) as $row) {
        $user_nr = $row['user_nr'];
        $user[$user_nr] = $user_nr;
        
        $sql = "SELECT `platz` FROM `Rangliste` WHERE user_nr = $user_nr ORDER BY spieltag ASC";
        
        $str[$user_nr] = "{name: \"".get_username_from_nr($user_nr)."\",
        data:
        [";
        
        foreach ($g_pdo->query($sql) as $row) {
            $platz = $row['platz'];
        
            $str[$user_nr] .= "$platz ,";
        }
        $str[$user_nr] .= "]},";
        
        $ret .= $str[$user_nr];
    }
    
    $ret .= "],";
    
             
        
    return $ret;
}

function hide_anything(){
    //BÄÄH
    
    // vllt nicht in dem chart ding ausblenden, sondern generell nur 1-3 kurven plotten..
    // default mit eigener kurven
    // nach wunsch noch andere auswählen
    // ylim 1 - anzahl_spieler..
    $str = "";
    
    $names = all_user();
    
    foreach ($names as $id => $name  ) {

            $str .= "chart.hideSeries(\"".$name."\");
            ";
    }
    
    $str .= "chart.showSeries(\"".get_username()."\");";
    return $str;

}


function get_date(){
    $str =  "[";
    for ($i = 1; $i<= akt_spieltag(); $i++){
        $str .= "$i, ";
    }
    
    $str .= "],";
    
    return $str;
}

?>
