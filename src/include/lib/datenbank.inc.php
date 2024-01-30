<?php

/*
 *SCRIPT ZUR VERBINDUNG ZUR DATENBANK
 *
 */

 
 //hole credentials aus der auth-datenbank
 

$db_host="rdbms.strato.de";

$db_ben=get_wettbewerb_db_user();

$db_pass=get_wettbewerb_db_pw();

$db_name=get_wettbewerb_db_name();



$g_pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_ben, $db_pass);


function new_db_connection($array){
  global $g_pdo;
  $g_pdo = null;
  
  $db_host="rdbms.strato.de";
  
  $db_ben=get_wettbewerb_db_user($array);

  $db_pass=get_wettbewerb_db_pw($array);

  $db_name=get_wettbewerb_db_name($array);

  $g_pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_ben, $db_pass);
}

/*


// langsam aber sicher -->

$stmt = $g_pdo->prepare("SELECT user_nr,user_name FROM User WHERE team = :username AND user_nr = :usernr ORDER BY user_nr ASC;");
$params = array('username' => '12', 'usernr' => '2');
$stmt->execute($params);
$stmt = $stmt->fetchAll();

  foreach ($stmt as $entry) { 
      echo '<div class="name">'.$entry['user_nr']. $entry['user_name'].'</div>';
      echo '<hr />';


}



// _AUCH GUT --->
$sql = "";
$result = $g_pdo->query($sql);
      if ($result != true){
         $error_count++;
      }



// SCHNELL -->


$sql = 'SELECT user_name FROM User';
foreach ($g_pdo->query($sql) as $row) {
  print $row['user_name'] . "\t";

}
*/

$abcdef = "HALLOOOOO";

?>
