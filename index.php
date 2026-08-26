<?php require 'partials.php'; page_top('Spela live'); ?>
<section class="hero"><div><span class="eyebrow">LIVE QUIZ</span><h1>Släpp loss<br><em>energin.</em></h1><p>Skapa, dela och tävla i quiz som får hela rummet att vakna.</p></div>
<form class="join-card" action="play.php" method="get"><label for="code">Ange Jolt-kod</label><input id="code" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="000 000" required><button>Gå med <b>→</b></button><small>Ingen inloggning behövs</small></form></section>
<section class="features"><article><b>01</b><h3>Skapa</h3><p>Bygg frågor med text, bild eller ljudlös video.</p></article><article><b>02</b><h3>Starta</h3><p>Dela den sexsiffriga koden med hela gruppen.</p></article><article><b>03</b><h3>Tävla</h3><p>Rätt svar och snabb reaktion ger mest poäng.</p></article></section>
<?php page_bottom(); ?>

