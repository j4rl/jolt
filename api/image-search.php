<?php
require '../config.php';require_login();
$query=trim(mb_substr($_GET['q']??'',0,100));if(mb_strlen($query)<2)json_response(['error'=>'Skriv minst två tecken.'],422);
$url='https://api.openverse.org/v1/images/?'.http_build_query(['q'=>$query,'page_size'=>12,'mature'=>'false','license'=>'cc0,pdm,by,by-sa']);
$ch=curl_init($url);curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>12,CURLOPT_CONNECTTIMEOUT=>5,CURLOPT_USERAGENT=>'Jolt/1.0 image search']);$raw=curl_exec($ch);$status=(int)curl_getinfo($ch,CURLINFO_RESPONSE_CODE);curl_close($ch);
if($raw===false||$status!==200)json_response(['error'=>'Bildsökningen kunde inte nås just nu.'],502);
$data=json_decode($raw,true);$images=[];foreach($data['results']??[] as $image){if(empty($image['id'])||empty($image['thumbnail']))continue;$images[]=['id'=>$image['id'],'title'=>$image['title']?:'Namnlös bild','thumbnail'=>$image['thumbnail'],'creator'=>$image['creator']?:'Okänd skapare','license'=>strtoupper(trim(($image['license']??'').' '.($image['license_version']??''))),'source_url'=>$image['foreign_landing_url']??''];}
json_response(['images'=>$images]);
