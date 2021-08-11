  <link href="https://cdn.anychart.com/releases/v8/css/anychart-ui.min.css" rel="stylesheet" type="text/css">
  <link href="https://cdn.anychart.com/releases/v8/fonts/css/anychart-font.min.css" rel="stylesheet" type="text/css">
  
  <style>

#container {
  width: 100%;
  height: 1000px;
  margin: 0;
  padding: 0;
}


</style>


  <div id="container"></div>
  
  <script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>
  <script src="https://cdn.anychart.com/releases/v8/js/anychart-ui.min.js"></script>
  <script src="https://cdn.anychart.com/releases/v8/js/anychart-exports.min.js"></script>
  <script type="text/javascript">
  
  
  anychart.onDocumentReady(function () {
  // create data set on our data
  var dataSet = anychart.data.set(getData());

<?php echo get_user(1); ?>


  // create line chart
  var chart = anychart.line();

  // turn on chart animation
  chart.animation(true);

  // set chart padding
  chart.padding([10, 20, 15, 20]);

  // turn on the crosshair
  chart.crosshair().enabled(true).yLabel(false).yStroke(null);

  // set tooltip mode to point
  chart.tooltip().positionMode('point');

  // set chart title text settings
  chart.title(
    'Verlauf der Ranglisten Positionen.'
  );
chart.xGrid().enabled(true);
chart.yGrid().enabled(true);
  // set yAxis title
  //chart.yAxis().title('Number of Bottles Sold (thousands)');
  
chart.xAxis().labels().padding(5);
chart.background().fill("transparent");
chart.yScale().inverted(true);

<?php echo get_user(2); ?>


  // turn the legend on
  chart.legend().enabled(true).fontSize(13).padding([0, 0, 10, 0]);

  // set container id for the chart
  chart.container('container');
  // initiate chart drawing
  chart.draw();
});



function getData() {
<?php echo get_data(); ?>
}</script>

<?php
function get_platz($spieltag){
    global $g_pdo;
    $sql = "SELECT `user_nr`,`spieltag`,`platz` FROM `Rangliste` WHERE spieltag = $spieltag ORDER BY spieltag ASC, user_nr ASC";
    $str = "['$spieltag'";
    foreach ($g_pdo->query($sql) as $row) {
        $user_nr = $row['user_nr'];
        $spieltag = $row['spieltag'];
        $platz = $row['platz'];
        
        $str .= ", $platz";
    }
    $str .= "]";
    return $str;
}

function get_data(){
    $str =  "return [
";
    for ($i = 1; $i<= akt_spieltag(); $i++){
        $str .= get_platz($i);
        if ($i != akt_spieltag()) {
            $str .= ", 
";
        }
    }
    $str .= "];";
    
    return $str;
}

function get_user($id){
    global $g_pdo;
    $sql = "SELECT user_nr FROM `Rangliste` WHERE 1 group by user_nr order by user_nr ASC";
    $count = 1;
    $str = "";
    $str1 = "";
    foreach ($g_pdo->query($sql) as $row) {
        $user_nr = $row['user_nr'];
        $str1 .=  "    var SeriesData$count = dataSet.mapAs({ x: 0, value: $count });
";
                
        $str .=   "    
    var Series$count = chart.line(SeriesData$count);
    Series$count.name('".get_username_from_nr($user_nr)."');
    Series$count.hovered().markers().enabled(true).type('circle').size(4);
    Series$count
      .tooltip()
      .position('right')
      .anchor('left-center')
      .offsetX(5)
      .offsetY(5);
                        
";
        $count += 1;
        if ($count == 21){
            break;
            }

    }
  

if($id == 1){
    return $str1;
}
 if($id == 2){
    return $str;
}   
}

?>


 </body>
</html>
