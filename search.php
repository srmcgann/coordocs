<?
  require_once('functions.php');
  $input = json_decode(file_get_contents('php://input'));
  $passhash = $input->{'passhash'};
  $userID   = $input->{'userID'};
  $search   = $input->{'search'};
  echo json_encode(search($search, $userID, $passhash));
?>
