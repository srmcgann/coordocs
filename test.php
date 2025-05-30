<?
require('db.php');
  $seen = date(strtotime('2025-05-15 23:29:15'));
  $now = time();
  echo (($now - $seen) > 8) . "\n";
?>
