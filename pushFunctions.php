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
    $ret = '<div class="projectList">my projects<br>';
    if(sizeof($projects) > 0){
      forEach($projects as $project){
        $ret .= "<div class=\"projectMenuItem\">
                   <button
                    class=\"projectButton\"
                    onclick=\"window.LoadProject('{$project['slug']}')\"
                   >{$project['name']}</button>
                   <button
                     class=\"deleteButton\"
                     title=\"delete this project? -> {$project['name']}\"
                     onclick=\"deleteProject('{$project['slug']}',
                                             '{$project['name']}')\"
                   ></button>
                 </div>";
      }
    }else{
      $ret .= '<span style="color: #888;"> you have no projects </span>';
    }
    $ret .= '</div>';
    return $ret;
  }
  
  function Login($userName, $password, $passhash){
    global $link;
    $ret = false;
    $sanUserName  = mysqli_real_escape_string($link, $userName);
    $avatar       = '';
    $userID       = '';
    $sql = "SELECT * FROM users WHERE name LIKE BINARY \"$sanUserName\"";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)) {
      $row = mysqli_fetch_assoc($res);
      $avatar     = $row['avatar'];
      $userID     = $row['id'];
      if($passhash){
        if($row['passhash'] == $passhash) $ret = true;
      }else{
        $passhash = $row['passhash'];
        if(password_verify($password, $passhash)) $ret = true;
      }
    }
    
    return json_encode([
      'success' => $ret,
      'avatar' => $avatar,
      'userID' => $userID,
      'passhash' => $passhash,
    ]);
  }
  
  function GetProjects($userID, $passhash){
    global $link;
    $projects = [];
    if(Authed($userID, $passhash)){
      $sanUID      = intval($userID);
      $sql = "SELECT * FROM projects WHERE $userID = $sanUID";
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
    }
    return $projects;
  }
  
  function Authed($userID, $passhash){
    global $link;
    $ret = false;
    $sanUID      = intval($userID);
    $sanPasshash = mysqli_real_escape_string($link, $passhash);
    $sql = "SELECT * FROM users WHERE id = $sanUID AND
                                  passhash LIKE BINARY \"$sanPasshash\" AND
                                  enabled = 1";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)) $ret = true;
    return $ret;
  }

  function PageData($slug, $page, $userID, $passhash) {
    global $link;
    if($slug){
      if(Authed($userID, $passhash)){
        $sanSlug = mysqli_real_escape_string($link, $slug);
        $sanUID  = intval($userID);
        $sql = "SELECT * FROM projects WHERE slug LIKE BINARY \"$sanSlug\" AND
                                                   userID = $sanUID";
        $res = mysqli_query($link, $sql);
        if(mysqli_num_rows($res)){
          $row = mysqli_fetch_assoc($res);
          $data = str_replace('<','&lt;', $row['data']);
          return [ 'name'    => $row['name'],
                   'slug'    => $row['slug'],
                   'userID'  => $userID,
                   'success' => true,
                   'error'   => '',
                   'page'    => intval($page),
                   'data'    => $data];
        }else{
          return [ 'name'    => "create or search projects",
                   'slug'    => $slug,
                   'userID'  => $userID,
                   'success' => false,
                   'error'   => "slug ($slug) not user\'s. user ok.",
                   'page'    => intval($page),
                   'data'    => RenderProjectMenu(GetProjects($userID, $passhash))];
        }
      }else{
        return [ 'name'    => "create or search projects",
                 'slug'    => $slug,
                 'error'   => 'user auth not ok.',
                 'userID'  => $userID,
                 'success' => false,
                 'page'    => intval($page),
                 'data'    => RenderProjectMenu(GetProjects($userID, $passhash))];
      }
    }
    return [ 'name'    => "create or search projects",
             'slug'    => $slug,
             'error'   => '',
             'userID'  => $userID,
             'success' => true,
             'page'    => intval($page),
             'data'    => RenderProjectMenu(GetProjects($userID, $passhash))];
 }
  
?>


FILE;
file_put_contents('../../coordocs/functions.php', $file);
?>