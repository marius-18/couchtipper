<?php
require_once('src/include/code/tabelle.inc.php');
require_once('src/include/code/get_games.inc.php');
require_once('src/include/code/print_games.php');
require_once('src/include/lib/precomputation.inc.php');


function get_groups(){
  ## Definiere die Gruppen für die einzelnen Wettbewerbe
  if (get_wettbewerb_code(get_curr_wett())  == "WM"){
    if (get_wettbewerb_jahr(get_curr_wett()) == 2018){
      $groups = ["A", "B", "C", "D", "E", "F", "G", "H"];
    }

    if (get_wettbewerb_jahr(get_curr_wett()) == 2026){
      $groups = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L"];
    }
  }

  if (get_wettbewerb_code(get_curr_wett())  == "EM"){
    $groups = ["A", "B", "C", "D", "E", "F"];
  }

  return $groups;
}


function show_main_groups(){
  ## Haupt Seite "Gruppenphase" mit Tabelle und allen Spielen

  echo '<div class="container-fluid text-center" >';

  $gruppen = get_groups();

  ## Zeige das selection formular für die Gruppen an:
  select_gruppe($gruppen);

  ## Gehe einzelne Gruppen durch
  foreach ($gruppen as $gruppe){
    ## Nur Gruppe A wird zu Beginn angezeigt
    if ($gruppe == "A"){
      $show = "block";
    } else {
      $show = "none";
    }

    ## Zeige Tabelle und Spiele an
    echo "<div style='display: ".$show.";' id=\"group".$gruppe."\" class=\"big_tournament_group\">";
      print_wm_tabelle(wm_tabelle($gruppe));
      print_gruppe($gruppe);
    echo "</div>
    ";
  }

  echo "</div>";
}

function show_small_group_overview(){
  ## Zeigt alle Tabellen untereinander an (für die linke Spalte)

  echo '<div class="container-fluid text-center" >';

  $gruppen = get_groups();

  foreach ($gruppen as $gruppe){
    echo "<h5>Gruppe $gruppe</h5>";
    print_wm_tabelle(wm_tabelle($gruppe));
  }

  echo "</div>";
}


?>

