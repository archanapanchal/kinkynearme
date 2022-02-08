<?php
$path = '/var/www/vhosts/kinkynearme.mobilegiz.com/httpdocs';
$zip = new ZipArchive;
$res = $zip->open('knm.zip');
if ($res === TRUE) {
  $zip->extractTo($path);
  $zip->close();
  echo 'woot!';
} else {
  echo 'doh!';
}