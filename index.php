<?php
ini_set('session.cookie_domain', '.couchtipper.de' );
session_start();
require_once("../auth/include/security.inc.php");
is_logged();

## if subdomain == ?? do..
$g_modus = "Buli";
$global_wett_id = "4";

### Bindet alle Wettbewerbs Sachen ein
### Muss zuerst stehen, da sonst nicht auf Datenbank o.Ä. zugegriffen werden kann. 
require_once("../auth/include/wettbewerbe.inc.php");

### Stellt die Verbindung zur Datenbank her. Zugangsdaten kommen aus der Wettbewerbs-DB
require_once("src/include/lib/datenbank.inc.php");

### Library für alle möglichen Zeit-Berechnungen
require_once("src/include/lib/time.inc.php");

### Bindet die Befugnisse ein. Wer darf was ?
require_once("../auth/include/permissions.inc.php");

### Checkt neue Besucher in den jeweiligen Wettbewerb ein.. Das kann man auch noch schöner machen.. 
require_once("../auth/include/check_in.inc.php");


$index = $_GET["index"];
if ($index == "api"){
    include_once("api.php");
    exit;
}


// Wettbewerb check in
check_in_modal();

####### DU HAST NOCH NICHT BEZAHLT
### BITTE BITTE SCHÖNER MACHEN!!


#if ((!check_cash(get_curr_wett())) && (!$_SESSION['bezahlt']) && is_logged() && (get_usernr() != -10) && is_checked_in() && false){
#        //$name123 = get_usernr();
#        echo "<script language='javascript'>alert ('Du hast noch nicht bezahlt!\\nDenk bitte daran, demnächst zu bezahlen :)')</script>";
#        $_SESSION['bezahlt'] = 1;
#}



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

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

    <script src=https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.16.0/bootstrap-table.min.js></script> <!-- sortierbare Tabellen! (braucht jquery) --> 
    <link href=https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.16.0/bootstrap-table.min.css rel=stylesheet> <!-- stylesheet für sortierbare Tabellen -->

    <script src="src/include/scripts/ausblenden1.js"></script> <!-- Zum Ein- und ausblenden verschiedener Elemente -->
  

  <!-- das zeug in die CSS datei!! -->
  <style>
  .stockerl {
    width: 30%;
    display:inline-block;
    text-align:center;
    vertical-align:bottom;
    #writing-mode:vertical-lr;
    #text-orientation:upright;
    #word-break: break-word;
    #hyphens: auto;
    -moz-hyphens: auto;
    -o-hyphens: auto;
    -webkit-hyphens: auto;
    -ms-hyphens: auto;
    hyphens: auto;
    }
    
  .tablestockerl{
    width: 33%;
    height: 50px;
  }
    .platz1 {
    background: #C98910;
    font-size:calc(90% + 1vw + 1vh);
    -moz-hyphens: auto;
    -o-hyphens: auto;
    -webkit-hyphens: auto;
    -ms-hyphens: auto;
    hyphens: auto;
    }
    
     .platz2 {
    background: #A8A8A8;
    font-size:calc(50% + 1vw + 1vh);
    -moz-hyphens: auto;
    -o-hyphens: auto;
    -webkit-hyphens: auto;
    -ms-hyphens: auto;
    hyphens: auto;
    }  
    
    .platz3 {
    background: #965A38;
    font-size:calc(50% + 1vw + 1vh);
    height: 200px;
    -moz-hyphens: auto;
    -o-hyphens: auto;
    -webkit-hyphens: auto;
    -ms-hyphens: auto;
    hyphens: auto;
    }    
    
  .pl1 {
    background: #C98910;
    height: 350px;
    #font-size:calc(70% + 1vw + 1vh);
    font-size:2vmin;
    }
    
  .pl2 {
    height: 300px;
    #font-size:calc(50% + 1vw + 1vh);
    background: #A8A8A8;
    font-size:2vmin;

    }
    
  .pl3 {
    background: #965A38;
    height: 250px;
    #font-size:calc(50% + 1vw + 1vh);
    font-size:2vmin;
    }
    
  .yellow {
    background: darkred;
  }
  .grey {
    //background: #CCCCCc;
    background: #ae4951;
    background: #0081AF;
  }
  .dkgrey {
    background: #999999;
  }
  
  
  .hintergrund{
   background-color: #4d4b46;
   scroll-margin-top: 108px;

}

.fenster{
   background-color: lightgray;
}

.main{
   padding-right: 0%;
   padding-left: 0%;

}

.modal {
  padding: 0 !important; // override inline padding-right added from js
    vertical-align: middle;

}
.modal .modal-dialog {
  width: 90%;
  max-width: none;
  height: 100%;
  margin: auto;
  vertical-align: middle;
  align: middle;
    display: flex;
  align-items: center;
}
.modal .modal-content {
  height: 90%;
  border: 0;
  border-radius: 0;
    vertical-align: middle;

}
.modal .modal-body {
  overflow-y: auto;
    vertical-align: middle;

}

  </style>
</head>


<body>

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
                <li class="nav-item">
                    <a class="nav-link" href="?index=1#main">Bundesliga-Tabelle</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?index=2#main">Spieltage</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?index=3#main">Restprogramm</a>
                </li>
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
                <li class="nav-item">
                    <a class="nav-link" href="?index=12#main">Tagessieger</a>
                </li>
                <?php if (is_logged()){ echo "
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
                 
                <?php if (is_logged()){ echo "
                <li class=\"nav-item\">
                    <a class=\"nav-link\" href=\"?index=10#main\">Gewinnverteilung</a>
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
                    //if (true){
                        echo "
                            <a class=\"btn btn-secondary\" href=\"auth/login.php\">Anmelden</a>";      
                    } else {
                        echo "
                            <a class=\"btn btn-secondary\" href=\"auth/logout.php\">Logout</a>";      
                    }
                ?>
            </ul>
        </div>  
    </nav>

    <?php
    
        if ((!check_cash(get_curr_wett())) && (is_logged())) {  
            echo "<div class=\"alert alert-danger text-center\" style=\"margin-bottom:0\">
                <strong>Achtung!</strong> Du hast noch nicht bezahlt! <a href=\"?index=11#main\" class=\"alert-link\"> <i class=\"fas fa-info-circle\"></i></a>
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
    <p><i class="fas fa-futbol"></i> couchtipper.de <i class="fas fa-futbol"></i></p> 
</div>
</div>

<!--<?php echo getcwd() . "\n";?>-->

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
                if (($index != 4) && ($index != 2) && ($index != 5) ){
                    echo "<h2>Rangliste:</h2>";
                    include_once("src/pages/rangliste.php");
                }

                 if ($index == 5){
                    echo "<h2>Bundesliga-Tabelle:</h2>";
                    include_once("src/pages/tabelle.php");

                }
                /*
                if ($index == 2){
                    echo "<h2>Gruppen-Tabellen:</h2>";
                    include_once("src/pages/nur_tabelle.php");

                }
                */
                
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
                        include_once("src/pages/hello.php");
                        break;
                    case 1:
                        echo "<h2> Bundesliga-Tabelle</h2>";
                        include_once("src/pages/tabelle.php");
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
                        echo "<h2>Datum eingeben</h2>";
                        include_once("src/set_date.php");
                        echo "<h2>Spiele terminieren</h2>";
                        include_once("src/spiele_term.php");
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
                        include_once("src/pages/faq.php");
                        break;
                    case 12: 
                        echo "<h2>Tagessieger</h2>";
                        include_once("src/pages/tagessieger.php");
                        break;
                    case 15:
                        echo "<h2>Statistiken</h2>";
                        include_once("src/stats3.php");
                        #include_once("src/newbot.php");
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
    <p>&copy; couchtipper.de v4.2</p>
</div>


</body>
</html>
 
      
 
