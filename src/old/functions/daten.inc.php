<?php
function sitzungen(){
   global $g_pdo;
   $user_id = get_usernr();

   if (isset($_GET['rem'])){
      $rem_id = $_GET['rem'];
      if (is_numeric($rem_id)){
         echo "Die ausgew&auml;hlte Sitzung wurde geschlossen.<br><br>";
         $sql = "DELETE FROM Security WHERE ((user_id = :user_id) AND (id = :rem_id)) ";
         $stmt = $g_pdo->prepare($sql);
         $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);	
         $stmt->bindParam(':rem_id', $rem_id, PDO::PARAM_INT);	
         $stmt->execute();
      }
   }

   $sql = "SELECT id, created_at, ort, land, geraet, last_change, identifier, land, ip FROM Security WHERE user_id = \"$user_id\" ORDER BY last_change DESC";

   foreach ($g_pdo->query($sql) as $row) {
      $id = $row['id'];
      $date = new DateTime($row['created_at']);
      $erstellt[$id] = $date -> format('d.m.y H:i');
      $ort[$id] = $row['ort'];
      $geraet[$id] = $row['geraet'];
      $date = new DateTime($row['last_change']);
      $letzt[$id] = $date -> format('d.m.y');
      $identifier[$id] = $row['identifier'];
      $land[$id] = $row['land'];
      $ip[$id] = $row['ip'];
   }


   echo "<table border = \"0\" align = \"center\">";
   echo "<tr>
         <td>Browser</td><td>Standort</td><td>Aktivit&auml;t</td><td></td><td></td>
         </tr>
        ";

   foreach ( $erstellt as $id => $anfang) {
      if ($identifier[$id] == $_COOKIE['identifier']){
         $farbe =  "bgcolor = \"darkred\" ";
         $akt = "Aktuelle Sitzung";
         $close = "";
      } else {
         $farbe = ""; 
         $akt = $letzt[$id];
         $close = "<a href = \"?index=13&rem=$id#13\"><img src = \"images/remove.svg\" height = \"20\" width = \"20\"></a>";
      }
 
      if ($ort[$id] == ", "){
         $standort = $land[$id];
      } else {
         $standort = $ort[$id]. " ".$land[$id]; 
      }

      echo "<tr $farbe>
           <td>".$geraet[$id]."</td>
           <td>".$standort."</td>
           <td>".$akt."</td>
           <td onclick = \"myFunction($id)\"><img src = \"images/info.gif\" height = \"20\" width = \"20\"></td>
           <td>$close</td>
           </tr>";

      echo "<tr><td colspan = \"5\" id = \"$id\" style = \"display: none\" bgcolor = \"lightgreen\">&nbsp;&nbsp;Angemeldet: ".$erstellt[$id]."<br>&nbsp;&nbsp;IP-Adresse: ".$ip[$id]."</td>
            </tr>
           ";
   }

   echo "</table>";

   echo "
   <script>
      function myFunction(wert) {
         if ( document.getElementById(wert).style.display == \"\"){
            document.getElementById(wert).style.display = \"none\";
         } else {
            document.getElementById(wert).style.display = \"\";
         }
      }
   </script>";

}

function name($mode){
   if ($mode == 0) {
      return get_username();
   } else {
   $str = "<form action=\"?index=13#13\" method=\"post\">
        <input type=\"text\" style=\"font-size:medium;\" name=\"name\" value =\"".get_username()."\"><br>
        <input type=\"Submit\" value=\"Enter\"><br>";
   return $str;
   }

}



function email($mode){
   global $g_pdo;
   $user_nr = get_usernr();
   $sql = "SELECT email FROM User WHERE user_nr = $user_nr";

   foreach ($g_pdo->query($sql) as $row) {
      $mail = $row['email'];
   }

   if ($mode == 0){
      return $mail;
   } else {
      $str = "<form action=\"?index=13#13\" method=\"post\">
        <input type=\"text\" style=\"font-size:medium;\" size = \"25\" name=\"mail\" value =\"$mail\"><br>
        <input type=\"Submit\" value=\"Enter\"><br>";
      return $str;
   }

}


function password($mode){
   if ($mode == 0) {
      return "*******";
   } else {
   $str = "<form action=\"?index=13#13\" method=\"post\"> <table align = \"center\">
        <tr><td>Altes Passwort:</td><td><input type=\"password\" style=\"font-size:medium;\" name=\"password_old\" value =\"\"></td></tr>
        <tr><td>Neues Passwort:</td><td> <input type=\"password\" style=\"font-size:medium;\" name=\"password\" value =\"\"></td></tr>
        <tr><td>Wiederholen:</td><td> <input type=\"password\" style=\"font-size:medium;\" name=\"password1\" value =\"\"></td></tr></table>
        <input type=\"Submit\" value=\"Enter\"><br>";
   return $str;
   }

}

function team(){
   global $g_pdo;
   $str = "<form action=\?index=13#13\" method=\"post\"><select name=\"team\">";
   $select = "";
   $sql = "SELECT team_name,team_nr FROM Teams ORDER BY team_nr ASC";

   foreach ($g_pdo->query($sql) as $row) {

      if ($row['team_nr'] == my_team()){
         $select = " selected";
      }

      $team = $row['team_name'];
      $team_nr = $row['team_nr'];
      $str .= "<option value=\"$team_nr\" $select>$team</option>";
      $select = "";
   }

   $str .= "</select><br>";

   $str .= "<input type=\"Submit\" value=\"Enter\"></form>";

   return $str;

}








?>
