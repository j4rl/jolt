<?php
require_once __DIR__ . '/config.php';
function page_top(string $title): void { ?>
<!doctype html><html lang="sv"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($title)?> · Jolt</title><link rel="stylesheet" href="assets/style.css"><link rel="stylesheet" href="assets/theme.css"><link rel="stylesheet" href="assets/walking.css"><script src="https://ld.j4rl.se/ld-theme-toggle.js" defer></script></head><body>
<nav><a class="logo" href="index.php">JOLT<span>⚡</span></a><div><?php if(user_id()): ?><a href="dashboard.php">Mina Jolt</a><a href="auth.php?logout=1">Logga ut</a><?php else: ?><a href="auth.php">Logga in</a><?php endif ?><ld-theme-toggle></ld-theme-toggle></div></nav><main>
<?php }
function page_bottom(): void { ?></main><footer>Jolt — snabb kunskap, hög energi.</footer></body></html><?php }
