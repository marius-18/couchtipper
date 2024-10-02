<?php
ini_set('session.cookie_domain', '.couchtipper.de' );
session_start();
date_default_timezone_set("CET");
header('Content-Type: text/html; charset=UTF-8');
require_once("../auth/include/security.inc.php");
is_logged();

$wartung = 0;
$aktuelle_wett_id = [8,7];
$g_modus = "BuLi";
$global_wett_id = "8";
$subdomain = explode(".",$_SERVER['SERVER_NAME'])[0];
$fulldomain = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://" . $_SERVER['HTTP_HOST'];

$g_nachholspiel = NULL;

#if (($wartung) && ($subdomain == "code")){
if ((1) && ($subdomain == "code")){
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL ^  E_NOTICE); #E_NOTICE
}

if ($wartung && ($subdomain != "code")){
    include_once("wartung.php");
    exit;
}

if (isset($_GET["year"])){
    $_SESSION['year'] = $_GET["year"];
}

if ( isset($_SESSION['year']) && is_numeric($_SESSION['year']) ){
    $g_modus = "BuLi";
    $global_wett_id = $_SESSION['year'];
}
### Bindet alle Wettbewerbs Sachen ein
### Muss zuerst stehen, da sonst nicht auf Datenbank o.Ä. zugegriffen werden kann. 
require_once("../auth/include/wettbewerbe.inc.php");

### Stellt die Verbindung zur Datenbank her. Zugangsdaten kommen aus der Wettbewerbs-DB
require_once("src/include/lib/datenbank.inc.php");

### Library für alle möglichen Zeit-Berechnungen
require_once("src/include/lib/time.inc.php");

### Library für alle möglichen Datenbankabfragen
require_once("src/include/lib/queries.inc.php");

### Library für alle möglichen Eingabeforms
require_once("src/include/lib/forms.inc.php");

### Um precomputations im Hintergrund auszuführen
require_once("src/include/lib/precomputation.inc.php");

### Bindet die Befugnisse ein. Wer darf was ?
require_once("../auth/include/permissions.inc.php");

### Checkt neue Besucher in den jeweiligen Wettbewerb ein.. Das kann man auch noch schöner machen.. 
require_once("../auth/include/check_in.inc.php");


if (isset($_GET["index"])){
    $index = $_GET["index"];
} else {
    $index = "";
}

if ($index == "api"){
    include_once("src/api/crontab.php");
    exit;
}

if ($index == "cal"){
    include_once("src/api/calendar.php");
    exit;
}
// Wettbewerb check in (nur im aktiven Wettbewerb!!)
#TODO: Der checkin functioniert so nicht!! ANPASSEN!
#TODO: checkin nur, wenn der wettbewerb "aktiv" ist..
if (is_active_wettbewerb()){
    #TODO:
    ## Check in page ändern..
    #funktion is_checked_in einführen und dann Tipps seite sperren
    ## Dann Statt "du hast noch nicht bezahlt" einfach "Du bist noch nicht im Wettbewerb eingetragen".. Dann Verlinken zur Seite.. oder einfach auf Hello? Statt dem Modal..
    ## Da dann einfach alle Aktiven Wettbewerbe auflisten, dann kann man auswählen wo man
    ## tippen will und sich eintragen.. mit bestätigung über den einsatz oder so..

    #TODO: weiter: unter "Mein Konto" alle laufenden Wettbewerbe anzeigen
    if (isset($_GET["new_check_in"]) && $_GET["new_check_in"] == 1){
        $_SESSION["seen_check_in_modal"] = 0;
    }
    check_in_modal();
}

#if ((1) && ($subdomain == "code")){
    #$player = [3,4,5,6,7,8,9,10,11,12,13,14,15,17,20,21];
    #$player = [3,5,6,7,13,17,29,76,77,78,79];
    #$player = [55];
    #check_in_manually($player, 7,0);
    
    #require_once("src/include/code/refresh.php");
    #for ($i=1;$i<35;$i++){
        #update_tabelle($i);
        #update_rangliste($i);
        #update_tabelle_platz($i);
    #}
#}

####### DU HAST NOCH NICHT BEZAHLT
### BITTE BITTE SCHÖNER MACHEN!!


#if ((!check_cash(get_curr_wett())) && (!$_SESSION['bezahlt']) && is_logged() && (get_usernr() != -10) && is_checked_in() && false){
#        //$name123 = get_usernr();
#        echo "<script language='javascript'>alert ('Du hast noch nicht bezahlt!\\nDenk bitte daran, demnächst zu bezahlen :)')</script>";
#        $_SESSION['bezahlt'] = 1;
#}

## Beim Wechseln der Saison wollen wir auf der Selben Seite bleiben
## TODO: Das sollte auch beim cookie reset passieren...
$url_suffix = "";
$url_suffix_no_year = "";
foreach ($_GET as $url_parameter => $url_value){
    $url_suffix .= "$url_parameter=$url_value&";
    if ($url_parameter != "year"){
        $url_suffix_no_year .= "$url_parameter=$url_value&";        
    }
}
?>


<!DOCTYPE html>
<html lang="de">
<head>
    <title><?php echo get_wettbewerb_title(get_curr_wett());?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">

    <script src=https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>

    <script src="https://kit.fontawesome.com/59d142c614.js" crossorigin="anonymous"></script>
    <!--
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    -->

    <script src=https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.16.0/bootstrap-table.min.js></script> <!-- sortierbare Tabellen! (braucht jquery) --> 
    <link href=https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.16.0/bootstrap-table.min.css rel=stylesheet> <!-- stylesheet für sortierbare Tabellen -->

    <link href="src/include/styles/main_style.css?v=2" rel="stylesheet" type="text/css">
    
    <script src="src/include/scripts/ausblenden.js?v=<?php echo rand();?>"></script> <!-- Zum Ein- und ausblenden verschiedener Elemente -->

    <script src="src/include/scripts/update.js?v=1"></script> <!-- Um neue Einstellungen zu updaten -->


    <script src="src/include/scripts/bootstrap.js?v=2"></script> <!-- Für Bootstrap steuerungen -->
    
    <link rel="icon" type="image/png" href="images/logo3-rund.png">
    <link rel="apple-touch-icon" href="images/logo3.png"/>

<link href='https://fonts.googleapis.com/css?family=Noto Sans' rel='stylesheet'>
<style>
body {
    font-family: 'Noto Sans';
}
</style>
</head>


<body>

<?php
    if (($wartung) && ($subdomain == "code")){
        echo "<div class=\"jumbotron text-center jumbotron-fluid bg-danger\">
        <h1>ACHTUNG - DU BIST IM WARTUNGSMODUS</h1></div>";
    }
?>

<!-- 
###########################################################
MENÜ                                               
###########################################################
-->

<div class = "sticky-top">
    <nav class="navbar navbar-expand-xl bg-dark navbar-dark">
        <a class="navbar-brand" href="/">
            <?php
                if (!is_logged()){
                //if (true) {
                    echo "Nicht angemeldet!";
                } else {
                    #echo "<img width=\"30px\" src = \"images/logo2-rund.png\">&nbsp;";
                    echo "Gude, ".get_username();
                }
            ?> 
        </a>
        


        <!-- Menü Knopf (verschwindet)                  -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
  
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="nav navbar-nav navbar-left">
                <span class="navbar-text">
                    <div class="dropdown">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                            <?php echo get_wettbewerb_code(get_curr_wett()). " " .get_wettbewerb_jahr(get_curr_wett());?>
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-header">Aktuelle Saison</div>
                            <a class="dropdown-item" href="?<?php echo $url_suffix_no_year;?>year=8" style="color:black">BuLi 2024/25</a>
                            <a class="dropdown-item" href="?<?php echo $url_suffix_no_year;?>year=7" style="color:black">EM 2024</a>
                            <div class="dropdown-divider"></div>
                            <div class="dropdown-header">Vergangene</div>
                            <a class="dropdown-item" href="?<?php echo $url_suffix_no_year;?>year=6" style="color:black">BuLi 2023/24</a>
                            <a class="dropdown-item" href="?<?php echo $url_suffix_no_year;?>year=5" style="color:black">BuLi 2022/23</a>
                            <a class="dropdown-item" href="?<?php echo $url_suffix_no_year;?>year=4" style="color:black">BuLi 2021/22</a>
                            <a class="dropdown-item" href="?<?php echo $url_suffix_no_year;?>year=3" style="color:black">EM 2021</a>
                            <a class="dropdown-item" href="?<?php echo $url_suffix_no_year;?>year=2" style="color:black">BuLi 2020/21</a>
                            <a class="dropdown-item" href="?<?php echo $url_suffix_no_year;?>year=1" style="color:black">BuLi 2019/20</a>
                            <a class="dropdown-item" href="?<?php echo $url_suffix_no_year;?>year=0" style="color:black">BuLi 2018/19</a>
                            <a class="dropdown-item" href="?<?php echo $url_suffix_no_year;?>year=-1" style="color:black">WM 2018</a>
                            <a class="dropdown-item" href="?<?php echo $url_suffix_no_year;?>year=-2" style="color:black">BuLi 2017/18</a>
                            <a class="dropdown-item" href="?<?php echo $url_suffix_no_year;?>year=-3" style="color:black">BuLi 2016/17</a>
                            <!--<a class="dropdown-item" href="?<?php echo $url_suffix_no_year;?>year=-4" style="color:black">EM 2016</a>-->
                            <a class="dropdown-item" href="?<?php echo $url_suffix_no_year;?>year=-5" style="color:black">BuLi 2015/16</a>
                            <!--<a class="dropdown-item" href="?<?php echo $url_suffix_no_year;?>year=-6" style="color:black">BuLi 2014/15</a>-->
                            <?php
                            if (allow_verwaltung()){
                                #echo "<div class=\"dropdown-divider\"></div>
                                #    <div class=\"dropdown-header\">Turniere</div>
                                #    <a class=\"dropdown-item\" href=\"?year=7\" style=\"color:black\">EM 2024</a>";
                            }
                            ?>
                        </div>
                    </div>
                </span>
            </ul>
            
            <ul class="nav navbar-nav navbar-left">
                <?php 
                    if (get_wettbewerb_code(get_curr_wett()) == "BuLi"){
                        echo "
                            <li class=\"nav-item\">
                                <a class=\"nav-link\" href=\"?index=1#main\">Bundesliga-Tabelle</a>
                            </li>";
                    } 
                    if (is_big_tournament(get_curr_wett())) {
                        echo "
                            <li class=\"nav-item\">
                                <a class=\"nav-link\" href=\"?index=1.1#main\">Gruppenphase</a>
                            </li>";                   
                        echo "
                            <li class=\"nav-item\">
                                <a class=\"nav-link\" href=\"?index=1.2#main\">KO-Runde</a>
                            </li>";   
                    }
                ?>
                <li class="nav-item">
                    <a class="nav-link" href="?index=2#main">Spieltage</a>
                </li>
                <?php
                    if (!is_big_tournament(get_curr_wett())) {
                        echo "
                            <li class=\"nav-item\">
                                <a class= \"nav-link\" href=\"?index=3#main\">Restprogramm</a>
                            </li>";
                    }
                ?>
                <li class="nav-item">
                    <a class="nav-link" href="?index=4#main">Rangliste</a>
                </li>   
                <?php if (is_logged()){ echo "
                <li class=\"nav-item\">
                    <a class=\"nav-link\" href=\"?index=5#main\">Tipps</a>
                </li>";
                }
                ?>
                <?php if (allow_erg()){ echo "
                <li class=\"nav-item\">
                    <a class=\"nav-link\" href=\"?index=6#main\">Ergebnisse</a>
                </li>";
                }
                ?>
                <?php if (is_logged() && !is_big_tournament(get_curr_wett())) { echo "
                <li class=\"nav-item\">
                    <a class=\"nav-link\" href=\"?index=12#main\">Tagessieger</a>
                </li>";
                }
                ?>
                
                <?php if (is_logged()){ echo "
                <li class=\"nav-item\">
                    <a class=\"nav-link\" href=\"?index=10#main\">Gewinnverteilung</a>
                </li>";
                }
                ?>   
                
                <?php if (is_logged() && (is_active_wettbewerb())){ echo "
                <li class=\"nav-item\">
                    <a class=\"nav-link\" href=\"?index=7#main\">Mein Konto</a>
                </li>";
                }
                ?>
                <?php if (allow_date()){ echo "
                <li class=\"nav-item\">
                    <a class=\"nav-link\" href=\"?index=8#main\">Spiele terminieren</a>
                </li>";
                }
                ?>                 
                <?php if (allow_verwaltung()){ echo "
                <li class=\"nav-item\">
                    <a class=\"nav-link\" href=\"?index=9#main\">Verwaltung</a>
                </li>";
                }
                ?> 
                <?php if (allow_erg()){ echo "
                <li class=\"nav-item\">
                    <a class=\"nav-link\" href=\"?index=15#main\">Stats</a>
                </li>";
                }
                ?> 
                                            
                <li class="nav-item">
                    <a class="nav-link" href="?index=11#main">FAQ</a>
                </li>
                
            </ul>
            
            <ul class="nav navbar-nav ml-auto">              
            
                <?php
                    if (!is_logged()){
                        echo "
                            <a class=\"btn btn-secondary\" href=\"auth/login.php?return=$fulldomain\">Anmelden</a>";      
                    } else {
                        echo "
                            <a class=\"btn btn-secondary\" href=\"auth/logout.php\">Logout</a>";      
                    }
                ?>
                
            </ul>
        </div>  
    </nav>

    <?php
    
        if ((!is_checked_in()) && (is_logged()) && (is_active_wettbewerb()) ) {  
            echo "<div class=\"alert alert-warning text-center\" style=\"margin-bottom:0\">
                <strong>Achtung!</strong> Du bist im aktuellen Wettbewerb noch nicht eingecheckt! 
                <a href=\"?new_check_in=1\" class=\"alert-link\"> <i class=\"fas fa-info-circle\"></i></a>
                </div>";
        }
        
        elseif ((!check_cash(get_curr_wett())) && (is_logged()) && (is_active_wettbewerb()) ) {  
            echo "<div class=\"alert alert-danger text-center\" style=\"margin-bottom:0\">
                <a href=\"?index=11#main\" class=\"alert-link\"> 
                <!--<i class=\"fas fa-info-circle\"></i>-->
                <strong>Achtung!</strong></a> Du hast noch nicht bezahlt! 
                <a href=\"https://paypal.me/couchtipper\" class=\"alert-link\">PayPal <i class=\"fa-brands fa-paypal\"></i></a> 
                <!--&nbsp;&nbsp;-->
                </div>";
        }
        
        
    
    ?>
</div>



<!--
####################################################################################
### Logo Banner
####################################################################################
-->

<div class="container-fluid hintergrund" style="padding-bottom:30px; padding-top:30px;" >
<div class="container jumbotron text-center grey" style="margin-bottom:0">
    <h1> Willkommen zum <?php echo get_wettbewerb_title(get_curr_wett());?>! </h1> 
    <p>
    <!--<i class="fas fa-futbol"></i> -->
    <img width="50px" src = "images/logo3-rund.png">
    couchtipper.de
    <img width="50px" src = "images/logo3-rund.png">
    <!--<i class="fas fa-futbol"></i></p> -->
</div>
</div>


<!--
####################################################################################
### BODY
####################################################################################
-->

<div class="container-fluid hintergrund" style="margin-top:0px; padding-bottom:55px" id="main">
    <div class="row centering justify-content-around">
    
        <!-- NICHT SICHTBAR; QUASI LINKER RAHMEN -->
        <div class="col-lg-0 d-none d-lg-block d-lg-block text-center">
            <hr class="d-sm-none">
        </div>
        
        <!-- Linkes Fenster, Standard Rangliste, sonst ...? mobil nicht sichtbar! -->
        <div class="col-lg-4 d-none d-xl-block d-lg-block text-center fenster rounded main">
            <?php
                if (($index != 4) && ($index != 2) && ($index != 5) && ($index != "1.2") && ($index != "16")){
                    echo "<h2>Rangliste:</h2>";
                    include_once("src/pages/rangliste_links.php");
                }

                 if (($index == 5) || ($index == 2) || ($index == "1.2")){
                    if (get_wettbewerb_code(get_curr_wett()) == "BuLi"){
                        echo "<h2>Bundesliga-Tabelle:</h2>";
                        include_once("src/pages/tabelle.php");
                    }
                    if (is_big_tournament(get_curr_wett())){
                        echo "<h2>Gruppen-Tabellen:</h2>";
                        include_once("src/pages/nur_tabelle.php");
                    }
                }
                
            ?>

            <hr class="d-sm-none">
        </div>

        <!-- NICHT SICHTBAR; QUASI - MITTEL - RAHMEN -->
        <div class="col-lg-0 d-none d-xl-block d-lg-block text-center">
            <hr class="d-sm-none">
        </div>
    
        <!-- HAUPTFELD  -->
        <div class="col-lg-6 hidden-md-up fenster text-center rounded main">
      
            <?php
                switch ($index) {
                    case "":
                        if (get_wettbewerb_code(get_curr_wett()) == "Überblick"){
                         include_once("src/rangliste_overview.php");
                         break;
                        }
                        include_once("src/pages/hello.php");
                        break;
                    case 1:
                        echo "<h2> Bundesliga-Tabelle</h2>";
                        include_once("src/pages/tabelle.php");
                        break;
                    case 1.1:
                        echo "<h2> Gruppenphase</h2>";
                        include_once("src/pages/wm_tabelle.php");
                        break;
                    case 1.2:
                        echo "<h2> KO-Runde</h2>";
                        include_once("src/pages/ko.php");
                        break;
                    case 2: 
                        echo "<h2>Spieltage</h2>";
                        include_once("src/pages/spieltag.php");
                        break;
                    case 3:
                        echo "<h2>Restprogramm</h2>";
                        include_once("src/pages/restprogramm.php");
                        break;
                    case 4:
                        echo "<h2>Rangliste</h2>";
                        include_once("src/pages/rangliste.php");
                        break;
                    case 5:
                        echo "<h2>Tipps</h2>";
                        include_once("src/pages/tipp.php");
                        break;
                    case 6:
                        echo "<h2>Ergebnisse eingeben</h2>";
                        include_once("src/pages/ergebnis.php");
                        break;
                    case 7:
                        echo "<h2>Mein Konto</h2>";
                        include_once("src/pages/konto.php");
                        break;
                    case 8:
                        echo "<h2>Spiele terminieren</h2>";
                        include_once("src/pages/datum_spiele_term.php");
                        echo "<h2>Datum eingeben</h2>";
                        include_once("src/pages/datum_set_spieltag.php");
                        break;
                    case 9:
                        echo "<h2>Verwaltung</h2>";
                        include_once("src/pages/user.php");
                        break;
                    case 10:
                        echo "<h2>Gewinnverteilung</h2>";
                        include_once("src/pages/gewinn.php");
                        break;
                    case 11:
                        echo "<h2>FAQ</h2>";
                        if (!is_big_tournament(get_curr_wett())){
                            include_once("src/pages/faq.php");
                        } else {
                            include_once("src/pages/faq_em.php");                            
                        }
                        break;
                    case 12: 
                        echo "<h2>Tagessieger</h2>";
                        include_once("src/pages/tagessieger.php");
                        break;
                    case 15:
                        echo "<h2>Statistiken</h2>";
                        include_once("src/pages/tabellenverlauf.php");
                        #include_once("src/newbot.php");
                        break;
                    case 16:
                        echo "<h2>TEST</h2>";
                        include_once("src/liga_anlegen.php");
                        break;
                    default:
                        include_once("src/pages/error.php");
                        break;
                    
                }
            ?>
        </div>
        
                <!-- NICHT SICHTBAR; QUASI Rechter RAHMEN -->
        <div class="col-lg-0 d-none d-lg-block d-lg-block text-center">
            <hr class="d-sm-none">
        </div>
    </div>
</div>





<div class="jumbotron text-center grey" style="margin-bottom:0">
    <br>
    <p>&copy; couchtipper.de v4.2</p>
</div>
</body>
</html>
 
      
 
