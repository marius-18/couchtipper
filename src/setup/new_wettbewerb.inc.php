<?php

function edit_db_entries_update($saison_id){
    ## Übernimmt die Werte aus dem Formular und updatet dann den Wettbewerb in der DB
    $messages = '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $runde          = $_POST['runde']          ?? null;
        $wett_name      = $_POST['wett_name']      ?? null;
        $wett_code      = $_POST['wett_code']      ?? null;
        $wett_jahr      = $_POST['wett_jahr']      ?? null;
        $wett_title     = $_POST['wett_title']     ?? null;
        $wett_einsatz   = $_POST['wett_einsatz']   ?? null;
        $wett_anteil    = $_POST['wett_anteil']    ?? null;
        $wett_daempfung = $_POST['wett_daempfung'] ?? null;
        $wett_praemie   = $_POST['wett_praemie']   ?? null;
        $wett_shortcut  = $_POST['wett_shortcut']  ?? null;

        if (
            $saison_id      !== null &&
            $runde          !== null &&
            $wett_name      !== null &&
            $wett_code      !== null &&
            $wett_jahr      !== null &&
            $wett_title     !== null &&
            $wett_einsatz   !== null &&
            $wett_anteil    !== null &&
            $wett_daempfung !== null &&
            $wett_praemie   !== null &&
            $wett_shortcut  !== null
        ) {
            $success = update_wettbewerb_db(
                $saison_id,
                $runde,
                $wett_name,
                $wett_code,
                $wett_jahr,
                $wett_title,
                $wett_einsatz,
                $wett_anteil,
                $wett_daempfung,
                $wett_praemie,
                $wett_shortcut
            );
            
            if ($success) {
                $messages .= "<div class='alert alert-success'>Eintrag erfolgreich gespeichert.</div>";
            } else {
                $messages .= "<div class='alert alert-danger'>Fehler beim Speichern des Eintrags.</div>";
            }
            
        } else {
            $messages .= "<div class='alert alert-danger'>Bitte alle Felder ausfüllen.</div>";
        }
    }
    return $messages;
}


function create_db_entries_update($saison_id) {
    ## Übernimmt die Werte aus dem Formular und erstellt dann den Wettbewerb in der DB
    $messages = '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $post_db_name        = $_POST['db_name']        ?? '';
        $post_db_user        = $_POST['db_user']        ?? '';
        $post_db_password    = $_POST['db_password']    ?? '';
        $post_wettbewerbsart = $_POST['wettbewerbsart'] ?? '';
        
        if ($post_db_name && $post_db_user && $post_db_password && $post_wettbewerbsart !== '') {
            $success = false;
            if ($post_wettbewerbsart == 1) {
                $success = create_wettbewerb_in_db($post_db_name, $post_db_user, $post_db_password, $saison_id, 0);
            } else {
                $success_part1 = create_wettbewerb_in_db($post_db_name, $post_db_user, $post_db_password, $saison_id, 0);
                $success_part2 = create_wettbewerb_in_db($post_db_name, $post_db_user, $post_db_password, $saison_id, 1);
                $success = $success_part1 && $success_part2;
            }
            
            if ($success) {
                $messages .= "<div class='alert alert-success'>Eintrag erfolgreich gespeichert.</div>";
            } else {
                $messages .= "<div class='alert alert-danger'>Fehler beim Speichern des Eintrags.</div>";
            }
        } else {
            $messages .= "<div class='alert alert-danger'>Bitte alle Felder ausfüllen.</div>";
        }
    }
    return $messages;
}


function copy_db_entries_update($saison_id){
    ## Kopiert eine Saison in die nächste
    $message = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $copy_id = $_POST['copy_saison_id'] ?? null;
        
        if ($copy_id !== null) {
            if (wettbewerb_has_parts($copy_id) != wettbewerb_has_parts($saison_id)){
                $message .= "<div class='alert alert-warning'>Unterschiedlicher typ.</div>";
            } else {
                $message .= "<div class='alert alert-info'>Kopiere Saison ".htmlspecialchars($copy_id)." in Saison ".htmlspecialchars($saison_id)." </div>";
                update_wettbewerb_by_copy($copy_id, 0, $saison_id, 0);
                update_wettbewerb_by_copy($copy_id, 1, $saison_id, 1);
            }
        } else {
            $message .= "<div class='alert alert-warning'>Keine ID zum Kopieren übergeben.</div>";
        }
    }
    
    return $message;
}


function create_db_entries_form_html($saison_id) {
    if (isset($_GET['index'])){
        $index_url = "index=" . htmlspecialchars($_GET['index']) . "&";
    } else {
        $index_url = "";
    }
    
    $nextId = get_max_wett_id() + 1;
    
    $db_name = get_wettbewerb_db_name(array($saison_id,0));
    $db_user = get_wettbewerb_db_user(array($saison_id,0));   
    if (wettbewerb_has_parts($saison_id) || ($saison_id == $nextId) ){
        $einteilig = "";
        $zweiteilig = "checked";
    
    } else {
        $einteilig = "checked";
        $zweiteilig = "";
    }
    
    
    $html = '
    <div class="container bg-secondary p-2 rounded">
    <h3>Bearbeiten der Datenbankeinträge</h3>
    <form method="POST" action="?' . $index_url . 'saison_id=' . urlencode($saison_id) . '">
        
        <div class="row mb-3">
            <label for="db_name" class="col-sm-2 col-form-label">DB Name:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="db_name" name="db_name" required value="' . htmlspecialchars($db_name) . '">
            </div>
        </div>
        
        <div class="row mb-3">
            <label for="db_user" class="col-sm-2 col-form-label">DB User:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="db_user" name="db_user" required value="' . htmlspecialchars($db_user) . '">
            </div>
        </div>
        
        <div class="row mb-3">
            <label for="db_password" class="col-sm-2 col-form-label">DB Passwort:</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" id="db_password" name="db_password" required>
            </div>
        </div>
        
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label text-end">Wettbewerbsart:</label>
            <div class="col-sm-10 d-flex justify-content-center">
                <div class="form-check mx-4">
                    <input class="form-check-input" type="radio" name="wettbewerbsart" id="einteilig" value="1" ' . $einteilig . '>
                    <label class="form-check-label" for="einteilig">1-teilig</label>
                </div>
                <div class="form-check mx-4">
                    <input class="form-check-input" type="radio" name="wettbewerbsart" id="zweiteilig" value="2" ' . $zweiteilig . '>
                    <label class="form-check-label" for="zweiteilig">2-teilig</label>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary">Enter</button>
            </div>
        </div>
        
    </form>
    </div>';
    
    return $html;
}


function copy_db_entries_form_html($saison_id) {
    
    if (isset($_GET['index'])){
        $index_url = "index=" . htmlspecialchars($_GET['index']) . "&";
    } else {
        $index_url = "";
    }
    
    $array = get_all_wettbewerbe();

    $html = '
    <div class="container bg-secondary p-2 rounded">
    <form method="POST" id="copyForm" action="?'.$index_url.'saison_id=' . urlencode($saison_id) . '&step='. htmlspecialchars($_GET['step']) .'">
        <h3>Wettbewerb zum Kopieren auswählen</h3>
        
        
        <div class="row mb-3">
            <label for="copy_select" class="col-sm-2 col-form-label">Kopieren von:</label>
            <div class="col-sm-10">';
    
            $html .= saison_select_form_html($saison_id, "copy_saison_id");
            $html .= '

            </div>
        </div>
        
        
        <div class="row">
            <div class="col-sm-12 text-center">
                <button type="submit" class="btn btn-primary">Kopieren</button>
            </div>
        </div>
    </form>
    </div>';
    
    return $html;
}


function edit_db_entries_form_html($saison_id, $runde) {
    
    $wett_code = get_wettbewerb_code(array($saison_id,$runde));   
    $wett_shortcut = get_openliga_shortcut(array($saison_id,$runde));   
    $wett_name = get_wettbewerb_name(array($saison_id,$runde));   
    $wett_jahr = get_wettbewerb_jahr(array($saison_id,$runde));   
    $wett_einsatz = get_wettbewerb_einsatz(array($saison_id,$runde));   
    $wett_gewinner = get_wettbewerb_gewinner(array($saison_id,$runde));   
    $wett_daempfung = get_wettbewerb_daempfung(array($saison_id,$runde));   
    $wett_praemie = get_wettbewerb_praemie(array($saison_id,$runde));   
    $wett_title = get_wettbewerb_title(array($saison_id,$runde));  
    
    
    $html = '<div class="container bg-secondary p-2 rounded">
    <h3> Anlegen des Wettbewerbs</h3>';

    $runde_form = '
    <form method="POST" id="rundeForm">
    <div class="row mb-3">
        <label class="col-sm-2 col-form-label text-end">Runde:</label>
        <div class="col-sm-10 d-flex justify-content-center">
            <div class="form-check mx-4">
                <input class="form-check-input" type="radio" name="runde" id="hinrunde" value="0" ' . ($runde === '0' ? 'checked' : '') . ' onchange="document.getElementById(\'rundeForm\').submit();">
                <label class="form-check-label" for="hinrunde">Hinrunde</label>
            </div>
            
            <div class="form-check mx-4">
                <input class="form-check-input" type="radio" name="runde" id="rueckrunde" value="1" ' . ($runde === '1' ? 'checked' : '') . ' onchange="document.getElementById(\'rundeForm\').submit();">
                <label class="form-check-label" for="rueckrunde">Rückrunde</label>
            </div>
            
        </div>
    </div>
    </form>';


    if (wettbewerb_has_parts($saison_id)){
        $html .= $runde_form;
    }


    $html .= '
    <form method="POST">
        <input type="hidden" class="form-control" id="runde" name="runde" required value="' . htmlspecialchars($runde ?? '') . '">
        <div class="row mb-3">
            <label for="name" class="col-sm-2 col-form-label">Name:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="name" name="wett_name" required value="' . htmlspecialchars($wett_name ?? '') . '">
            </div>
        </div>
        
        <div class="row mb-3">
            <label for="code" class="col-sm-2 col-form-label">Code:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="code" name="wett_code" required value="' . htmlspecialchars($wett_code ?? '') . '">
            </div>
        </div>
        
        <div class="row mb-3">
            <label for="jahr" class="col-sm-2 col-form-label">Jahr:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="jahr" name="wett_jahr" required value="' . htmlspecialchars($wett_jahr ?? '') . '">
            </div>
        </div>
        
        <div class="row mb-3">
            <label for="title" class="col-sm-2 col-form-label">Titel:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="title" name="wett_title" required value="' . htmlspecialchars($wett_title ?? '') . '">
            </div>
        </div>
        
        <div class="row mb-3">
            <label for="einsatz" class="col-sm-2 col-form-label">Einsatz:</label>
            <div class="col-sm-10">
                <input type="number" step="0.01" class="form-control" id="einsatz" name="wett_einsatz" required value="' . htmlspecialchars($wett_einsatz ?? '') . '">
            </div>
        </div>
        
        <div class="row mb-3">
            <label for="anteil" class="col-sm-2 col-form-label">Anteil Gewinner:</label>
            <div class="col-sm-10">
                <input type="number" step="0.01" class="form-control" id="anteil" name="wett_anteil" required value="' . htmlspecialchars($wett_gewinner ?? '') . '">
            </div>
        </div>
        
        <div class="row mb-3">
            <label for="daempfung" class="col-sm-2 col-form-label">Dämpfung:</label>
            <div class="col-sm-10">
                <input type="number" step="0.01" class="form-control" id="daempfung" name="wett_daempfung" required value="' . htmlspecialchars($wett_daempfung ?? '') . '">
            </div>
        </div>
        
        <div class="row mb-3">
            <label for="praemie" class="col-sm-2 col-form-label">Spieltagprämie:</label>
            <div class="col-sm-10">
                <input type="number" step="0.01" class="form-control" id="praemie" name="wett_praemie" required value="' . htmlspecialchars($wett_praemie ?? '') . '">
            </div>
        </div>
        
        <div class="row mb-3">
            <label for="shortcut" class="col-sm-2 col-form-label">OpenLiga Shortcut:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="shortcut" name="wett_shortcut" required value="' . htmlspecialchars($wett_shortcut ?? '') . '">
            </div>
        </div>
        
        <div class="row">
            <div class="col-sm-12 text-center">
                <button type="submit" class="btn btn-primary">Enter</button>
            </div>
        </div>
    </form>
    </div>';
    
    return $html;
}


function get_saison_select_form_html($saison_id) {
    $html = '<form method="GET" id="saisonForm">';
    
    $html .= '<div class="row mb-3">';
    $html .= '<div class="col-sm-12"> ';           
    $html .= '<input type="hidden" name="index" value="' . htmlspecialchars($_GET['index'] ?? '') . '">';
    
    $html .= saison_select_form_html($saison_id, "saison_id");
    
    $html .= "</div></div>";
    $html .= '</form>';
    
    return $html;
}


function saison_select_form_html($saison_id, $type){
    $array = get_all_wettbewerbe();
    
    if ($type == "saison_id"){
        $nextId = get_max_wett_id() + 1;
        $newLabel = "Neuen Wettbewerb anlegen";
        $selected = ($saison_id == $nextId) ? 'selected' : '';
        $start_option = '<option value="' . htmlspecialchars($nextId) . '" ' . $selected . '>' . htmlspecialchars($newLabel) . '</option>';
        
        $onchange = 'onchange="this.form.submit()"';
    } else {
        $onchange = "";
        $start_option = "";
    }
    
    
    $html = '<select class="form-select" id="'.$type.'" name="'.$type.'" '.$onchange.'>';
    $html .= $start_option;
    
    
    foreach (array_reverse($array[0], true) as $id => $name) {
        $selected = ($saison_id == $id) ? 'selected' : '';
        $text = $name . " " . ($array[1][$id] ?? '') . " (id=" . $id . ")";
        $html .= '<option value="' . htmlspecialchars($id) . "\" " . $selected . '>' . htmlspecialchars($text) . '</option>';
    }
    
    $html .= '</select>';
    
    return $html;
}






?>
