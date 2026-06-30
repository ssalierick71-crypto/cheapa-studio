<?php
/**
 * Lightweight, privacy-friendly visit tracker.
 * Logs one row per public page view. IPs are hashed (never stored raw),
 * bots/link-preview fetchers are flagged so they don't count as people.
 * Called once from includes/header.php (public pages only).
 */
function track_visit(): void {
    global $pdo;
    if (!isset($pdo)) return;
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') return;

    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $isBot = preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|whatsapp|telegram|embedly|pinterest|preview|monitor|curl|wget|python-requests|go-http|headless/i', $ua) ? 1 : 0;

    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? ($_SERVER['REMOTE_ADDR'] ?? '');
    if (strpos($ip, ',') !== false) $ip = trim(explode(',', $ip)[0]);
    $ipHash = hash('sha256', $ip . '|cheapa-visit-salt-7731');

    $device = preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $ua) ? 'mobile' : 'desktop';

    $ref = '';
    if (!empty($_SERVER['HTTP_REFERER'])) {
        $h = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) ?: '';
        // ignore self-referrals (navigation within the site)
        if ($h && stripos($_SERVER['HTTP_HOST'] ?? '', $h) === false) $ref = $h;
    }

    $path = basename($_SERVER['PHP_SELF'] ?? 'index.php');

    try {
        $pdo->prepare("INSERT INTO visits (day, ip_hash, path, referrer, device, is_bot) VALUES (?,?,?,?,?,?)")
            ->execute([date('Y-m-d'), $ipHash, substr($path, 0, 120), substr($ref, 0, 150), $device, $isBot]);
    } catch (Throwable $e) {
        // visits table not present yet — ignore silently
    }
}
