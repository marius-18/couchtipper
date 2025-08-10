<div class="container-fluid hintergrund" style="margin-top:0px; padding-bottom:55px" id="main">
    <div class="row centering justify-content-around">
    <!-- NICHT SICHTBAR; QUASI LINKER RAHMEN -->
        <div class="col-lg-1 d-none d-lg-block d-lg-block text-center">
            <hr class="d-sm-none">
        </div>
        
        <div class="col-lg-10 d-none d-xl-block d-lg-block text-center fenster rounded main">
 
 <h2>Neuen Wettbewerb anlegen</h2>


<div class="container-fluid">

<?php

if (!allow_main_verwaltung()){
    echo "<div class=\"alert alert-danger\"> Dieser Bereich ist <strong>nur f&uuml;r Administratoren</strong>!<br>
        Frage beim Administrator nach, um Rechte zum &Auml;ndern von Rechten zu bekommen.</div>";
    echo "</div>";
    return 0;
}

include_once("src/setup/new_wettbewerb.inc.php");

$nextId = get_max_wett_id() + 1;

// Hole die Saison ID aus dem GET.
$saison_id = $_GET["saison_id"] ?? null;
if ($saison_id === null) {
    ## Wenn keine Saison ID übergeben wurde => Neue Saison wird erstellt
    $saison_id = $nextId;
}

## Hinrunde oder Rückrunde
$runde = $_POST['runde'] ?? '0';
    
## UPDATE THE DATABASE
$update_msg_create = create_db_entries_update($saison_id);
$update_msg_edit = edit_db_entries_update($saison_id);
$update_msg_copy = copy_db_entries_update($saison_id);


?>



<?php


echo get_saison_select_form_html($saison_id);


// Helper: Querystring bauen
function qs(array $overrides = []) {
    $params = array_merge($_GET, $overrides);
    return '?' . http_build_query($params);
}

// Tabs einmalig pflegen – alles andere passt sich automatisch an
$tabs = [
  1 => '1. DB-Zugang',
  2 => '2. Kopieren',
  3 => '3. Bearbeiten',
  4 => '4. Saison erstellen',
  // 5 => '5. …',
];

// Schritt bestimmen & validieren
$orderedKeys = array_keys($tabs);
sort($orderedKeys, SORT_NUMERIC);
$firstKey = $orderedKeys[0];
$lastKey  = $orderedKeys[count($orderedKeys)-1];

$step = isset($_GET['step']) ? (int)$_GET['step'] : $firstKey;
if (!in_array($step, $orderedKeys, true)) $step = $firstKey;

$pos     = array_search($step, $orderedKeys, true);               // 0-basiert
$percent = (int)round((($pos+1)/count($orderedKeys))*100);
$prevKey = $orderedKeys[max(0, $pos-1)];
$nextKey = $orderedKeys[min(count($orderedKeys)-1, $pos+1)];
?>



<div class="container my-3">
  <ul class="nav nav-pills nav-justified mb-3">
    <?php foreach ($orderedKeys as $k): ?>
      <li class="nav-item">
        <a class="nav-link <?= $step === $k ? 'active' : '' ?>" href="<?= htmlspecialchars(qs(['step'=>$k])) ?>">
          <?= htmlspecialchars($tabs[$k]) ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>

  <div class="progress mb-3">
    <div class="progress-bar" role="progressbar"
         style="width: <?= $percent ?>%;"
         aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
  </div>

  <div>
    <?php
    // Nur hier im switch Inhalte pflegen/erweitern
    switch ($step) {
      case 1:
        echo create_db_entries_form_html($saison_id);
        echo "<br>", $update_msg_create;
        break;

      case 2:
        echo copy_db_entries_form_html($saison_id);
        echo "<br>", $update_msg_copy;
        break;

      case 3:
        echo edit_db_entries_form_html($saison_id, $runde);
        echo "<br>", $update_msg_edit;
        break;

      case 4:
        echo "<div class=\"alert alert-info\"><strong>Erfolg!</strong> Die Datenbankzugänge wurden jetzt richtig erstellt. Jetzt muss die Datenbank noch gefüllt werden. Weiter gehts hier!<br><br>";
        echo '<a href="?year='.$saison_id.'" class="btn btn-primary">Saison erstellen</a>';
        echo "</div> <br>";
        break;
    }
    ?>
  </div>

  <div class="d-flex justify-content-between mt-3">
    <a class="btn btn-outline-secondary <?= $step === $firstKey ? 'disabled' : '' ?>"
       href="<?= htmlspecialchars(qs(['step'=>$prevKey])) ?>">Zurück</a>

    <a class="btn btn-primary <?= $step === $lastKey ? 'disabled' : '' ?>"
       href="<?= htmlspecialchars(qs(['step'=>$nextKey])) ?>">Weiter</a>
  </div>
</div>









</div><!-- End Container -->


</div><!-- End Fenster -->

<!-- NICHT SICHTBAR; QUASI LINKER RAHMEN -->
<div class="col-lg-1 d-none d-lg-block d-lg-block text-center">
    <hr class="d-sm-none">
</div>
    
</div><!-- End Row -->
</div><!-- End Background -->
