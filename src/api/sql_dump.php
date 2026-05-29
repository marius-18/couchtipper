<?php
declare(strict_types=1);

if (get_usernr() == ""){
echo "Zugang nur f&uuml;r angemeldete user!";
exit;
}

// Hier ggf. deine Includes/Bootstrap für DB/Funktionen
// include __DIR__ . '/init.php';
// include __DIR__ . '/functions.php';

$all = get_all_wettbewerbe(); // [0] => namen, [1] => saisonen
$names   = $all[0] ?? [];
$seasons = $all[1] ?? [];

/**
 * Wenn Export geklickt wurde:
 * - index entgegennehmen
 * - und dann dein Export-Skript includen
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export_idx'])) {
    $idx = filter_var($_POST['export_idx'], FILTER_VALIDATE_INT);
    if ($idx === false) {
        http_response_code(400);
        echo "Ungültiger Index.";
        exit;
    }

    // Optional: prüfen ob Index existiert
    if (!array_key_exists($idx, $names) || !array_key_exists($idx, $seasons)) {
        http_response_code(404);
        echo "Index nicht gefunden.";
        exit;
    }

    // Serverseitig weitergeben (z.B. an dein Dump/Export-Skript)
    // Beispiel: $exportWettbewerbIndex wird vom Export-Skript gelesen
    $exportWettbewerbIndex = $idx;

    // Optional: Token serverseitig (wenn du den Mechanismus nutzt)
    // $providedToken = 'CHANGE_ME_TO_A_LONG_RANDOM_SECRET';

    // Export-Skript ausführen
    // include __DIR__ . '/dump.php';
    // ODER: include __DIR__ . '/export_wettbewerb.php';
    // Wichtig: Das Export-Skript soll dann $exportWettbewerbIndex verwenden.
    new_db_connection([$idx,0]);
    $providedToken = 'CHANGE_ME_TO_A_LONG_RANDOM_SECRET';
    $my_dbName = get_wettbewerb_code([$idx,0]) . get_wettbewerb_jahr([$idx,0]);    
    $filename = $my_dbName . ".sql";
    include_once("sql_dump_download.php");
    exit;
}

// Für Anzeige: gemeinsame Keys bestimmen (Schnittmenge) und sortieren
$keys = array_values(array_unique(array_merge(array_keys($names), array_keys($seasons))));
sort($keys, SORT_NUMERIC);

// Optional: nur Einträge anzeigen, die in beiden Arrays existieren
$keys = array_values(array_filter($keys, function ($k) use ($names, $seasons) {
    return array_key_exists($k, $names) && array_key_exists($k, $seasons);
}));

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Export Datenbank Dump</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h3 class="mb-4">Export Datenbank Dump</h3>

    <div class="row g-3">
        <?php foreach ($keys as $k): ?>
            <?php
                $w = (string)$names[$k];
                $s = (string)$seasons[$k];
                $title = $w . ' ' . $s; // z.B. "BuLi 2025/26"
            ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-3">
                            <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
                        </h5>

                        <div class="mt-auto">
                            <form method="post">
                                <input type="hidden" name="export_idx" value="<?= htmlspecialchars((string)$k, ENT_QUOTES, 'UTF-8') ?>">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        export database dump
                                    </button>
                                </div>
                            </form>

                        </div>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>