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
            enabled: false
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
            min:1,
            max:<?php echo anz_user_wett(get_curr_wett()); ?>
        },
        tooltip:{
            x:{
                formatter: function(value, { series, seriesIndex, dataPointIndex, w }) {
                                return value+". Spieltag"
                            } 
            }
        }
        
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
        <?php echo hide_anything(); ?>        
</script>
 
 
 <?php
 get_platz();

 function get_platz(){
    global $g_pdo;
    
    $ret = "series: [";
    foreach (all_user(get_curr_wett()) as $user_nr => $name){
        $user[$user_nr] = $user_nr;
        
        $sql = "SELECT `platz` FROM `Rangliste` WHERE user_nr = $user_nr AND spieltag > 17 ORDER BY spieltag ASC";
        
        $str[$user_nr] = "{name: \"".$name."\",
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
    $str = "";
    
    $names = all_user_wett(get_curr_wett());
    
    foreach ($names as $id => $name  ) {

            $str .= "chart.hideSeries(\"".$name."\");
            ";
    }
    
    $str .= "chart.showSeries(\"".get_username()."\");";
    return $str;

}


function get_date(){
    $str =  "[";
    for ($i = 18; $i<= akt_spieltag(); $i++){
        $str .= "$i, ";
    }
    
    $str .= "],";
    
    return $str;
}

?>
