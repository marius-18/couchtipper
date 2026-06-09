<link href='src/include/styles/ko1.css' rel='stylesheet'/>
<script>
$(document).ready(function(){
  $('[data-toggle="popover"]').popover();
});
</script>

<div class="container-fluid">
<?php
require_once('src/include/code/refresh.php');

$mannschaften = ko_mannschaften();
$maxEbene = log($mannschaften, 2);
$gesamtgroesse = 1.5 * $mannschaften;

print_ko($mannschaften, $maxEbene, $gesamtgroesse);

function ko_jahr(){
    return (string)get_wettbewerb_jahr(get_curr_wett());
}

function ko_code(){
    return (string)get_wettbewerb_code(get_curr_wett());
}

function is_wm_2026(){
    return ko_code() == "WM" && ko_jahr() == "2026";
}

function ko_mannschaften(){
    return is_wm_2026() ? 32 : 16;
}

function print_ko($mannschaften, $maxEbene, $gesamtgroesse){
    echo "<div class=\"table-responsive\">";
    echo "<table class=\"table table-sm text-nowrap table-borderless text-center center\">";

    for($zeile = 1; $zeile <= $gesamtgroesse; $zeile++) {
        echo "<tr>";
        for($ebene = 1; $ebene <= $maxEbene; $ebene++) {
            zelle($zeile, $ebene);
        }
        echo "</tr>";
    }
    echo "</table></div>";
}

function zelle($zeile, $spalte){
    $start_oben = ceil(pow(2,$spalte-2) * 3);
    $start_unten = $start_oben + 1;
    $start_title = $start_oben - 1;
    $counter = pow(2,$spalte-1) * 3;

    if ((($zeile - $start_oben) % $counter) == 0) {
        echo team_label_for_current_wettbewerb($zeile, $spalte, "top");
        return;
    }

    if ((($zeile - $start_unten) % $counter) == 0) {
        echo team_label_for_current_wettbewerb($zeile, $spalte, "down");
        return;
    }

    if ((($zeile - $start_title) % $counter) == 0) {
        echo "<td colspan=\"2\">";
        label($zeile,$spalte,"name");
        echo "</td>";
        return;
    }

    echo "<td></td><td></td>";
}

function team_label_for_current_wettbewerb($zeile, $spalte, $mode){
    if (is_wm_2026()) {
        return team_label_2026($zeile, $spalte, $mode);
    }

    if (ko_code() == "WM" && ko_jahr() == "2018") {
        return team_label_2018($zeile, $spalte, $mode);
    }

    return team_label($zeile, $spalte, $mode);
}

function ko_match_index($zeile, $spalte){
    return (int)ceil($zeile / (3 * pow(2, $spalte - 1)));
}

function ko_logical_match_2026($spalte, $visual_spiel){
    // Reihenfolge im Baum nach Wikipedia-Überblick, damit die Anschluss-Spiele
    // wirklich nebeneinander stehen (z. B. AF 1 = Sieger 16F 1 - Sieger 16F 4).
    $order = [
        1 => [1 => 3, 2 => 6, 3 => 1, 4 => 4, 5 => 12, 6 => 11, 7 => 10, 8 => 9,
              9 => 2, 10 => 5, 11 => 7, 12 => 8, 13 => 15, 14 => 14, 15 => 13, 16 => 16],
        2 => [1 => 2, 2 => 1, 3 => 5, 4 => 6, 5 => 3, 6 => 4, 7 => 7, 8 => 8],
        3 => [1 => 1, 2 => 2, 3 => 3, 4 => 4],
        4 => [1 => 1, 2 => 2],
        5 => [1 => 1]
    ];

    return isset($order[$spalte][$visual_spiel]) ? $order[$spalte][$visual_spiel] : $visual_spiel;
}

function ko_team_pos($mode){
    return $mode == "top" ? 1 : 2;
}

function ko_round_name($spalte){
    if (is_wm_2026()) {
        $names = [1 => "16F", 2 => "AF", 3 => "VF", 4 => "HF", 5 => "Finale"];
    } else {
        $names = [1 => "AF", 2 => "VF", 3 => "HF", 4 => "Finale"];
    }

    return isset($names[$spalte]) ? $names[$spalte] : "";
}

function ko_real_match_2026($runde, $spiel){
    // WM 2026 nach Wikipedia/FIFA-KO-Nummerierung.
    // Rueckgabe: [spieltag, sp_nr] aus der Datenbank. Die Spieltage sind nach deutschem Datum nummeriert.
    // Wichtig: Die Baum-Nummer ist die KO-Spielnummer, nicht zwingend eine einfache Paarung benachbarter Zeilen.
    $matches = [
        1 => [
            1 => [18,1],  // 16F 1:  2A - 2B
            2 => [19,1],  // 16F 2:  1C - 2F
            3 => [19,2],  // 16F 3:  1E - 3A/B/C/D/F
            4 => [19,3],  // 16F 4:  1F - 2C
            5 => [20,1],  // 16F 5:  2E - 2I
            6 => [20,2],  // 16F 6:  1I - 3C/D/F/G/H
            7 => [20,3],  // 16F 7:  1A - 3C/E/F/H/I
            8 => [21,1],  // 16F 8:  1L - 3E/H/I/J/K
            9 => [21,2],  // 16F 9:  1G - 3A/E/H/I/J
           10 => [21,3],  // 16F 10: 1D - 3B/E/F/I/J
           11 => [22,1],  // 16F 11: 1H - 2J
           12 => [22,2],  // 16F 12: 2K - 2L
           13 => [22,3],  // 16F 13: 1B - 3E/F/G/I/J
           14 => [23,1],  // 16F 14: 2D - 2G
           15 => [23,2],  // 16F 15: 1J - 2H
           16 => [23,3]   // 16F 16: 1K - 3D/E/I/J/L
        ],
        2 => [
            1 => [24,1], // AF 1: Sieger 16F 1 - Sieger 16F 4
            2 => [24,2], // AF 2: Sieger 16F 3 - Sieger 16F 6
            3 => [25,1], // AF 3: Sieger 16F 2 - Sieger 16F 5
            4 => [25,2], // AF 4: Sieger 16F 7 - Sieger 16F 8
            5 => [26,1], // AF 5: Sieger 16F 12 - Sieger 16F 11
            6 => [26,2], // AF 6: Sieger 16F 10 - Sieger 16F 9
            7 => [27,1], // AF 7: Sieger 16F 15 - Sieger 16F 14
            8 => [27,2]  // AF 8: Sieger 16F 13 - Sieger 16F 16
        ],
        3 => [
            1 => [28,1], // VF 1: Sieger AF 2 - Sieger AF 1
            2 => [29,1], // VF 2: Sieger AF 5 - Sieger AF 6
            3 => [30,1], // VF 3: Sieger AF 3 - Sieger AF 4
            4 => [30,2]  // VF 4: Sieger AF 7 - Sieger AF 8
        ],
        4 => [
            1 => [31,1], // HF 1: Sieger VF 1 - Sieger VF 2
            2 => [32,1]  // HF 2: Sieger VF 3 - Sieger VF 4
        ],
        5 => [1 => [34,1]] // Finale
    ];

    return isset($matches[$runde][$spiel]) ? $matches[$runde][$spiel] : null;
}

function ko_fallback_2026($runde, $spiel, $team_pos){
    $fallbacks = [
        1 => [
            1 => ["2. Gruppe A", "2. Gruppe B"],
            2 => ["1. Gruppe C", "2. Gruppe F"],
            3 => ["1. Gruppe E", "3. Gruppe A/B/C/D/F"],
            4 => ["1. Gruppe F", "2. Gruppe C"],
            5 => ["2. Gruppe E", "2. Gruppe I"],
            6 => ["1. Gruppe I", "3. Gruppe C/D/F/G/H"],
            7 => ["1. Gruppe A", "3. Gruppe C/E/F/H/I"],
            8 => ["1. Gruppe L", "3. Gruppe E/H/I/J/K"],
            9 => ["1. Gruppe G", "3. Gruppe A/E/H/I/J"],
           10 => ["1. Gruppe D", "3. Gruppe B/E/F/I/J"],
           11 => ["1. Gruppe H", "2. Gruppe J"],
           12 => ["2. Gruppe K", "2. Gruppe L"],
           13 => ["1. Gruppe B", "3. Gruppe E/F/G/I/J"],
           14 => ["2. Gruppe D", "2. Gruppe G"],
           15 => ["1. Gruppe J", "2. Gruppe H"],
           16 => ["1. Gruppe K", "3. Gruppe D/E/I/J/L"]
        ],
        2 => [
            1 => ["Sieger 16F 1", "Sieger 16F 4"],
            2 => ["Sieger 16F 3", "Sieger 16F 6"],
            3 => ["Sieger 16F 2", "Sieger 16F 5"],
            4 => ["Sieger 16F 7", "Sieger 16F 8"],
            5 => ["Sieger 16F 12", "Sieger 16F 11"],
            6 => ["Sieger 16F 10", "Sieger 16F 9"],
            7 => ["Sieger 16F 15", "Sieger 16F 14"],
            8 => ["Sieger 16F 13", "Sieger 16F 16"]
        ],
        3 => [
            1 => ["Sieger AF 2", "Sieger AF 1"],
            2 => ["Sieger AF 5", "Sieger AF 6"],
            3 => ["Sieger AF 3", "Sieger AF 4"],
            4 => ["Sieger AF 7", "Sieger AF 8"]
        ],
        4 => [
            1 => ["Sieger VF 1", "Sieger VF 2"],
            2 => ["Sieger VF 3", "Sieger VF 4"]
        ],
        5 => [1 => ["Sieger HF 1", "Sieger HF 2"]]
    ];

    return isset($fallbacks[$runde][$spiel][$team_pos-1]) ? $fallbacks[$runde][$spiel][$team_pos-1] : "TBD";
}

function ko_is_placeholder_name_2026($name){
    if (!is_string($name)) {
        return false;
    }

    return preg_match('/^(Sieger|Verlierer|Zweiter|Bester Dritter)(\s|$)/u', $name) === 1;
}

function help($i){
    if (ko_jahr() == "2020"){
        $array = [1 => 5, 2 => 6, 3 => 2, 4 => 4, 5 => 1, 6 => 3, 7 => 7, 8 => 8];
    } elseif (ko_jahr() == "2018"){
        $array = [1 => 2, 2 => 1, 3 => 5, 4 => 6, 5 => 3, 6 => 4, 7 => 7, 8 => 8];
    } else {
        // 2024 und ältere EM-Logik
        $array = [1 => 4, 2 => 2, 3 => 6, 4 => 5, 5 => 7, 6 => 8, 7 => 3, 8 => 1];
    }

    return $array[$i];
}

function help_vf($i){
    if (ko_jahr() == "2024"){
        $array = [1 => 1, 2 => 2, 3 => 4, 4 => 3];
        return $array[$i];
    }
    return $i;
}

function label($zeile, $spalte, $typ){
    if ($typ != "name") {
        return;
    }

    if (is_wm_2026()) {
        $visual_spiel = ko_match_index($zeile, $spalte);
        $spiel = ko_logical_match_2026($spalte, $visual_spiel);
        $name = ko_round_name($spalte);

        if ($spalte == 5) {
            echo "Finale<a data-toggle=\"popover\" title=\"Finale\" data-content=\"".datum($spalte, 1)."\"> <i class=\"far fa-clock\"></i></a>";
        } else {
            echo $name." ".$spiel;
            echo "<a data-toggle=\"popover\" title=\"".$name." ".$spiel."\" data-content=\" ".datum($spalte, $spiel)."\"> <i class=\"far fa-clock\"></i></a>";
        }
        return;
    }

    switch ($spalte){
        case 1:
            echo "AF ".help(($zeile+2)/3);
            echo "<a data-toggle=\"popover\" title=\"Achtelfinale ".help(($zeile+2)/3)."\" data-content=\" ".datum(1,help(($zeile+2)/3))."\"> <i class=\"far fa-clock\"></i></a>";
            break;
        case 2:
            echo "VF ".help_vf(($zeile+4)/6);
            echo "<a data-toggle=\"popover\" title=\"Viertelfinale ".help_vf(($zeile+4)/6)."\" data-content=\" ".datum(2,help_vf(($zeile+4)/6))."\"> <i class=\"far fa-clock\"></i></a>";
            break;
        case 3:
            echo "HF ".(($zeile+7)/12);
            $hf_nr = (($zeile+7)/12);
            echo "<a data-toggle=\"popover\" title=\"Halbfinale ".$hf_nr."\" data-content=\" ".datum(3,$hf_nr)."\"> <i class=\"far fa-clock\"></i></a>";
            break;
        case 4:
            echo "Finale<a data-toggle=\"popover\" title=\"Finale\" data-content=\"".datum(4,1)."\"> <i class=\"far fa-clock\"></i></a>";
            break;
    }
}

function teams($spieltag, $spiel, $sonst, $team_pos, $mode){
    global $g_pdo;

    $spieltag = (int)$spieltag;
    $spiel = (int)$spiel;
    $team_pos = (int)$team_pos;

    $team = [1 => "TBD", 2 => "TBD"];

    $sql = "SELECT t1.team_name AS team1, t2.team_name AS team2 FROM `Spieltage`, Teams AS t1, Teams AS t2 WHERE
           ((team1 = t1.team_nr) AND (team2 = t2.team_nr) AND (spieltag = $spieltag) AND (sp_nr = $spiel) )";

    foreach ($g_pdo->query($sql) as $row) {
        $team[1] = $row['team1'];
        $team[2] = $row['team2'];
    }

    // Für WM 2026 sollen die lokalen Baum-Bezeichnungen maßgeblich sein.
    // In der DB können Platzhalter wie "Sieger 16F 3" aus einer anderen Sortierung stehen.
    if (is_wm_2026() && ko_is_placeholder_name_2026($team[$team_pos])) {
        $tm = $sonst;
    } else {
        $tm = ($team[$team_pos] != "TBD") ? $team[$team_pos] : $sonst;
    }
    $erg = erg($spieltag, $spiel, $team_pos);

    return "<td class=\"erg_$mode\">$erg</td><td class=\"spiel_$mode\"> ".$tm."</td>";
}

function erg($spieltag,$spiel,$team_pos){
    global $g_pdo;

    $spieltag = (int)$spieltag;
    $spiel = (int)$spiel;
    $team_pos = (int)$team_pos;
    $tore = [1 => "", 2 => ""];

    $sql = "SELECT tore1, tore2 FROM Ergebnisse WHERE ((spieltag = $spieltag) AND (sp_nr = $spiel))";
    foreach ($g_pdo->query($sql) as $row) {
        $tore[1] = $row['tore1'];
        $tore[2] = $row['tore2'];
    }

    return $tore[$team_pos];
}

function datum($spieltag, $spiel){
    global $g_pdo;

    if (is_wm_2026()) {
        $erg = ko_real_match_2026((int)$spieltag, (int)$spiel);
    } else {
        // Legacy-Übersetzung: alte EM/WM-KO-Spiele mit 16 Teams.
        $array = [
            1 => [1 => [14,1], 2 => [14,2], 3 => [15,1], 4 => [15,2], 5 => [16,1], 6 => [16,2], 7 => [17,1], 8 => [17,2]],
            2 => [1 => [18,1], 2 => [18,2], 3 => [19,1], 4 => [19,2]],
            3 => [1 => [20,1], 2 => [21,1]],
            4 => [1 => [22,1]]
        ];
        $erg = isset($array[$spieltag][$spiel]) ? $array[$spieltag][$spiel] : null;
    }

    if ($erg === null) {
        return "";
    }

    $real_spt = (int)$erg[0];
    $real_spl = (int)$erg[1];

    $date = "";
    $spielort = "";
    $sql = "SELECT datum1, stadt FROM `Spieltage`, `Spielorte` WHERE spieltag = $real_spt AND sp_nr = $real_spl AND `Spielorte`.id = `Spieltage`.spielort";
    foreach ($g_pdo->query($sql) as $row) {
        $date = $row['datum1'];
        $spielort = $row['stadt'];
    }

    return ($date !== "" ? stamp_to_date_programm($date) : "") . " " . $spielort;
}

function team_label_2026($zeile, $spalte, $mode){
    $visual_spiel = ko_match_index($zeile, $spalte);
    $spiel = ko_logical_match_2026($spalte, $visual_spiel);
    $team_pos = ko_team_pos($mode);
    $real = ko_real_match_2026($spalte, $spiel);

    if ($real === null) {
        return "<td></td><td></td>";
    }

    return teams($real[0], $real[1], ko_fallback_2026($spalte, $spiel, $team_pos), $team_pos, $mode);
}

function team_label($zeile, $spalte, $mode){
    if ($spalte == 1){
        $af_zeile = help(ceil($zeile/3));
        $af_spieltage = [0,14,15,16,17];
        $af_spieltag = $af_spieltage[ceil($af_zeile/2)];
        $af_spiel = ($af_zeile+1)%2+1;
        $af_position = ($zeile+1)%3+1;

        $AF = [
            1 => ["","2. Gruppe A","2. Gruppe B"],
            2 => ["","1. Gruppe A","2. Gruppe C"],
            3 => ["","1. Gruppe C","3. Gruppe D/E/F"],
            4 => ["","1. Gruppe B","3. Gruppe A/D/E/F"],
            5 => ["","2. Gruppe D","2. Gruppe E"],
            6 => ["","1. Gruppe F","3. Gruppe A/B/C"],
            7 => ["","1. Gruppe E","3. Gruppe A/B/C/D"],
            8 => ["","1. Gruppe D","2. Gruppe F"],
        ];

        return teams($af_spieltag,$af_spiel,$AF[$af_zeile][$af_position],$af_position, $mode);
    }

    if ($spalte == 2){
        $vf_zeile = ceil($zeile/3);
        $vf_spt = ($vf_zeile <= 4) ? 18 : 19;
        $vf_team_pos = (($vf_zeile+1) % 2)+1;
        $vf_spiel = ((ceil($vf_zeile/2)+1) % 2)+1;

        if (($vf_spt == 19) && ($vf_spiel == 1)){
            $vf_spiel = 2;
        } elseif (($vf_spt == 19) && ($vf_spiel == 2)) {
            $vf_spiel = 1;
        }

        return teams($vf_spt,$vf_spiel,"Sieger AF ".help($vf_zeile),$vf_team_pos, $mode);
    }

    if ($spalte == 3){
        switch (ceil($zeile/6)) {
            case 1: return teams(20,1,"Sieger VF 1",1, $mode);
            case 2: return teams(20,1,"Sieger VF 2",2, $mode);
            case 3: return teams(21,1,"Sieger VF 4",1, $mode);
            case 4: return teams(21,1,"Sieger VF 3",2, $mode);
        }
    }

    if ($spalte == 4){
        switch (ceil($zeile/12)) {
            case 1: return teams(22,1,"Sieger HF 1",1, $mode);
            case 2: return teams(22,1,"Sieger HF 2",2, $mode);
        }
    }

    return "<td></td><td></td>";
}

function team_label_2018($zeile, $spalte, $mode){
    if ($spalte == 1){
        switch ($zeile) {
            case 2: return teams(16,2,"2. Gruppe D",1, $mode);
            case 3: return teams(16,2,"2. Gruppe E",2, $mode);
            case 5: return teams(16,1,"1. Gruppe F",1, $mode);
            case 6: return teams(16,1,"3. Gruppe A/B/C ",2, $mode);
            case 8: return teams(18,1,"1. Gruppe A",1, $mode);
            case 9: return teams(18,1,"2. Gruppe C",2, $mode);
            case 11: return teams(18,2,"1. Gruppe B",1, $mode);
            case 12: return teams(18,2,"3. Gruppe A/D/E/F",2, $mode);
            case 14: return teams(17,1,"2. Gruppe A",1, $mode);
            case 15: return teams(17,1,"2. Gruppe B",2, $mode);
            case 17: return teams(17,2,"1. Gruppe C",1, $mode);
            case 18: return teams(17,2,"3. Gruppe D/E/F",2, $mode);
            case 20: return teams(19,1,"1. Gruppe D",1, $mode);
            case 21: return teams(19,1,"2. Gruppe F",2, $mode);
            case 23: return teams(19,2,"1. Gruppe E",1, $mode);
            case 24: return teams(19,2,"3. Gruppe A/B/C/D",2, $mode);
        }
        return "<td class=\"erg_$mode\">1</td><td class=\"spiel_$mode\"> Sieger Gruppe X</td>";
    }

    if ($spalte == 2){
        switch (ceil($zeile/3)) {
            case 1: return teams(20,1,"Sieger AF 5",1, $mode);
            case 2: return teams(20,1,"Sieger AF 6",2, $mode);
            case 3: return teams(20,2,"Sieger AF 2",1, $mode);
            case 4: return teams(20,2,"Sieger AF 4",2, $mode);
            case 5: return teams(21,2,"Sieger AF 1",1, $mode);
            case 6: return teams(21,2,"Sieger AF 3",2, $mode);
            case 7: return teams(21,1,"Sieger AF 7",1, $mode);
            case 8: return teams(21,1,"Sieger AF 8",2, $mode);
        }
    }

    if ($spalte == 3){
        switch (ceil($zeile/6)) {
            case 1: return teams(22,1,"Sieger VF 1",1, $mode);
            case 2: return teams(22,1,"Sieger VF 2",2, $mode);
            case 3: return teams(23,1,"Sieger VF 3",1, $mode);
            case 4: return teams(23,1,"Sieger VF 4",2, $mode);
        }
    }

    if ($spalte == 4){
        switch (ceil($zeile/12)) {
            case 1: return teams(25,1,"Sieger HF 1",1, $mode);
            case 2: return teams(25,1,"Sieger HF 2",2, $mode);
        }
    }

    return "<td></td><td></td>";
}
?>
</div>
