<?
$file = <<<'FILE'
<?
  require_once('functions.php');
  $input = json_decode(file_get_contents('php://input'));
  echo json_encode(updateProjectData(
    $input->{'slug'},
    $input->{'userID'},
    $input->{'passhash'},
    $input->{'data'},
  ));
?>

FILE;
file_put_contents('../../coordocs/updateProjectData.php', $file);
?>