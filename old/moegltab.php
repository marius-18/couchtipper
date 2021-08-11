<?php
include "funktion.php";
$user=$_COOKIE['username'];

if ($user==""){
$user="Marius";
}


$i=true;
$num=2;

while ($i==true){
 $abfrage=mysql_query("SELECT UName FROM user WHERE UNr='$num'");

 while ($row=mysql_fetch_object($abfrage)) {
  $names[$num]=$row->UName;
 }

 if (!isset($names[$num])){
  $i=false;
 } else {
   if ($names[$num]==$user){
    $diezahl=$num;
    }
   }

 $num++;
}

?>




<link href='style.css' rel='stylesheet'/>
<div class="middleleft"><a href='#' onclick='slider3.prev();return false;'><- Vor</a></div>
<div class="middleright"><a href='#' onclick='slider3.next();return false;'>Weiter -></a></div><br>



<div id='slider3' class='swipe'>
  <ul>
   <li style='display:block'>
     <div>
      <?php
       echo "Tipp-Tabelle von: <b>$names[$diezahl]</b><br>";
       tipptabelle($diezahl);
      ?>
     </div>
    </li>

<?php
for ($a=2; $a<count($names)+2; $a++){
if ($a==$diezahl){
$a++;
if ($a==count($names)+2){
break;
}
}
echo"
    <li style='display:none'>
     <div>";
       echo "Tipp-Tabelle von: <b>$names[$a]</b><br>";
       tipptabelle($a);
      
     echo"</div>
    </li>";
}
?>    
  </ul>
</div>



<script src='swipe.js'></script>
<script>
var slider3 = new Swipe(document.getElementById('slider3'));
</script>
<br>