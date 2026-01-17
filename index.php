<?php
ini_set('session.cookie_domain', '.couchtipper.de' );
session_start();
date_default_timezone_set("CET");
header('Content-Type: text/html; charset=UTF-8');
require_once("../auth/include/security.inc.php");
is_logged();


$wartung = 1;
$aktuelle_wett_id = get_aktuelle_wett_id();
$g_modus = "BuLi";
$global_wett_id = get_global_wett_id();
$subdomain = explode(".",$_SERVER['SERVER_NAME'])[0];
$fulldomain = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://" . $_SERVER['HTTP_HOST'];

$g_nachholspiel = NULL;

if ($subdomain == "code"){
    ## Auf code.couchtipper.de werden alle PHP Fehler angezeigt
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL ^  E_NOTICE); #E_NOTICE
}

if ($wartung && ($subdomain != "code")){
    ## Wenn Wartungsmodus an ist, schalten wir auf die entsprechende Fehlerseite
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

### Bindet den Code der Index Seite ein
require_once("src/include/code/index.code.php");

if (isset($_GET["index"])){
    $index = $_GET["index"];
} else {
    $index = "";
}

if ($index == "api"){
    ## Für API calls leiten wir direkt weiter
    include_once("src/api/crontab.php");
    exit;
}

if ($index == "cal"){
    ## Für den Calendar wird auch weitergeleitet
    include_once("src/api/calendar.php");
    exit;
}

if ($index == "rebuild"){
    ## Um die vorberechneten Werte der DB komplett neu zu setzen
    require_once("src/include/code/refresh.php");
    require_once("src/include/code/get_games.inc.php");

    rebuild_rangliste();
    rebuild_tabelle();
    rebuild_full_precomputation();
    exit;
}

### CHECK IN
if (is_active_wettbewerb()){
    ## Bei aktivem Wettbewerb, können sich User selbst einchecken
    if (isset($_GET["new_check_in"]) && $_GET["new_check_in"] == 1){
        ## Setzen der Session Variable, damit das Modal nur beim ersten Besuch angezeigt wird.
        $_SESSION["seen_check_in_modal"] = 0;
    }
    ## Zeigt das Modal an
    check_in_modal();
}

## Beim Wechseln der Saison wollen wir auf der Selben Seite bleiben
## TODO: Das sollte auch beim cookie reset passieren...
$url_suffix = "";
$url_suffix_no_year = "";
foreach ($_GET as $url_parameter => $url_value){
    ## Speichert alle GET parameter in der variable. 
    ## Dann können wir diese später für Links nutzen.
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
    
    <!-- Bootstrap Style Sheet -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
    
    <!-- Bootstrap & Ajax JS Code -->
    <script src=https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
    
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/59d142c614.js" crossorigin="anonymous"></script>
    
    <!-- Sortierbare Tabellen -->
    <script src=https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.16.0/bootstrap-table.min.js></script>
    <link href=https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.16.0/bootstrap-table.min.css rel=stylesheet>
    
    <!-- Schriftart einbinden -->
    <link href='https://fonts.googleapis.com/css?family=Noto Sans' rel='stylesheet'>
    
    <!-- Main Style Sheet -->
    <link href="src/include/styles/main_style.css?v=<?php echo 11; # echo rand();?>" rel="stylesheet" type="text/css">
    
    <!-- JS Code zum Ausblenden von Elementen -->
    <script src="src/include/scripts/ausblenden.js?v=<?php echo 10; #echo rand();?>"></script>
    
    <!-- JS Code um neue Einstellungen zu updaten -->
    <script src="src/include/scripts/update.js?v=3"></script>
    
    <!-- JS Code zum steuern von Bootstrap Elementen -->
    <script src="src/include/scripts/bootstrap.js?v=3"></script>
    
    <!-- Logos -->
    <link rel="icon" type="image/png" href="images/logo3-rund.png">
    <link rel="apple-touch-icon" href="images/logo3.png"/>
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
                            <?php print_year_dropdown($url_suffix_no_year); ?>
                        </div>
                    </div>
                </span>
            </ul>
            
            <ul class="nav navbar-nav navbar-left">
                <?php print_nav();?>
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
        ## Wer noch nicht im Wettbewerb eingecheckt ist, bekommt einen Banner angezeigt
        if ((!is_checked_in()) && (is_logged()) && (is_active_wettbewerb()) ) {  
            echo "<div class=\"alert alert-warning text-center\" style=\"margin-bottom:0\">
                <strong>Achtung!</strong> Du bist im aktuellen Wettbewerb noch nicht eingecheckt! 
                <a href=\"?new_check_in=1\" class=\"alert-link\"> <i class=\"fas fa-info-circle\"></i></a>
                </div>";
        }
        
        ## Wer schon im Wettbewerb eingecheckt ist, aber noch nicht bezahlt hat bekommt einen Banner angezeigt        
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
</div><!-- End Sticky Top -->



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


<?php 
## Wenn die Datenbank komplett leer ist, wurde gerade eine neue Saison erstellt. 
## Dann springen wir auf die Seite, zum weiteren Erstellen der Saison
if (check_if_db_empty() || (isset($_GET["setup"]) &&  $_GET["setup"] == 1)) {
    if (allow_main_verwaltung()){
        ## Nur mit Verwaltungsrechten aufrufbar!
        include("src/setup/setup.php");
        exit;
    } else {
        ## Fehler anzeigen, falls hier jemand landet..
        echo "<div class=\"alert alert-danger\"><strong>Hier ist etwas schiefgelaufen..</strong> Dieser Wettbewerb wurde noch nicht erstellt.
        Du solltest hier eigentlich nicht sein! Durch den folgenden Button kommst du wieder zurück!";
        echo "<br>";
        echo '<a href="?year=reset" class="btn btn-primary">Zurück!</a>';
        echo "</div>";
        exit;
    }
}

if (get_wettbewerb_code(get_curr_wett()) == "Verwaltung") {
    if (allow_main_verwaltung()){
        ## Nur mit Verwaltungsrechten aufrufbar!
        include_once("src/setup/new_wettbewerb.php");
        ## TODO: im Verwaltungsmenü hinzufügen, welche Wettbewerbe aktiv sind usw.
        ### set_global_wett_id(8);
        exit;
    } else {
        ## Fehler anzeigen, falls hier jemand landet..
        echo "<div class=\"alert alert-danger\"><strong>Hier ist etwas schiefgelaufen..</strong> Du hast keinen Zugriff auf diese Seite!.
        Du solltest hier eigentlich nicht sein! Durch den folgenden Button kommst du wieder zurück!";
        echo "<br>";
        echo '<a href="?year=reset" class="btn btn-primary">Zurück!</a>';
        echo "</div>";
        exit;
    }
}
?>

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
            <?php print_pages();?>
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
