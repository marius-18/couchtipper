<?php
require_once("datenbank.inc.php");

// BEIM PW ÄNDERN ALLE SESSIONS LÖSCHEN


function is_logged(){
    global $g_pdo;
    $host = $_SERVER['SERVER_NAME'];

    if(!isset($_SESSION['userid']) && isset($_COOKIE['identifier']) && isset($_COOKIE['securitytoken'])) {
        /* 
        ## SESSION NOCH NICHT ERSTELLT, ABER COOKIES SIND DA
        */
        
        $identifier = $_COOKIE['identifier'];
        $securitytoken = $_COOKIE['securitytoken'];
	
        $statement = $g_pdo->prepare("SELECT user_id, securitytoken, securitytoken1 FROM Security WHERE identifier = :identifier");
        $result = $statement->execute(array("identifier" => $identifier));
        $securitytoken_db = $statement->fetch();
        $userid = $securitytoken_db['user_id'];

        if ((sha1($securitytoken) != $securitytoken_db['securitytoken']) && (sha1($securitytoken) != $securitytoken_db['securitytoken1'])) {
            /*
            ## TOKEN IST FALSCH !! --> ausloggen
            */

            $delete = $g_pdo->prepare("DELETE FROM Security WHERE identifier = :identifier");
            $delete->execute(array('identifier' => $identifier));

            $_SESSION['remove'] = true; // Was passier hiermit?

            //echo "cookie id:". $identifier."<br>";
            //echo "cookie token:". $securitytoken."<br>";
            //echo "DB token:". $securitytoken_db['securitytoken']."<br>";
            //echo "DB token1:". $securitytoken_db['securitytoken1']."<br>";
            //echo "sess id:". $_SESSION['userid']."<br>";
            
            
            
            setcookie("identifier","",time()-(3600*24*365),"/", "couchtipper.de") or die ("Beim L&ouml;schen des Cookies gab es einen Fehler.");
            setcookie("securitytoken","",time()-(3600*24*365),"/", "couchtipper.de") or die ("Beim L&ouml;schen des Cookies gab es einen Fehler.");
            setcookie("identifier","",time()-(3600*24*365),"/") or die ("Beim L&ouml;schen des Cookies gab es einen Fehler.");
            setcookie("securitytoken","",time()-(3600*24*365),"/") or die ("Beim L&ouml;schen des Cookies gab es einen Fehler.");
            



            echo "<div class=\"jumbotron text-center bg-warning\" style=\"margin-bottom:0\">
                    <div class=\"alert alert-danger\" style=\"margin-bottom:0\">
                        <strong>Warnung!</strong> <br>
                        Angemeldet bleiben ist fehlgeschlagen. Logge dich erneut ein. 
                        <br>Sollte dies &ouml;fter vorkommen, &auml;ndere eventuell dein Passwort.<br><br>
                        Du wirst gleich weitergeleitet.
                    </div>
                 </div>";
                
            echo "<meta http-equiv=\"refresh\" content=\"1; URL=/\">";
      
      
        } else {
            /*
            ## EIN TOKEN STIMMT ÜBEREIN --> session wird neu erstellt!!
            */

            
            /*
            ## Token der übereinstimmt wird gespeichert (als ''letzter'' token)
            */
            if (sha1($securitytoken) == $securitytoken_db['securitytoken']) {
                $alter_securitytoken = $securitytoken_db['securitytoken'];
            }

            if (sha1($securitytoken) == $securitytoken_db['securitytoken1']) {		
                $alter_securitytoken = $securitytoken_db['securitytoken1'];
            }

            /*
            ## Erstelle einen neuen zufälligen Token
            */
            
            $neuer_securitytoken = random_string();
            
            
            $_SESSION['identifier'] = $identifier; 
            $_SESSION['securitytoken'] = $neuer_securitytoken;
            $_SESSION['securitytoken_old'] = $alter_securitytoken;


            /*
            ## Damit beim reload nicht schon "du hast noch nicht bezahlt" steht
            */
            
            $_SESSION['userid'] = -10;


            /*
            ## Erstmal alle cookies löschen
            */
            
            setcookie("identifier","asdf",time()-(3600*24*365),"/", "couchtipper.de") or die ("Beim L&ouml;schen des Cookies gab es einen Fehler. Sind Cookies aktiviert?");
            setcookie("securitytoken","asdf",time()-(3600*24*365),"/", "couchtipper.de") or die ("Beim L&ouml;schen des Cookies gab es einen Fehler. Sind Cookies aktiviert?");

            
            /*
            ## identifier und token im cookie speichern.
            */
            
            setcookie("identifier",$identifier,time()+(3600*24*365),"/", "couchtipper.de") or die ("Beim Setzen des Cookies gab es einen Fehler. Sind Cookies aktiviert?"); //1 Jahr Gültigkeit
            setcookie("securitytoken",$neuer_securitytoken,time()+(3600*24*365),"/", "couchtipper.de") or die ("Beim Setzen des Cookies gab es einen Fehler. Sind Cookies aktiviert?"); //1 Jahr Gültigkeit

            
            /*
            ## Hier könnte jetzt eventuell ein Problem entstehen. Auf iOS geräten wird der cookie eventuell nicht erstellt
            ## (bzw. er wird erstellt und dann aber wieder irgendwie gelöscht.)
            ## Also weiterleitung zu cookie.php die werte von identifier und token sind
            ## serverseitig in der session gespeichert. dann wird geprüft ob alles im cookie drin steht und nur dann
            ## wird der neue token in die datenbank geschrieben. Zuletzt wird dann in die Session die ID geschrieben!!!!
            */
            
            
            echo "<meta http-equiv=\"refresh\" content=\"0; URL=/src/functions/cookie.php\">";
      }
   }

   return (isset($_SESSION['userid']));

}



function get_usernr(){
    $userid = $_SESSION['userid'];
    return $userid;
}

function get_username(){
    global $g_pdo;
    $userid = $_SESSION['userid'];
    $statement = $g_pdo->prepare("SELECT user_name FROM User WHERE user_nr = :userid");
    $result = $statement->execute(array("userid" => $userid));
    $allow = $statement->fetch();
    return $allow['user_name'];
}

function allow_date(){
    global $g_pdo;

    $userid = get_usernr();
    $statement = $g_pdo->prepare("SELECT datum_aendern FROM User WHERE user_nr = :userid");
    $result = $statement->execute(array("userid" => $userid));
    $allow = $statement->fetch();

    return $allow['datum_aendern'];

}

function allow_erg() {
    global $g_pdo;

    $userid = get_usernr();
    $statement = $g_pdo->prepare("SELECT erg_aendern FROM User WHERE user_nr = :userid");
    $result = $statement->execute(array("userid" => $userid));
    $allow = $statement->fetch();

    return $allow['erg_aendern'];
}


function allow_tipps(){
    global $g_pdo;

    $userid = get_usernr();
    $statement = $g_pdo->prepare("SELECT tipps_aendern FROM User WHERE user_nr = :userid");
    $result = $statement->execute(array("userid" => $userid));
    $allow = $statement->fetch();

    return $allow['tipps_aendern'];
}

function allow_verwaltung(){
    global $g_pdo;

    $userid = get_usernr();
    $statement = $g_pdo->prepare("SELECT user_verwaltung FROM User WHERE user_nr = :userid");
    $result = $statement->execute(array("userid" => $userid));
    $allow = $statement->fetch();

    return $allow['user_verwaltung'];

}

function my_team(){
    global $g_pdo;
    $userid = get_usernr();

    $statement = $g_pdo->prepare("SELECT team FROM User WHERE user_nr = :userid");
    $result = $statement->execute(array("userid" => $userid));
    $allow = $statement->fetch();

    return $allow['team'];
}

function bezahlt(){
    ## ändern zu: in welcher liga tippst du ? hast du das bezahlt ? /hin, rück nicht in User sondern in eigener tippgruppen tabelle
    global $g_pdo;
    $userid = get_usernr();
    $statement = $g_pdo->prepare("SELECT hin,rueck FROM User WHERE user_nr = :userid");
    $result = $statement->execute(array("userid" => $userid));
    $allow = $statement->fetch();

    return array($allow['hin'], $allow['rueck']);
}

function user_vorhanden($name){
    global $g_pdo;
    $statement = $g_pdo->prepare("SELECT * FROM User WHERE user_name = :username");
    $params = array('username' => $name);
    $statement->execute($params);
    $user = $statement->fetch();
    if ($user == true){
        return 1;
    } else {
        return 0;
    }
}

function random_string() {
    if(function_exists('random_bytes')) {
        $bytes = random_bytes(16);
        $str = bin2hex($bytes); 
    } else if(function_exists('openssl_random_pseudo_bytes')) {
        $bytes = openssl_random_pseudo_bytes(16);
        $str = bin2hex($bytes); 
    } else if(function_exists('mcrypt_create_iv')) {
        $bytes = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
        $str = bin2hex($bytes); 
    } else {
        $str = md5(uniqid('sadoiUSOIzdsa87(7dsauzidzudZIUSAZDOI', true));
    }	
   
    return "0".$str."0";
}

function spt_select(){
    global $g_pdo;

    $ende = 1;
    $spt = 0;

    while (($ende < time() ) && ($ende != "")){
        $spt++; 
        $datum = "";
        $sql = "SELECT datum FROM Datum WHERE spieltag='$spt'";
        foreach ($g_pdo->query($sql) as $row) {
            $datum = $row['datum'];
        }

        if (date("w", $datum) == 2){ // FALLS ENGLISCHE WOCHE!
            $ende = $datum + (2*24*60*60);
        } else {
            $ende = $datum + (4*24*60*60);
        }

        if ($spt>34){
            return 34;
            exit;
        }
    }

    if ($spt<1){
        $spt = 1;
    }

    return $spt;
}



function spt_select1() { // spieltagsanzeige fuer SelectFormular

    // AB MITTE DER WOCHE SOLL SCHON DER NEUE SPIELTAG ANGEZEIGT WERDEN !!!!!
    //vllt über ende 

    global $g_pdo;
    $datum = 1;
    $spt = 0;

    $abzug = (3*24*60*60);
    $abzug = 0 ;

    while (($datum - $abzug < time()) && ($datum != "")){
        $spt ++;
        $datum = "";
        $sql = "SELECT datum FROM Datum WHERE spieltag='$spt'";
        foreach ($g_pdo->query($sql) as $row) {
            $datum = $row['datum'];
        }

        echo "Spiel ". date("w", $datum);
        if (date("w", $datum) == 2){ // FALLS ENGLISCHE WOCHE!
            $abzug = (1*24*60*60);
        } else {
            $abzug = (3*24*60*60);
        }
        
        if ($spt>34){
            return 34;
            exit;
        }
    }

    $spt--;

    if ($spt<1){
        $spt = 1;
    }

    
    return $spt;
}


function akt_spieltag() {
    #return 17;
    global $g_pdo;
    $datum = 1;
    $spt = 0;

    while (($datum < time()) && ($datum != "")){
        $spt ++;

        $datum = "";
        $sql = "SELECT datum FROM Datum WHERE spieltag='$spt'";
        foreach ($g_pdo->query($sql) as $row) {
            $datum = $row['datum'];
        }   
        
        if ($spt>34){
            return 34;
            exit;
        }
    }

    $spt--;

    if ($spt<1){
        $spt = 1;
    }

    return $spt;
}



function last_spieltag(){
    return akt_spieltag() -1;
}


?>
