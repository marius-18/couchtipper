<?php

function print_pages(){
    global $index;
    ## Im Übersicht Modus die entsprechenden Seiten anzeigen.
    if (get_wettbewerb_code(get_curr_wett()) == "Übersicht"){
        switch ($index) {
            case "":
                include_once("src/pages/hello_overview.php");
                break;
            case 1:
                echo "<h2>Gesamt Tabelle</h2>";
                include_once("src/gesamt_tabelle.php");
                break;
            case 2:
                echo "<h2>Punkte nach Saison</h2>";
                include_once("src/gesamt_tabelle.php");
                break;
            case 3:
                echo "<h2>Platzierungen nach Saison</h2>";
                include_once("src/gesamt_tabelle.php");
                break;
            default:
                include_once("src/pages/error.php");
                break;
        }
        return 0;
    }

    ## Hier für Verwaltung!
    if ((get_wettbewerb_code(get_curr_wett()) == "Verwaltung") ||
    (check_if_db_empty() || (isset($_GET["setup"]) &&  $_GET["setup"] == 1))) {
        switch ($index) {
            case "":
                include_once("src/pages/hello_overview.php");
                break;
            case 1:
                echo "<h2>Neuen Wettbewerb anlegen</h2>";
                include_once("src/pages/system_verwaltung.php");
                break;
        }
        return 0;
    }

    ## Standard Anzeigen
    switch ($index) {
        case "":
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
            #include_once("src/pages/tabellenverlauf.php");
            #include_once("src/newbot.php");
            break;
        default:
            include_once("src/pages/error.php");
            break;
    }

}

function print_pages_left(){
    global $index;
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
}

function print_nav(){
    if ((get_wettbewerb_code(get_curr_wett()) == "Verwaltung") ||
    (check_if_db_empty() || (isset($_GET["setup"]) &&  $_GET["setup"] == 1))){
        echo "
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"?index=1#main\">Neuen Wettbewerb anlegen</a>
            </li>";
        return 0;
    }

    if (get_wettbewerb_code(get_curr_wett()) == "Übersicht"){
        ## Überblicks Seite!
        echo "
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"?index=1#main\">All-Time Tabelle</a>
            </li>";

        echo "
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"?index=2#main\">Punkte nach Saisons</a>
            </li>";

        echo "
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"?index=3#main\">Platzierung nach Saisons</a>
            </li>";
        return 0;
    }

    ## In BuLi nur die Tabelle anzeigen
    if (get_wettbewerb_code(get_curr_wett()) == "BuLi"){
        echo "
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"?index=1#main\">Bundesliga-Tabelle</a>
            </li>";
    }

    ## Bei WM/EM Gruppenphase und KO-Runde anzeigen
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

    ## Spieltage
    echo "
        <li class=\"nav-item\">
            <a class=\"nav-link\" href=\"?index=2#main\">Spieltage</a>
        </li>";

    ## Restliche Spiele (nur bei BuLi)
    if (!is_big_tournament(get_curr_wett())) {
        echo "
            <li class=\"nav-item\">
                <a class= \"nav-link\" href=\"?index=3#main\">Restprogramm</a>
            </li>";
    }

    ## Rangliste
    echo "
        <li class=\"nav-item\">
            <a class=\"nav-link\" href=\"?index=4#main\">Rangliste</a>
        </li>";

    ## Tipps (nur angemeldet!)
    if (is_logged()){
        echo "
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"?index=5#main\">Tipps</a>
            </li>";
    }

    ## Ergebnisse ändern (nur mit Berechtigung!)
    if (allow_erg()){
        echo "
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"?index=6#main\">Ergebnisse</a>
            </li>";
    }

    ## Tagessieger (nur BuLi und angemeldet)
    if (is_logged() && !is_big_tournament(get_curr_wett())) {
        echo "
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"?index=12#main\">Tagessieger</a>
            </li>";
    }

    ## Gewinnverteilung (nur angemeldet)
    if (is_logged()){
        echo "
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"?index=10#main\">Gewinnverteilung</a>
            </li>";
    }

    ## Konto Verwaltung (nur angemdeldet & aktiver Wettbewerb)
    if (is_logged() && (is_active_wettbewerb())){
        echo "
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"?index=7#main\">Mein Konto</a>
            </li>";
    }

    ## Spiele terminieren (nur mit Berechtigung)
    if (allow_date()){
        echo "
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"?index=8#main\">Spiele terminieren</a>
            </li>";
    }

    ## Verwaltung (nur mit Berechtigung)
    if (allow_verwaltung()){
        echo "
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"?index=9#main\">Verwaltung</a>
            </li>";
    }

    ## Statistiken # TODO:
    if (allow_erg()){
        echo "
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"?index=15#main\">Stats</a>
            </li>";
    }

    ## FAQ
    echo "
        <li class=\"nav-item\">
            <a class=\"nav-link\" href=\"?index=11#main\">FAQ</a>
        </li>";

}



function print_year_dropdown($url_suffix_no_year){
    ?>
    <!-- ## TODO: Automatisieren! -->
    <div class="dropdown-header">Aktuelle Saison</div>
    <a class="dropdown-item" href="?<?php echo $url_suffix_no_year;?>year=9" style="color:black">BuLi 2025/26</a>
    <a class="dropdown-item" href="?<?php echo $url_suffix_no_year;?>year=8" style="color:black">BuLi 2024/25</a>
    <div class="dropdown-divider"></div>
    <div class="dropdown-header">Vergangene</div>
    <a class="dropdown-item" href="?<?php echo $url_suffix_no_year;?>year=7" style="color:black">EM 2024</a>
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

    if (allow_main_verwaltung()){
        echo "<div class=\"dropdown-divider\"></div>
              <div class=\"dropdown-header\">Sonstiges</div>
              <a class=\"dropdown-item\" href=\"?year=-11\" style=\"color:black\">Verwaltung</a>";
        echo "
              <a class=\"dropdown-item\" href=\"?year=-10\" style=\"color:black\">Übersicht</a>";
    }
}

?>
