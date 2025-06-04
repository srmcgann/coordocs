<?
  require_once('functions.php');
  $input = json_decode(file_get_contents('php://input'));
  echo json_encode(deleteProject($input->{'slug'}, $input->{'userID'}, $input->{'passhash'}));
?>