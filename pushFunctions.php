<?
$file = <<<'FILE'
<?php
  require_once('db.php');

  function alphaToDec($val){
    $pow=0;
    $res=0;
    while($val!=""){
      $cur=$val[strlen($val)-1];
      $val=substr($val,0,strlen($val)-1);
      $mul=ord($cur)<58?$cur:ord($cur)-(ord($cur)>96?87:29);
      $res+=$mul*pow(62,$pow);
      $pow++;
    }
    return $res;
  }

  function decToAlpha($val){
    $alphabet="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $ret="";
    while($val){
      $r=floor($val/62);
      $frac=$val/62-$r;
      $ind=(int)round($frac*62);
      $ret=$alphabet[$ind].$ret;
      $val=$r;
    }
    return $ret==""?"0":$ret;
  }
  

  function deleteProject($slug) {
    global $link;
    $sql = "DELETE FROM projects WHERE slug LIKE BINARY \"$slug\"";
    mysqli_query($link, $sql);
    echo RenderProjectMenu(GetProjects());
  }
  
  function fullCurrentURL() {
    return ($_SERVER['HTTPS'] ? 'https' : 'http') .
            '://' . $_SERVER['SERVER_NAME'] .
            $_SERVER['REQUEST_URI'];
  }

  function RenderProjectMenu($projects){
    $ret = '';
    if(sizeof($projects) > 0){
      forEach($projects as $project){
        $ret .= "<div class=\"projectMenuItem\">
                   <button
                    class=\"projectButton\"
                    onclick=\"loadProject('{$project['slug']}')\"
                   >{$project['name']}</button>
                   <button
                     class=\"deleteButton\"
                     title=\"delete this project? -> {$project['name']}\"
                     onclick=\"deleteProject('{$project['slug']}',
                                             '{$project['name']}')\"
                   ></button>
                 </div>";
      }
    }
    return $ret;
  }
  
  function GetProjects(){
    global $link;
    $projects = [];
    $sql = "SELECT * FROM projects";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      for($i = 0; $i < mysqli_num_rows($res); ++$i){
        $row = mysqli_fetch_assoc($res);
        $projects[] = [
          'name' => $row['name'],
          'slug' => $row['slug'],
        ];
      }
    }
    return $projects;
  }

  function PageData() {
    global $link;
    $url = fullCurrentURL();
    if(!!strpos($url, '?')){
      $params = explode('&', parse_url($url)['query']);
      if(sizeof($params) > 0){
        forEach($params as $param){
          $pair = explode('=', $param);
          $key = $pair[0];
          $val = $pair[1];
          if($key == 'p'){
            $val = mysqli_real_escape_string($link, $val);
            $sql = "SELECT * FROM projects WHERE slug LIKE BINARY \"$val\"";
            $res = mysqli_query($link, $sql);
            if(mysqli_num_rows($res)){
              $row = mysqli_fetch_assoc($res);
              return [ 'name' => $row['name'],
                       'data' => $row['data'] ];
            }
          }
        }
      }else{
        return [ 'name' => "create or search projects",
                 'data' => []];
      }
    }else{
      return [ 'name' => "create or search projects",
               'data' => RenderProjectMenu(GetProjects())];
    }
  }
  
?>




FILE;
file_put_contents('../../coordocs/functions.php', $file);
?>