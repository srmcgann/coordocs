<?
$file = <<<'FILE'
<?
  require_once('functions.php');
  $input = json_decode(file_get_contents('php://input'));
  $passhash = $input->{'passhash'};
  if($passhash == 'samplePasshash'){
    DeleteProject($input->{'slug'});
  }
?>

FILE;
file_put_contents('../../coordocs/deleteProject.php', $file);
?>
