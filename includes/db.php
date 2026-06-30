<?php
require_once dirname(__DIR__) . '/config.php';

$__driver = env('DB_DRIVER', 'mysql');
$__host   = env('DB_HOST', '127.0.0.1');
$__port   = env('DB_PORT', $__driver === 'pgsql' ? '5432' : '3306');
$__name   = env('DB_NAME', 'cheapa_db');
$__user   = env('DB_USER', 'root');
$__pass   = env('DB_PASS', '');

if ($__driver === 'pgsql') {
    $ssl = env('DB_SSLMODE', 'require');
    $dsn = "pgsql:host={$__host};port={$__port};dbname={$__name};sslmode={$ssl}";
    // pgbouncer transaction pooling (Supabase serverless) needs emulated prepares
    $opts = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => true,
    ];
} else {
    $dsn = "mysql:host={$__host};port={$__port};dbname={$__name};charset=utf8mb4";
    $opts = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
}

try {
    $pdo = new PDO($dsn, $__user, $__pass, $opts);
} catch (PDOException $e) {
    http_response_code(500);
    die('<h2 style="font-family:sans-serif;color:#c00;padding:2rem">Database connection failed. Check your database settings (XAMPP MySQL running, or the Supabase env vars).</h2>');
}

/** The active PDO driver name ('mysql' or 'pgsql'). */
function db_driver(): string { global $pdo; return $pdo->getAttribute(PDO::ATTR_DRIVER_NAME); }

/** SQL expression for "today's date", portable across MySQL and Postgres. */
function sql_today(): string {
    return db_driver() === 'pgsql' ? 'CURRENT_DATE' : 'CURDATE()';
}

/** SQL expression for "the date N days ago", portable across MySQL and Postgres. */
function sql_days_ago(int $n): string {
    $n = max(0, $n);
    return db_driver() === 'pgsql'
        ? "(CURRENT_DATE - INTERVAL '{$n} days')"
        : "(CURDATE() - INTERVAL {$n} DAY)";
}

/** Last inserted id (Postgres needs the sequence name). */
function last_id(PDO $pdo, string $table = '', string $col = 'id') {
    if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql' && $table) {
        return (int)$pdo->lastInsertId("{$table}_{$col}_seq");
    }
    return (int)$pdo->lastInsertId();
}

/** Portable upsert of a single key/value into the settings table. */
function setting_upsert(PDO $pdo, string $key, string $val): void {
    if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql') {
        $pdo->prepare("INSERT INTO settings (skey, sval) VALUES (?, ?) ON CONFLICT (skey) DO UPDATE SET sval = EXCLUDED.sval")->execute([$key, $val]);
    } else {
        $pdo->prepare("INSERT INTO settings (skey, sval) VALUES (?, ?) ON DUPLICATE KEY UPDATE sval = VALUES(sval)")->execute([$key, $val]);
    }
}

// Load editable settings so cfg() can override the config.php constants.
$GLOBALS['CFG'] = $GLOBALS['CFG'] ?? [];
try {
    foreach ($pdo->query("SELECT skey, sval FROM settings") as $row) {
        $GLOBALS['CFG'][$row['skey']] = $row['sval'];
    }
} catch (Throwable $e) {
    // settings table not present yet — constants remain the source of truth.
}

// Serverless (Vercel) sessions must live in the DB, not the ephemeral disk.
if (env('SESSION_DRIVER', 'file') === 'db') {
    require_once __DIR__ . '/session.php';
    cheapa_db_sessions($pdo);
}
