<?php
// ============================================================
//  CHEAPA STUDIO — Site Configuration
//  Mobile-first digital creative agency platform (Kampala, UG)
// ============================================================

// Never print raw PHP warnings/notices to visitors. Errors are
// still written to PHP's error log, just not shown on the page.
error_reporting(E_ALL);
ini_set('display_errors', '0');

date_default_timezone_set('Africa/Kampala');

// ── Environment variables ───────────────────────────────────
// Loads a local .env file (for XAMPP) if present; on Vercel/Supabase the
// platform injects real env vars, so .env is optional and git-ignored.
(function () {
    $envFile = __DIR__ . '/.env';
    if (is_file($envFile)) {
        foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
            [$k, $v] = explode('=', $line, 2);
            $k = trim($k); $v = trim($v, " \t\"'");
            if (getenv($k) === false) { putenv("$k=$v"); $_ENV[$k] = $v; }
        }
    }
})();

/** Read an environment variable with a fallback default. */
function env(string $key, $default = null) {
    $v = getenv($key);
    return ($v === false || $v === '') ? $default : $v;
}

// ── Brand identity ──────────────────────────────────────────
define('SITE_NAME',     'Cheapa Studio');
define('SITE_TAGLINE',  'Professional Branding Made Affordable');
define('SITE_DOMAIN',   'cheapastudio.com');
define('LOCATION',      'Kampala, Uganda');

// ── Contact (replace placeholders with the real numbers) ────
define('PHONE_1',         '+256 753 168599');
define('WHATSAPP_NUMBER', '256753168599');   // no '+', no spaces
define('EMAIL',           'hello@cheapastudio.com');

// ── Currency ────────────────────────────────────────────────
define('CURRENCY', 'UGX');

// ── Auto-detect the URL the visitor actually used ───────────
// Works on localhost, ngrok, and a live domain with no edits.
$__scheme = 'http';
if (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') ||
    (($_SERVER['SERVER_PORT'] ?? '') == 443)
) {
    $__scheme = 'https';
}
$__host = $_SERVER['HTTP_HOST'] ?? 'localhost';
// Folder the app lives in (e.g. /Cheapa_Studio), derived automatically.
$__base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
// If a page is inside /admin, strip that so SITE_URL is the site root.
$__base = preg_replace('#/admin$#', '', $__base);

define('SITE_URL',    $__scheme . '://' . $__host . $__base);
define('SITE_ORIGIN', $__scheme . '://' . $__host);   // scheme + host only
define('UPLOADS_DIR', __DIR__ . '/uploads/');
define('UPLOADS_URL', SITE_URL . '/uploads/');

/** Absolute URL of the current request (for share links + og:url). */
function current_url(): string {
    return SITE_ORIGIN . ($_SERVER['REQUEST_URI'] ?? '/');
}

/** Helper: first existing assets/img file with the given base name. */
function _logo_asset(string $base): ?string {
    foreach (['png', 'jpg', 'jpeg', 'webp', 'svg'] as $ext) {
        $p = __DIR__ . '/assets/img/' . $base . '.' . $ext;
        if (is_file($p)) return SITE_URL . '/assets/img/' . $base . '.' . $ext . '?v=' . @filemtime($p);
    }
    return null;
}

/**
 * Resolve the logo image URL for a surface.
 *  - 'light' surfaces (white navbar/drawer/login) use the dark logo (logo-dark.*)
 *  - 'dark'  surfaces (footer/admin) use the white logo (Settings upload, then logo.*)
 */
function brand_logo_src(string $ctx = 'dark'): ?string {
    if ($ctx === 'light') {
        return _logo_asset('logo-dark');
    }
    $uploaded = function_exists('cfg') ? cfg('logo', '') : '';
    if ($uploaded) {
        if (env('STORAGE_DRIVER', 'local') === 'supabase') {
            return rtrim((string)env('SUPABASE_URL'), '/') . '/storage/v1/object/public/' . env('SUPABASE_BUCKET', 'cheapa') . '/brand/' . $uploaded;
        }
        if (is_file(UPLOADS_DIR . 'brand/' . $uploaded)) {
            return UPLOADS_URL . 'brand/' . $uploaded;
        }
    }
    return _logo_asset('logo');
}

/**
 * Render the Cheapa brand logo, picking the right colour for the surface.
 * Falls back to the wordmark if no logo image is present.
 */
function brand_logo(string $ctx = 'dark'): string {
    $src = brand_logo_src($ctx);
    if ($src) {
        return '<img class="brand-logo" src="' . htmlspecialchars($src, ENT_QUOTES) . '" alt="' . htmlspecialchars(SITE_NAME, ENT_QUOTES) . '">';
    }
    $cls = $ctx === 'light' ? 'brand' : 'brand brand-on-dark';
    return '<span class="' . $cls . '">'
         . '<span class="brand-mark"></span>'
         . '<span class="brand-text"><span class="brand-name">Cheapa</span><span class="brand-slogan">Lets Grow Together</span></span>'
         . '</span>';
}

// ── Helpers ─────────────────────────────────────────────────

/** Format an integer amount as "UGX 100,000". */
function ugx($amount): string {
    return CURRENCY . ' ' . number_format((float)$amount, 0, '.', ',');
}

/** Escape for safe HTML output. */
function e($v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

/**
 * Editable site setting with a constant fallback.
 * $GLOBALS['CFG'] is populated from the `settings` table in includes/db.php.
 */
function cfg(string $key, $default = '') {
    if (isset($GLOBALS['CFG'][$key]) && $GLOBALS['CFG'][$key] !== '') {
        return $GLOBALS['CFG'][$key];
    }
    $fallbacks = [
        'whatsapp_number' => WHATSAPP_NUMBER,
        'phone_1'         => PHONE_1,
        'email'           => EMAIL,
        'location'        => LOCATION,
        'site_tagline'    => SITE_TAGLINE,
    ];
    return $fallbacks[$key] ?? $default;
}

/**
 * Build a WhatsApp click-to-chat link with a pre-filled message.
 */
function wa_link(string $message = ''): string {
    $base = 'https://wa.me/' . preg_replace('/\D/', '', cfg('whatsapp_number'));
    return $message ? $base . '?text=' . rawurlencode($message) : $base;
}
