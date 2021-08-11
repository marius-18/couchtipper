<?php
function sitzungen(){
    global $g_pdo;
    $user_id = get_usernr();

    if (isset($_GET['rem'])){
        $rem_id = $_GET['rem'];
        $session_msg = rm_session($rem_id);
    }

    list ($erstellt, $ort, $geraet, $letzt, $identifier, $land, $ip) = ls_sessions();

    
    echo "<table class=\"table\" id=\"session\">";
    echo "<tr class=\"thead-dark\">
            <th>Browser</th><th>Standort</th><th>Aktivit&auml;t</th><th></th><th></th>
            </tr>
            ";

   foreach ( $erstellt as $id => $anfang) {
      if ($identifier[$id] == $_COOKIE['identifier']){
         $farbe =  "class = \"table-success\"  ";
         $akt = "Aktuelle Sitzung";
         $close = "";
      } else {
         $farbe = ""; 
         $akt = $letzt[$id];
         $close = "<a href = \"?index=7&rem=$id#session\"><img src = \"images/remove.svg\" height = \"20\" width = \"20\"></a>";
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
   
   if ($session_msg != ""){
        echo "<br><div class=\"alert alert-success\">$session_msg</div>";
   }

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
   $str = "<form action=\"?index=7#7\" method=\"post\">
        <input type=\"text\" style=\"font-size:medium;\" name=\"name\" value =\"".get_username()."\"><br>
        <input type=\"Submit\" value=\"Enter\"><br>";
   return $str;
   }

}



function email($mode){
   global $g_pdo;
   $user_nr = get_usernr();

   $mail = get_mail();

   if ($mode == 0){
      return $mail;
   } else {
      $str = "<form action=\"?index=7#7\" method=\"post\">
        <input type=\"text\" style=\"font-size:medium;\" size = \"25\" name=\"mail\" value =\"$mail\"><br>
        <input type=\"Submit\" value=\"Enter\"><br>";
      return $str;
   }

}


function password($mode){
   if ($mode == 0) {
      return "*******";
   } else {
   $str = "<form action=\"?index=7#7\" method=\"post\"> <table align = \"center\">
        <tr><td>Altes Passwort:</td><td><input type=\"password\" style=\"font-size:medium;\" name=\"password_old\" value =\"\"></td></tr>
        <tr><td>Neues Passwort:</td><td> <input type=\"password\" style=\"font-size:medium;\" name=\"password\" value =\"\"></td></tr>
        <tr><td>Wiederholen:</td><td> <input type=\"password\" style=\"font-size:medium;\" name=\"password1\" value =\"\"></td></tr></table>
        <input type=\"Submit\" value=\"Enter\"><br>";
   return $str;
   }

}

function team(){
   global $g_pdo;
   $str = "<form action=\?index=7#7\" method=\"post\"><select name=\"team\">";
   $select = "";
   $sql = "SELECT team_name,team_nr FROM Teams WHERE team_nr<33 ORDER BY team_nr ASC";

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
