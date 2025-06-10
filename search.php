<?
  require_once('functions.php');
  $input = json_decode(file_get_contents('php://input'));
  $passhash      = $input->{'passhash'};
  $userID        = $input->{'userID'};
  $search        = $input->{'search'};
  $exact         = $input->{'exact'};
  $searchMode    = $input->{'searchMode'};
  $caseSensitive = $input->{'caseSensitive'};
  $projectUserID = $input->{'projectUserID'};
  $slug          = $input->{'slug'};
  echo json_encode(search($search, $userID, $passhash, $exact,
                          $searchMode, $caseSensitive,
                          $projectUserID, $slug));
?>

