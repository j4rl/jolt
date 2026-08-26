<?php
declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_NAME = 'jolt';
const DB_USER = 'root';
const DB_PASS = '';
const APP_NAME = 'Jolt';
const MAX_UPLOAD_BYTES = 25 * 1024 * 1024;
// Sätt dessa i produktion, t.ex. https://jolt.example.se och https://jolter.example.se.
const PLAY_URL = '';
const STUDIO_URL = '';

ini_set('session.use_strict_mode', '1');
session_start();

function db(): mysqli {
    static $db;
    if ($db instanceof mysqli) return $db;
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset('utf8mb4');
    return $db;
}

function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }
function redirect(string $url): never { header('Location: ' . $url); exit; }
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(24)); }
function verify_csrf(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) {
        http_response_code(419); exit('Sessionen har gått ut. Ladda om sidan.');
    }
}
function user_id(): ?int { return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null; }
function require_login(): int { if (!user_id()) redirect('auth.php'); return user_id(); }
function json_response(array $data, int $status = 200): never {
    http_response_code($status); header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); exit;
}
function body(): array { return json_decode(file_get_contents('php://input'), true) ?: []; }
function game_code(): string {
    do { $code = (string)random_int(100000, 999999); $s = db()->prepare('SELECT id FROM games WHERE code=?'); $s->bind_param('s',$code); $s->execute(); }
    while ($s->get_result()->num_rows); return $code;
}
function media_url(?string $path): string { return $path ? e($path) : ''; }
function app_url(string $area, string $path = ''): string {
    $base = $area === 'play' ? PLAY_URL : STUDIO_URL;
    return ($base ? rtrim($base, '/') . '/' : '') . ltrim($path, '/');
}
