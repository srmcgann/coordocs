<?
  require_once('functions.php');
  $input = json_decode(file_get_contents('php://input'));
  echo json_encode(updateTooltips(
    $input->{'tooltips'},
    $input->{'userID'},
    $input->{'passhash'},
  ));
?>