<?
  require_once('functions.php');
  $input = json_decode(file_get_contents('php://input'));
  echo json_encode(createProject(
    $input->{'userID'},
    $input->{'passhash'},
  ));
?>