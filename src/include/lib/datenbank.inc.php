<?php

/*
 *SCRIPT ZUR VERBINDUNG ZUR DATENBANK
 *
 */


$db_host="rdbms.strato.de";

$db_ben="dbu377425";

$db_pass="&Z&32e-d4zH8fS&Tuu?y-ur4zx?yu32edTMS,568&ZrfSxMS,56H88";

$db_name="dbs2016292";




$g_pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_ben, $db_pass);


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
