<?
$file = <<<'FILE'
<?
  require_once('functions.php');
  $input = json_decode(file_get_contents('php://input'));
  echo json_encode(updateProjectName(
    $input->{'slug'},
    $input->{'name'},
    $input->{'userID'},
    $input->{'passhash'},
  ));
?>

FILE;
file_put_contents('../../coordocs/updateProjectName.php', $file);
?>