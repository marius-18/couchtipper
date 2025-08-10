<?php
## Falls die Seite nicht über index.php aufgerufen wird, gibt es hier einen Abbruch!
if (!isset($global_wett_id)){
    echo "Du solltest hier nicht sein! Hier geht es ";
    echo '<a href="/" class="btn btn-primary">Zurück</a>.';
    exit;
}
include_once("src/setup/setup.inc.php");
?>

<!-- 
## Main Window
-->
<div class="container-fluid hintergrund" style="padding-bottom:30px; padding-top:30px;" >
    <div class="container-fluid text-center fenster rounded main p-1">
        <?php echo render_installer_menu(); ?>
    </div>
    <br>
</div>


<!-- 
## Footer
-->
<div class="jumbotron text-center grey" style="margin-bottom:0">
    <br>
    <p>&copy; couchtipper.de</p>
</div>
</body>
</html>



<?php
function render_installer_menu() {
    ## Hauptfunktion, die alles anzeigt
    // >>> Hier nur Tabs anpassen (Key = Schritt, Value = Label)
    $tabs = [
        0 => '0. Start',
        1 => '1. Datenbank laden',
        2 => '2. Teams aktualisieren',
        3 => '3. Datum einlesen',
        4 => '4. Spieltage einlesen',
        5 => '5. Tabelle generieren',
        6 => '6. Benutzer eintragen',
    ];
    // <<<

    // Utility: Querystring bauen
    $qs = function(array $overrides = []) {
        $params = array_merge($_GET, $overrides);
        return '?' . http_build_query($params);
    };

    // Schritt bestimmen und auf gültige Keys mappen
    $orderedKeys = array_keys($tabs);
    sort($orderedKeys, SORT_NUMERIC);
    $firstKey = $orderedKeys[0];
    $lastKey  = $orderedKeys[count($orderedKeys) - 1];

    $step = isset($_GET['step']) ? (int)$_GET['step'] : $firstKey;
    if (!in_array($step, $orderedKeys, true)) {
        $step = $firstKey;
    }

    // Position/Navigation
    $pos = array_search($step, $orderedKeys, true);
    $prevKey = $orderedKeys[max(0, $pos - 1)];
    $nextKey = $orderedKeys[min(count($orderedKeys) - 1, $pos + 1)];

    // Progress
    $percent = (int) round((($pos + 1) / count($orderedKeys)) * 100);

    ob_start();
    ?>
    <div class="container my-3">
        <ul class="nav nav-pills nav-justified mb-3">
            <?php foreach ($orderedKeys as $k): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $step === $k ? 'active' : '' ?>" href="<?= htmlspecialchars($qs(['step' => $k])) ?>&setup=1">
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
            
            ## Wenn DB schon erstellt, können wir schonmal die ganze Daten bestimmen
            if (!check_if_db_empty()){
                ## TODO: übergebe parameter, etc, damit Datei automatisch erstellt werden kann
                list($season_teams, $season_dates, $season_matches) = extract_season_details();
            }
            
            global $aktuelle_wett_id;
            $jahr = $_SESSION['year'];
            
            ## Laufende Saions schließen wir aus
            if ($jahr <= max($aktuelle_wett_id)){
                echo '<div class="alert alert-danger" role="alert">
                        <strong>ACHTUNG!</strong> Hier wird gerade eine laufende Saison bearbeitet... Das brechen wir lieber ab!<br>
                        Hier wird sicherheitshalber nichts in den Datenbanken geändert.
                      </div>'; 
                
                $abort_running_season = true;
                
                if (get_usernr() == 2){
                    ## TODO: Hier noch ändern, dass es nicht nach der Usernr geht!
                    $success = grant_admin_rights(2, $jahr);
                    if ($success){
                        echo '<div class="alert alert-success">
                                <strong>Erfolg!</strong> Du hast jetzt Verwaltungsrechte für diese Saison.
                              </div>
                              <br>
                              <div class="alert alert-info">
                                Damit ist die Erstellung der Saison jetzt beendet! Über den Button geht es direkt zur Saison.<br>
                                <a href="/" class="btn btn-primary">Zurück</a>
                              </div>';
                    } else {
                        echo '<div class="alert alert-danger">
                                <strong>Fehler!</strong> Beim erstellen der Rechte ist ein Fehler aufgetreten.
                              </div>';
                    }
                }
                
            } else {
                $abort_running_season = false;                
            }
            
            ## Wenn noch nicht abgebrochen, zeigen wir die einzelnen Seiten an
            if (!$abort_running_season){
            switch ($step) {
                
                case 0:
                    ## Start
                    $title = get_wettbewerb_title(array($jahr, 0));
                    $code = get_wettbewerb_code(array($jahr, 0));
                    $spielzeit = get_wettbewerb_jahr(array($jahr, 0));
                    
                    echo '<div class="alert alert-info" role="alert">';
                    echo "<strong>Willkommen zum automatischen Anlegen der Saison!</strong><br><br>";
                    echo "Wir befinden uns in folgender Saison: ".$code . " " . $spielzeit ."";
                    echo '</div>';
                    
                    break;
                    
                case 1:
                    ## Datenbank laden
                    if (check_if_db_empty()){
                        $msg = import_sql_file(__DIR__ . "/BuLi_Blanko_DB.sql");
                        
                        if (strpos($msg, "Fehler") === 0) {
                            echo '<div class="alert alert-danger" role="alert">' . $msg . '</div>';
                        } else {
                            echo '<div class="alert alert-success" role="alert">' . $msg . '</div>';
                        }
                        
                    } else {
                        ## TODO: Button zum leeren der DB?
                        $msg = "Die Tabellen in der Datenbank wurden schon erstellt!";
                        echo '<div class="alert alert-success" role="alert">' . $msg . '</div>';
                    }
                    
                    break;
                    
                case 2:
                    ## Teams aktualisieren
                    echo '<div class="alert alert-info" role="alert">Falls neue Vereine in der Liga spielen, müssen sie hier hinzugefügt werden. Die aktuelle Liste der Vereine findet sich darunter.</div>';
                    add_team_to_db();
                    print_all_teams();
                    
                    break;
                    
                case 3:
                    ## Datum einlesen
                    list($success, $html) = create_all_spieltag_dates($season_dates);
                    if ($success){
                        echo '<div class="alert alert-success">
                                <strong>Erfolg!</strong> Die Daten der Spieltage wurden richtig gespeichert.
                              </div>';
                    } else {
                        echo '<div class="alert alert-danger">
                                <strong>Fehler!</strong> Es ist ein Problem beim Speichern aufgetreten.
                             </div>';
                    }
                    echo $html;
                    
                    break;
                    
                case 4:
                    ## Spieltage einlesen
                    list($success, $html) = create_all_game_pairings($season_matches);
                    if ($success){
                        echo '<div class="alert alert-success">
                                <strong>Erfolg!</strong> Die Daten der Spieltage wurden richtig gespeichert.
                              </div>';
                    } else {
                        echo '<div class="alert alert-danger">
                                <strong>Fehler!</strong> Es ist ein Problem beim Speichern aufgetreten.
                              </div>';
                    }
                    echo $html;
                    
                    break;
                    
                case 5:
                    ## Tabelle generieren
                    list($success, $html) = create_table($season_teams);
                    if ($success){
                        echo '<div class="alert alert-success">
                                <strong>Erfolg!</strong> Alle Teams wurden richtig in die Tabellen gespeichert.
                              </div>';
                    } else {
                        echo '<div class="alert alert-danger">
                                <strong>Fehler!</strong> Es ist ein Problem beim Speichern aufgetreten.
                              </div>';
                    }
                    echo $html;
                    
                    break;
                    
                case 6:
                    ## Benutzer eintragen
                    $success = add_aktuelle_wett_id($jahr);
                    if ($success){
                        echo '<div class="alert alert-success">
                                <strong>Erfolg!</strong> Die aktuelle Saison wurde zum aktuellem Wettbewerb gesetzt. <br>
                                Die Seite wird gleich neugeladen, damit du dich in den Wettbewerb eintragen kannst!
                              </div>';
                        ## Seite neuladen, damit wir Neu eingeloggt werden!
                        echo "<meta http-equiv=\"refresh\" content=\"5\">";
                    } else {
                        echo '<div class="alert alert-danger">
                                <strong>Fehler!</strong> Es ist ein Problem beim Speichern aufgetreten.
                              </div>';
                    }
                    
                    break;
                    
                default:
                    // Fallback
                    break;
            } ## End Switch
            } ## End If

            ?>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <a class="btn btn-outline-secondary <?= $step === $firstKey ? 'disabled' : '' ?>"
               href="<?= htmlspecialchars($qs(['step' => $prevKey])) ?>&setup=1">Zurück</a>
            <a class="btn btn-primary <?= $step === $lastKey ? 'disabled' : '' ?>"
               href="<?= htmlspecialchars($qs(['step' => $nextKey])) ?>&setup=1">Weiter</a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>
