<?php

function rechte(){
   global $g_pdo;

   $userid = get_usernr();
   $sql = "SELECT user_name, tipps_aendern, erg_aendern, datum_aendern, user_verwaltung, user_nr FROM `User` WHERE 1 ORDER BY user_nr";
   foreach ($g_pdo->query($sql) as $allow){

      $nr = $allow['user_nr'];

      $name = $allow['user_name'];
      $tipps = $allow['tipps_aendern'];
      $erg = $allow['erg_aendern'];
      $datum = $allow['datum_aendern'];
      $user = $allow['user_verwaltung'];

      if ($tipps){
         $chk_tipps = " checked";
      } else {
         $chk_tipps = " ";
      }

      if ($erg){
         $chk_erg = " checked";
      } else {
         $chk_erg = " ";
      }

      if ($datum){
         $chk_datum = " checked";
      } else {
         $chk_datum = " ";
      }

      if ($user){
         $chk_user = " checked";
      } else {
         $chk_user = " ";
      }

      $output[$nr] = "<tr align = \"center\"><td>$name</td>
                <td><input type=\"checkbox\" name=\"tipps$nr\" value = \"1\" $chk_tipps></td>
                <td><input type=\"checkbox\" name=\"erg".$nr."\" value = \"1\" $chk_erg></td>
                <td><input type=\"checkbox\" name=\"datum$nr\" value = \"1\" $chk_datum></td>
                <td><input type=\"checkbox\" name=\"user$nr\" value = \"1\" $chk_user></td></tr>
               ";


      }

   return $output ;

}


function rechte_update(){
   global $g_pdo; 
   
   if (($_POST['secure'] != "") && allow_verwaltung()){
      $sql = "SELECT user_nr FROM `User` WHERE 1 ORDER BY user_nr";
      foreach ($g_pdo->query($sql) as $allow){
         $nr = $allow['user_nr'];

         $erg = $_POST['erg'.$nr.''];
         if (!$erg){$erg = 0;}

         $datum = $_POST['datum'.$nr.''];
         if (!$datum){$datum = 0;}

         $tipps = $_POST['tipps'.$nr.''];
         if (!$tipps){$tipps = 0;}

         $user = $_POST['user'.$nr.''];
         if (!$user){$user = 0;} 

         $insert = $g_pdo->prepare("UPDATE User SET erg_aendern = :erg, tipps_aendern = :tipps, datum_aendern = :datum, user_verwaltung = :user WHERE user_nr = :nr");
         $params = array('erg' => $erg, 'tipps' => $tipps, 'datum' => $datum, 'user' => $user, 'nr' => $nr);
         $insert->execute($params);
      }
   }

}




function paid() {
   global $g_pdo;

   $sql = "SELECT user_nr, user_name, hin, rueck FROM `User` WHERE 1 ORDER BY user_nr";
   foreach ($g_pdo->query($sql) as $allow){
      $nr = $allow['user_nr'];
      $name = $allow['user_name'];
      $hin = $allow['hin'];
      $rueck = $allow['rueck'];

      if($hin){
         $chk_hin = " checked";
      } else {
         $chk_hin = "";
      }

      if($rueck){
         $chk_rueck = " checked";
      } else {
         $chk_rueck = "";
      }

      $out[$nr] = "<tr align = \"center\"><td>$name</td>
              <td><input type=\"checkbox\" name=\"hin$nr\" value = \"1\" $chk_hin></td>
              <td><input type=\"checkbox\" name=\"rueck$nr\" value=\"1\" $chk_rueck></td> 
             ";
   }


   return $out;
}


function pay_update(){
   global $g_pdo; 
   
   if (($_POST['paysecure'] != "") && allow_verwaltung()){
      $sql = "SELECT user_nr, user_name, hin, rueck FROM `User` WHERE 1 ORDER BY user_nr";
      foreach ($g_pdo->query($sql) as $allow){
         $nr = $allow['user_nr'];
         $vorher_hin = $allow['hin'];
         $vorher_rueck = $allow['rueck'];
         $name = $allow['user_name'];

         
         $hin = $_POST['hin'.$nr.''];
         if (!$hin){$hin = 0;}

         $rueck = $_POST['rueck'.$nr.''];
         if (!$rueck){$rueck = 0;}
         
         if (($hin != $vorher_hin) || ($rueck != $vorher_rueck)){

            echo  "<script> alert(\"Achtung! Der Status von\\n    $name\\nwird geändert! \\n(Hinrunde von $vorher_hin auf $hin\\nRückrunde von $vorher_rueck auf $rueck)\") </script>";

            
            $betreff = "Der Finanz-Status von $name wurde geändert";
            $from = "From: Finanz Admin <admin@couchtipper.de>";
            $text = "Gude,
            der Status von $name wurde geändert!
            
            Hinrunde von $vorher_hin auf $hin
            Rückrunde von $vorher_rueck auf $rueck";

            mail("bezahlt@couchtipper.de", $betreff, $text, $from);
            
            $insert = $g_pdo->prepare("UPDATE User SET hin = :hin, rueck = :rueck WHERE user_nr = :nr");
            $params = array('hin' => $hin, 'rueck' => $rueck, 'nr' => $nr);
            $insert->execute($params);
        }
      }
   }

}





?>
