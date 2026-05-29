<?php
declare(strict_types=1);

/**
 * MySQL DB Dump Download via PDO $g_pdo
 * - Exclude tables + columns
 * - MySQL: best-effort read-only (START TRANSACTION READ ONLY ...)
 * - Disable multi statements if possible
 */

if (!isset($g_pdo) || !($g_pdo instanceof PDO)) {
    http_response_code(500);
    echo "PDO \$g_pdo nicht verfügbar.";
    exit;
}

/* ===== Schutz (optional aber empfohlen) ===== */
$EXPECTED_TOKEN = 'CHANGE_ME_TO_A_LONG_RANDOM_SECRET';
$providedToken  = $providedToken ?? ($_GET['token'] ?? '');

if (!hash_equals($EXPECTED_TOKEN, (string)$providedToken)) {
    http_response_code(403);
    echo "Forbidden";
    exit;
}

set_time_limit(0);
ini_set('memory_limit', '-1');
while (ob_get_level() > 0) { ob_end_clean(); }

$g_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$g_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

/**
 * MySQL-spezifische Absicherung:
 * - Keine Multi-Statements (wenn Treiber es unterstützt)
 */
try {
    $g_pdo->setAttribute(PDO::MYSQL_ATTR_MULTI_STATEMENTS, false);
} catch (Throwable $e) {
    // falls nicht verfügbar: ignorieren
}

/* =========================
   EXCLUDE KONFIGURATION
   ========================= */

// Ganze Tabellen ausschließen
$EXCLUDE_TABLES = [
    'Precompute_Tipps', 
    'User', 
    'user', 
    'Security',
    'Saison'
];

// Spalten ausschließen (global)
$EXCLUDE_COLUMNS_GLOBAL = [
    'debug_time',
    'debug_ip',
    'debug_user'
];

// Spalten ausschließen (pro Tabelle)
$EXCLUDE_COLUMNS_BY_TABLE = [
    // 'users' => ['password_hash', 'reset_token'],
];

$write = function (string $s): void {
    echo $s;
    flush();
};

$quoteValue = function ($val) use ($g_pdo): string {
    if ($val === null) return "NULL";
    return $g_pdo->quote((string)$val);
};

$dbName = (string)$g_pdo->query("SELECT DATABASE()")->fetchColumn();
#$dbName = $_GET['db_name'];

if ($dbName === '') {
    http_response_code(500);
    echo "Keine Datenbank ausgewählt (SELECT DATABASE() leer).";
    exit;
}

$ts = gmdate('Y-m-d_His');
#$filename = preg_replace('~[^a-zA-Z0-9_\-\.]+~', '_', $dbName) . "_dump_{$ts}.sql";


header('Content-Type: application/sql; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

/* =========================
   MySQL: Read-only Snapshot
   =========================
   WICHTIG: Wir verwenden absichtlich KEIN beginTransaction(),
   sondern MySQLs START TRANSACTION ... READ ONLY.
*/
$txStarted = false;
try {
    // REPEATABLE READ + konsistenter Snapshot für InnoDB
    $g_pdo->exec("SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ");
    // MySQL Syntax: START TRANSACTION [READ ONLY] [WITH CONSISTENT SNAPSHOT]
    $g_pdo->exec("START TRANSACTION READ ONLY WITH CONSISTENT SNAPSHOT");
    $txStarted = true;
} catch (Throwable $e) {
    // Wenn es fehlschlägt: export geht weiter (nur ohne Snapshot/readonly),
    // aber das Skript führt weiterhin nur read-Queries aus.
}

/* =========================
   Dump Header
   ========================= */
$write("-- ------------------------------------------------------\n");
$write("-- MySQL Database Dump\n");
$write("-- Database: `{$my_dbName}`\n");
$write("-- Generated: " . gmdate('c') . " (UTC)\n");
$write("-- ------------------------------------------------------\n\n");

// Nur Einstellungen im Dump-Output (werden NICHT auf der DB ausgeführt)
$write("SET NAMES utf8mb4;\n");
$write("SET time_zone = '+00:00';\n");
$write("SET foreign_key_checks = 0;\n");
$write("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';\n\n");

/* =========================
   Tabellenliste holen
   ========================= */
$tablesStmt = $g_pdo->prepare("
    SELECT TABLE_NAME
    FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = :db
      AND TABLE_TYPE = 'BASE TABLE'
    ORDER BY TABLE_NAME
");
$tablesStmt->execute([':db' => $dbName]);
$tables = $tablesStmt->fetchAll();

foreach ($tables as $row) {
    $table = (string)$row['TABLE_NAME'];
    if (in_array($table, $EXCLUDE_TABLES, true)) {
        continue;
    }

    $safeTable = str_replace('`', '``', $table);

    $write("-- ------------------------------------------------------\n");
    $write("-- Table structure for table `{$safeTable}`\n");
    $write("-- ------------------------------------------------------\n\n");

    // Diese DROP/CREATE stehen NUR im SQL-Dump (nicht in der DB ausgeführt)
    $write("DROP TABLE IF EXISTS `{$safeTable}`;\n");

    // SHOW CREATE TABLE ist read-only
    $create = $g_pdo->query("SHOW CREATE TABLE `{$safeTable}`")->fetch();
    $createSql = $create['Create Table'] ?? null;
    if (!$createSql) {
        $write("-- Konnte CREATE Statement nicht ermitteln.\n\n");
        continue;
    }
    $write($createSql . ";\n\n");

    $write("-- Dumping data for table `{$safeTable}`\n\n");

    // Alle Spalten holen
    $colsStmt = $g_pdo->prepare("
        SELECT COLUMN_NAME
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :t
        ORDER BY ORDINAL_POSITION
    ");
    $colsStmt->execute([':db' => $dbName, ':t' => $table]);
    $allCols = array_map(static fn($r) => (string)$r['COLUMN_NAME'], $colsStmt->fetchAll());

    if (!$allCols) {
        $write("-- (Keine Spalten gefunden)\n\n");
        continue;
    }

    $excludedForTable = $EXCLUDE_COLUMNS_BY_TABLE[$table] ?? [];
    $excluded = array_unique(array_merge($EXCLUDE_COLUMNS_GLOBAL, $excludedForTable));

    // Gefilterte Spalten
    $cols = array_values(array_filter($allCols, static function (string $c) use ($excluded) {
        return !in_array($c, $excluded, true);
    }));

    if (!$cols) {
        $write("-- (Alle Spalten ausgeschlossen -> keine Daten exportiert)\n\n");
        continue;
    }

    // Nur diese Spalten selektieren (damit excluded nicht mal gelesen werden)
    $selectCols = implode(", ", array_map(static function (string $c) {
        $c = str_replace('`', '``', $c);
        return "`{$c}`";
    }, $cols));

    $colList = $selectCols;

    // Unbuffered Query (MySQL PDO), damit große Tabellen nicht alles in RAM laden
    try {
        $g_pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
    } catch (Throwable $e) {}

    // Daten lesen (read-only)
    $dataStmt = $g_pdo->query("SELECT {$selectCols} FROM `{$safeTable}`", PDO::FETCH_ASSOC);

    $batchSize = 200;
    $rowsInBatch = 0;
    $valuesSqlParts = [];

    while ($r = $dataStmt->fetch(PDO::FETCH_ASSOC)) {
        $vals = [];
        foreach ($cols as $c) {
            $vals[] = $quoteValue($r[$c] ?? null);
        }
        $valuesSqlParts[] = "(" . implode(", ", $vals) . ")";
        $rowsInBatch++;

        if ($rowsInBatch >= $batchSize) {
            $write("INSERT INTO `{$safeTable}` ({$colList}) VALUES\n" . implode(",\n", $valuesSqlParts) . ";\n");
            $valuesSqlParts = [];
            $rowsInBatch = 0;
        }
    }

    if ($rowsInBatch > 0) {
        $write("INSERT INTO `{$safeTable}` ({$colList}) VALUES\n" . implode(",\n", $valuesSqlParts) . ";\n");
    }

    $write("\n");
}

$write("SET foreign_key_checks = 1;\n");

/* Transaktion sicher beenden, ohne irgendwas zu committen */
try {
    if ($txStarted) {
        $g_pdo->exec("ROLLBACK");
    }
} catch (Throwable $e) {
    // ignore
}

exit;
