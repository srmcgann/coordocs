<?
  require_once('functions.php');
  $input = json_decode(file_get_contents('php://input'));
  $userID   = $input->{'userID'};
  echo json_encode(getAvatar($userID));
?>