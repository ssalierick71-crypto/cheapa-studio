<?php
// ============================================================
//  Shared helpers — image uploads, CSRF, image URLs
// ============================================================
if (!defined('SITE_NAME')) require_once dirname(__DIR__) . '/config.php';

/** Which storage backend is active ('local' or 'supabase'). */
function storage_driver(): string {
    return env('STORAGE_DRIVER', 'local') === 'supabase' ? 'supabase' : 'local';
}

/** PUT a file into Supabase Storage. Returns true on success. */
function sb_storage_put(string $path, string $tmpFile, string $mime): bool {
    $url = rtrim((string)env('SUPABASE_URL'), '/') . '/storage/v1/object/' . env('SUPABASE_BUCKET', 'cheapa') . '/' . $path;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS    => file_get_contents($tmpFile),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER    => [
            'Authorization: Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type: ' . $mime,
            'x-upsert: true',
        ],
    ]);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $code >= 200 && $code < 300;
}

/** DELETE a file from Supabase Storage. */
function sb_storage_delete(string $path): void {
    $url = rtrim((string)env('SUPABASE_URL'), '/') . '/storage/v1/object/' . env('SUPABASE_BUCKET', 'cheapa') . '/' . $path;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER    => ['Authorization: Bearer ' . env('SUPABASE_SERVICE_KEY')],
    ]);
    curl_exec($ch);
    curl_close($ch);
}

/** Public URL for an uploaded image (or null). */
function img_url(string $subdir, ?string $file): ?string {
    if (!$file) return null;
    if (storage_driver() === 'supabase') {
        return rtrim((string)env('SUPABASE_URL'), '/') . '/storage/v1/object/public/' . env('SUPABASE_BUCKET', 'cheapa') . '/' . $subdir . '/' . $file;
    }
    return UPLOADS_URL . $subdir . '/' . $file;
}

/**
 * Handle an <input type="file"> upload. Returns the stored filename,
 * or $current (the existing value) if nothing valid was uploaded.
 * Saves to local disk, or Supabase Storage when STORAGE_DRIVER=supabase.
 */
function upload_image(string $field, string $subdir, ?string $current = null): ?string {
    if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return $current;
    }
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) return $current;

    $tmp  = $_FILES[$field]['tmp_name'];
    $info = @getimagesize($tmp);
    if (!$info) return $current;

    $extByMime = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $ext = $extByMime[$info['mime']] ?? null;
    if (!$ext) return $current;

    $name = bin2hex(random_bytes(8)) . '.' . $ext;

    if (storage_driver() === 'supabase') {
        if (!sb_storage_put($subdir . '/' . $name, $tmp, $info['mime'])) return $current;
        if ($current) sb_storage_delete($subdir . '/' . $current);
        return $name;
    }

    $dir = UPLOADS_DIR . $subdir . '/';
    if (!is_dir($dir)) @mkdir($dir, 0775, true);
    if (!move_uploaded_file($tmp, $dir . $name)) return $current;
    if ($current && is_file($dir . $current) && strpos($current, '-') !== false && ctype_xdigit(substr($current, 0, 4))) {
        @unlink($dir . $current);
    }
    return $name;
}

/** Delete an uploaded image by name (local disk or Supabase Storage). */
function delete_image(string $subdir, ?string $file): void {
    if (!$file) return;
    if (storage_driver() === 'supabase') { sb_storage_delete($subdir . '/' . $file); return; }
    $path = UPLOADS_DIR . $subdir . '/' . $file;
    if (is_file($path)) @unlink($path);
}

// ── CSRF ──────────────────────────────────────────────────
function csrf_token(): string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}
function csrf_check(): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return !empty($_POST['csrf']) && hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf']);
}

/** The order/lead workflow statuses, in order. */
function lead_statuses(): array {
    return ['New', 'Contacted', 'Deposit Paid', 'In Progress', 'Review', 'Completed'];
}

/**
 * Reorder a list so any "website" entry comes first (it's the biggest hook).
 * Stable for the rest (PHP 8 sort is stable). $labelFn extracts the text
 * to test from each element (for arrays of rows).
 */
function website_first(array $list, ?callable $labelFn = null): array {
    usort($list, function ($a, $b) use ($labelFn) {
        $la = $labelFn ? (string)$labelFn($a) : (string)$a;
        $lb = $labelFn ? (string)$labelFn($b) : (string)$b;
        $aw = stripos($la, 'website') !== false ? 1 : 0;
        $bw = stripos($lb, 'website') !== false ? 1 : 0;
        return $bw <=> $aw;
    });
    return $list;
}

/**
 * Parse a product's "variants" text ("Single sided=200\nDouble sided=300")
 * into [['label'=>'Single sided','price'=>200], ...].
 */
function product_variants(?string $text): array {
    $out = [];
    foreach (preg_split('/\r?\n/', (string)$text) as $line) {
        $line = trim($line);
        if ($line === '') continue;
        if (strpos($line, '=') !== false) {
            [$label, $price] = explode('=', $line, 2);
            $out[] = ['label' => trim($label), 'price' => (int)preg_replace('/\D/', '', $price)];
        } else {
            $out[] = ['label' => $line, 'price' => 0];
        }
    }
    return $out;
}

/** Unit price for a product given an optional chosen variant label. */
function product_unit_price(array $product, ?string $variantLabel = null): int {
    $variants = product_variants($product['variants'] ?? '');
    if ($variantLabel !== null && $variants) {
        foreach ($variants as $v) {
            if (strcasecmp($v['label'], $variantLabel) === 0 && $v['price'] > 0) return $v['price'];
        }
    }
    if ($variants && $variants[0]['price'] > 0) return $variants[0]['price'];
    return (int)($product['price_ugx'] ?? 0);
}

/** Whether a product is configurable (chosen qty/variant) vs a flat add-to-cart. */
function is_configurable(array $product): bool {
    return ($product['unit_type'] ?? 'fixed') !== 'fixed';
}

/** A friendly order number from an order id, e.g. CS260628-0042. */
function order_number(int $id): string {
    return 'CS' . date('ymd') . '-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
}

/** Inline CSS for a status pill background/colour. */
function status_style(string $status): string {
    return [
        'New'          => 'background:#FEF0C7;color:#B54708',
        'Contacted'    => 'background:#E0E7FF;color:#3538CD',
        'Deposit Paid' => 'background:#D1FADF;color:#027A48',
        'In Progress'  => 'background:#F4EFFF;color:#6D28D9',
        'Review'       => 'background:#FCE7F6;color:#C11574',
        'Completed'    => 'background:#D1FADF;color:#05603A',
    ][$status] ?? 'background:#EEE;color:#444';
}

/** Make a URL-safe slug from a string. */
function slugify(string $s): string {
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim($s, '-') ?: 'item';
}

/**
 * Map a pack feature line (e.g. "200 Business cards") to a visual item:
 * an image from /uploads/items, an icon, and a short blurb. Used on the
 * pack detail page to show "what's inside" like an e-commerce listing.
 */
function pack_item_meta(string $feature): array {
    $f = strtolower($feature);
    // order matters — check more specific keywords first
    $map = [
        ['google',                'google-business',   'bi-geo-alt',        'Google Business Profile set up so customers find you on Maps and Search.'],
        ['website',               'website',           'bi-window',         'A clean, mobile-friendly website that works on every phone.'],
        ['web ',                  'website',           'bi-window',         'A clean, mobile-friendly website that works on every phone.'],
        ['business card',         'business-cards',    'bi-person-vcard',   'Professionally designed, print-ready business cards.'],
        ['card',                  'business-cards',    'bi-person-vcard',   'Professionally designed, print-ready business cards.'],
        ['flyer',                 'flyers',            'bi-file-richtext',  'Eye-catching flyers designed to bring in customers.'],
        ['poster',                'poster',            'bi-image',          'High-impact poster artwork for promotions.'],
        ['receipt',               'receipt-book',      'bi-receipt',        'A branded receipt book for clean, trusted transactions.'],
        ['banner',                'banner',            'bi-flag',           'A bold banner for your shopfront or events.'],
        ['letterhead',            'letterhead',        'bi-file-text',      'Branded letterhead for official documents and quotes.'],
        ['company profile',       'company-profile',   'bi-building',       'A polished company profile that builds trust with clients.'],
        ['profile',               'company-profile',   'bi-building',       'A polished company profile that builds trust with clients.'],
        ['social',                'social-media',      'bi-instagram',      'Ready-to-post social media designs for a consistent feed.'],
        ['whatsapp',              'whatsapp-branding', 'bi-whatsapp',       'WhatsApp profile, catalog and status branding.'],
        ['logo',                  'logo',              'bi-vector-pen',     'A memorable logo delivered in every format you need.'],
    ];
    foreach ($map as [$kw, $img, $icon, $blurb]) {
        if (strpos($f, $kw) !== false) {
            return ['image' => $img . '.jpg', 'icon' => $icon, 'blurb' => $blurb, 'label' => trim($feature)];
        }
    }
    return ['image' => 'brand-system.jpg', 'icon' => 'bi-stars', 'blurb' => 'Part of your complete brand package.', 'label' => trim($feature)];
}
