<?
  require_once('functions.php');
  $input = json_decode(file_get_contents('php://input'));
  $userName = $input->{'userName'};
  $password = $input->{'password'};
  $passhash = $input->{'passhash'};
  echo login($userName, $password, $passhash);
?>