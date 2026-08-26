# Jolt

Ett live-quiz byggt med PHP, MySQLi och ren JavaScript/CSS. **Jolt** är deltagarnas vy och **Jolter** är studion där quiz skapas och leds.

## Installation i XAMPP

1. Importera `schema.sql` i phpMyAdmin.
2. Kontrollera databasuppgifterna i `config.php` (standard är `root` utan lösenord).
3. Öppna `http://localhost/jolt`.

PHP-tilläggen `mysqli`, `fileinfo` och `mbstring` behöver vara aktiva. Mappen `uploads/` skapas automatiskt vid första uppladdningen.

## Två subdomäner

I produktion pekas både `jolt.dindomän.se` och `jolter.dindomän.se` mot samma public-katalog. Ange adresserna i `PLAY_URL` och `STUDIO_URL` i `config.php`. Jolt används för `index.php`, `play.php` och `game.php`; Jolter används för inloggning, redigering och värdvyn.

Musikslingorna genereras med Web Audio och startar efter värdens första knapptryckning, vilket följer webbläsarnas regler för autoplay. Bildformat: JPG, PNG och WebP. Videoformat: MP4 och WebM; video spelas alltid utan ljud.

Poängen för ett korrekt svar består av 50 % grundpoäng, upp till 35 % tidsbonus och upp till 15 % bonus för svarsplacering i förhållande till antalet deltagare. Fel svar ger noll poäng.
