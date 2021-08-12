<?php
ini_set('session.cookie_domain', '.couchtipper.de' );
session_start();
require_once("../auth/include/security.inc.php");
is_logged();

$g_modus = "Buli";
$g_wett_id = "4";

require_once("../auth/include/permissions.inc.php");
require_once("../auth/include/check_in.inc.php");
require_once("../auth/include/wettbewerbe.inc.php");

//require_once("src/include/wettbewerb_main.inc.php");
//require_once("src/functions/main.inc.php");

require_once("src/include/lib/datenbank.inc.php");
require_once("src/include/lib/time.inc.php");


$index = $_GET["index"];


// Wettbewerb check in
check_in();

####### DU HAST NOCH NICHT BEZAHLT
### BITTE BITTE SCHÖNER MACHEN!!


if ((!check_cash($g_wett_id)) && (!$_SESSION['bezahlt']) && is_logged() && (get_usernr() != -10) && is_checked_in() && false){
        //$name123 = get_usernr();
        echo "<script language='javascript'>alert ('Du hast noch nicht bezahlt!\\nDenk bitte daran, demnächst zu bezahlen :)')</script>";
        $_SESSION['bezahlt'] = 1;
}




?>


<!DOCTYPE html>
<html lang="de">
<head>
  <title><?php echo get_wettbewerb_title();?></title> <!-- hier aus db -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
  <!--<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">-->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
  <script src="src/include/scripts/tabelle.js"></script>

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
                <!--<li class="nav-item">
                    <a class="nav-link" href="?index=3#main">Restprogramm</a>
                </li>-->
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
    
        if ((!check_cash($g_wett_id)) && (is_logged())) {  
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
    <h1> Willkommen zum <?php echo get_wettbewerb_title();?>! </h1> 
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
    <div class="row centering">
    
        <!-- NICHT SICHTBAR; QUASI LINKER RAHMEN -->
        <div class="col-lg-1 d-none d-xl-block d-lg-block text-center">
            <hr class="d-sm-none">
        </div>
        
        <!-- Linkes Fenster, Standard Rangliste, sonst ...? mobil nicht sichtbar! -->
        <div class="col-lg-3 d-none d-xl-block d-lg-block text-center fenster rounded">
            <?php
                if (($index != 4) && ($index != 2)){
                    echo "<h2>Rangliste:</h2>";
                    include_once("src/pages/rangliste.php");
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
        <div class="col-lg-1 d-none d-xl-block d-lg-block text-center">
            <hr class="d-sm-none">
        </div>
    
        <!-- HAUPTFELD  -->
        <div class="col-lg-6 hidden-md-up fenster text-center rounded">
      
            <?php
                if ($index == "") {
                    include_once("src/pages/hello.php");
                }
      
                if ($index == 1) {
                    echo "<h2> Bundesliga-Tabelle</h2>";
                    include_once("src/pages/tabelle.php");
                }
      
                if ($index == 2) {
                    echo "<h2>Spieltage</h2>";
                    include_once("src/pages/spieltag.php");
                }

                if ($index == 3) {
                    echo "<h2>Rstprogramm</h2>";
                    include_once("src/pages/restprogramm.php");
                }

                if ($index == 4) {
                    echo "<h2>Rangliste</h2>";
                    include_once("src/pages/rangliste.php");
                }
  
                if ($index == 5) {
                    echo "<h2>Tipps</h2>";
                    include_once("src/pages/tipp.php");
                }
                if ($index == 6) {
                    echo "<h2>Ergebnisse eingeben</h2>";
                    include_once("src/pages/ergebnis.php");
                }           
                if ($index == 7) {
                    echo "<h2>Mein Konto</h2>";
                    include_once("src/konto.php");
                }                 
                if ($index == 8) {
                    echo "<h2>Datum eingeben</h2>";
                    include_once("src/set_date.php");
                    echo "<h2>Spiele terminieren</h2>";
                    include_once("src/spiele_term.php");
                } 
                
                if ($index == 9) {
                    echo "<h2>Verwaltung</h2>";
                    include_once("src/pages/user.php");
                } 
                
                if ($index == 10) {
                    echo "<h2>Gewinnverteilung</h2>";
                    include_once("src/pages/gewinn.php");
                }    
                
                if ($index == 11) {
                    echo "<h2>FAQ</h2>";
                    include_once("src/pages/faq.php");
                } 
                
                if ($index == 15) {
                    echo "<h2>Statistiken</h2>";
                    include_once("src/stats3.php");
                } 
                ?>
        </div>
    </div>
</div>



<div class="jumbotron text-center grey" style="margin-bottom:0">
    <p>&copy; couchtipper.de v4.0</p>
</div>


</body>
</html>
 
