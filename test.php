<?
require('db.php');

$ar = [];
$ar['abc'] = 1234;
$ar['def'] = 5678;
$ar[123] = 'abc';
forEach($ar as $key => $val){
  echo "$key => $val \n";
}

?>
