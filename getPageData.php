<?
  require_once('functions.php');
  $input = json_decode(file_get_contents('php://input'));
  $passhash = $input->{'passhash'};
  $slug     = $input->{'slug'};
  $userID   = $input->{'userID'};
  $page     = $input->{'page'};
  $user     = $input->{'user'};
  echo json_encode(pageData($slug, $page, $userID, $passhash, $user));
?>
