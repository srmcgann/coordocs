<?
$file = <<<'FILE'
<?
  require_once('functions.php');
  $input = json_decode(file_get_contents('php://input'));
  echo json_encode(nameIsAvailable( $input->{'regUserName'}));
?>

FILE;
file_put_contents('../../coordocs/nameIsAvailable.php', $file);
?>