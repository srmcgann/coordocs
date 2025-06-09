<?
$file = <<<'FILE'
<?
  require_once('functions.php');
  $input = json_decode(file_get_contents('php://input'));
  echo json_encode(updatePassword(
    $input->{'userID'},
    $input->{'passhash'},
    $input->{'oldPassword'},
    $input->{'newPassword'},
  ));
?>


FILE;
file_put_contents('../../coordocs/updatePassword.php', $file);
?>