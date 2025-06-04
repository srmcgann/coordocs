<?
$file = <<<'FILE'
<?
  require_once('functions.php');
  $input = json_decode(file_get_contents('php://input'));
  echo json_encode(updatePrivacy(
    $input->{'slug'},
    $input->{'private'},
    $input->{'userID'},
    $input->{'passhash'},
  ));
?>

FILE;
file_put_contents('../../coordocs/updatePrivacy.php', $file);
?>