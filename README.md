# Jolt

Jolt är en plattform för interaktiva livefrågor, byggd med PHP, MySQLi och ren JavaScript/CSS. **Jolt** är deltagarnas vy och **Jolter** är studion där Jolt görs, förhandsvisas och leds.

## Funktioner

- Gör Jolt med frågor som har ett eller flera rätta svar samt sant/falskt.
- Ställ in tid och maxpoäng per fråga.
- Ladda upp bilder och ljudlösa videor genom att välja eller släppa en fil i formuläret.
- Förhandsvisa hela Jolten direkt i studion och växla mellan deltagarvy och facit.
- Starta en liveomgång och dela en tydlig deltagarlänk tillsammans med en sexsiffrig kod.
- Låt deltagarna välja namn och avatar.
- Visa topp fem efter varje fråga med placeringsförändringar, exempelvis ny etta, ny tvåa eller tappad förstaplats.
- Avsluta med en animerad prispall och fullständig slutställning.
- Välj mellan ljust, mörkt och systemstyrt tema.
- Spela valfri musikslinga medan deltagarna Joltar.

## Installation i XAMPP

1. Placera projektet i XAMPP:s `htdocs`-katalog.
2. Importera `schema.sql` i phpMyAdmin.
3. Kontrollera databasuppgifterna i `config.php`. Standardinställningen är användaren `root` utan lösenord.
4. Starta Apache och MySQL.
5. Öppna `http://localhost/jolt`.

PHP-tilläggen `mysqli`, `fileinfo` och `mbstring` behöver vara aktiva. Katalogen `uploads/` skapas automatiskt vid den första uppladdningen och måste vara skrivbar för webbservern.

## Konfiguration

Databas, uppladdningsgräns och publika adresser ställs in i `config.php`:

```php
const DB_HOST = '127.0.0.1';
const DB_NAME = 'jolt';
const DB_USER = 'root';
const DB_PASS = '';

const MAX_UPLOAD_BYTES = 25 * 1024 * 1024;
const PLAY_URL = '';
const STUDIO_URL = '';
```

Tomma URL-värden fungerar för lokal utveckling. I produktion bör de innehålla de fullständiga HTTPS-adresserna till deltagarvyn respektive studion.

## Två subdomäner

I produktion kan exempelvis `jolt.dindomän.se` och `jolter.dindomän.se` peka mot samma publika katalog:

```php
const PLAY_URL = 'https://jolt.dindomän.se';
const STUDIO_URL = 'https://jolter.dindomän.se';
```

Deltagarvyn använder `index.php`, `play.php` och `game.php`. Inloggning, redigering och värdvyn ligger i Jolter.

## Media och tema

Följande uppladdningsformat stöds:

- Bilder: JPG, PNG och WebP.
- Video: MP4 och WebM.
- Maximal filstorlek: 25 MB.

Video spelas automatiskt, loopas och är alltid ljudlös. Filtyp och storlek kontrolleras även på serversidan.

Temaomkopplaren laddas från `https://ld.j4rl.se/ld-theme-toggle.js`. Valet sparas i webbläsarens `localStorage`; systemets tema används som standard.

## Poängberäkning

Poängen för ett korrekt svar består av:

- 50 % grundpoäng.
- Upp till 35 % tidsbonus.
- Upp till 15 % bonus för svarsplacering i förhållande till antalet deltagare.

Fel svar ger noll poäng. Efter varje fråga jämförs ställningen före och efter frågan för att visa deltagarnas placeringsförändringar.

## Ljud

Musikslingorna genereras med Web Audio och startar efter värdens första knapptryckning, i enlighet med webbläsarnas regler för autoplay. Värden kan välja Pulse, Arcade, Focus eller ingen musik.
