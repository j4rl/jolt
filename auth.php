<?php
require 'partials.php';
if(isset($_GET['logout'])) { session_destroy(); redirect('index.php'); }
$error='';
if($_SERVER['REQUEST_METHOD']==='POST') {
 verify_csrf(); $mode=$_POST['mode']??'login'; $email=strtolower(trim($_POST['email']??'')); $password=$_POST['password']??'';
 try {
  if($mode==='register') {
   $name=trim($_POST['name']??''); if(strlen($name)<2 || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($password)<8) throw new RuntimeException('Kontrollera namn, e-post och lösenord (minst 8 tecken).');
   $hash=password_hash($password,PASSWORD_DEFAULT); $s=db()->prepare('INSERT INTO users(name,email,password_hash) VALUES(?,?,?)'); $s->bind_param('sss',$name,$email,$hash); $s->execute(); $_SESSION['user_id']=$s->insert_id;
  } else {
   $s=db()->prepare('SELECT id,password_hash FROM users WHERE email=?'); $s->bind_param('s',$email); $s->execute(); $u=$s->get_result()->fetch_assoc();
   if(!$u || !password_verify($password,$u['password_hash'])) throw new RuntimeException('Fel e-postadress eller lösenord.'); $_SESSION['user_id']=(int)$u['id'];
  }
  session_regenerate_id(true); redirect('dashboard.php');
 } catch(mysqli_sql_exception $e) { $error=$e->getCode()===1062?'E-postadressen används redan.':'Något gick fel.'; } catch(RuntimeException $e){$error=$e->getMessage();}
}
page_top('Logga in'); ?>
<section class="auth-wrap"><div><span class="eyebrow">DIN JOLTSTUDIO</span><h1>Gör nästa<br><em>energikick.</em></h1></div><div class="panel auth-panel"><?php if($error):?><p class="error"><?=e($error)?></p><?php endif?>
<div class="tabs"><button class="active" data-tab="login">Logga in</button><button data-tab="register">Registrera</button></div>
<form method="post" id="authForm"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="mode" id="mode" value="login"><div id="nameRow" hidden><label>Namn</label><input name="name" autocomplete="name"></div><label>E-post</label><input type="email" name="email" required autocomplete="email"><label>Lösenord</label><input type="password" name="password" required minlength="8"><button>Fortsätt →</button></form></div></section>
<script>document.querySelectorAll('[data-tab]').forEach(b=>b.onclick=()=>{document.querySelectorAll('[data-tab]').forEach(x=>x.classList.remove('active'));b.classList.add('active');mode.value=b.dataset.tab;nameRow.hidden=b.dataset.tab!=='register'})</script>
<?php page_bottom(); ?>
