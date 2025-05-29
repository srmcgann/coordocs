<?php
  require_once('db.php');
  $sql = "SELECT * FROM projects";
  $res = mysqli_query($link, $sql);
  for($i = 0; $i<mysqli_num_rows($res); ++$i){
    $row = mysqli_fetch_assoc($res);
    echo 'project id -> ' . $row['id'] . "\n";
  }
?>
