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
  
  function prettyDate($dateTime){
    return date('F d Y', strtotime($dateTime));
  }

  function updateProjectName($slug, $name, $userID, $passhash){
    global $link;
    $success = false;
    if(authed($userID, $passhash)){
      $sanName = mysqli_real_escape_string($link, $name);
      $sanSlug = mysqli_real_escape_string($link, $slug);
      $sanUID  = intval($userID);
      $sql = "UPDATE projects SET name = \"$sanName\" WHERE userID = $sanUID AND slug LIKE BINARY \"$sanSlug\"";
      $success = mysqli_query($link, $sql);
    }
    return ['success' => $success];
  }

  function updatePrivacy($slug, $private, $userID, $passhash){
    global $link;
    $success = false;
    if(authed($userID, $passhash)){
      $sanPrivate = intval($private);
      $sanSlug = mysqli_real_escape_string($link, $slug);
      $sanUID  = intval($userID);
      $sql = "UPDATE projects SET private = $sanPrivate WHERE userID = $sanUID AND slug LIKE BINARY \"$sanSlug\"";
      $success = mysqli_query($link, $sql);
    }
    return ['success' => $success];
  }

  function pageMetaData() {
    global $link;
    $page = 0;
    $ret = [
      'url'      => '',
      'page'     => '',
      'slug'     => '',
      'slug'     => '',
      'created'  => '',
      'updated'  => '',
      'userName' => '',
      'name'     => 'coordocs - better docs',
    ];
    $ret['url'] = fullCurrentURL();
    if(!!strpos($ret['url'], '?')){
      $params = explode('&', parse_url($ret['url'])['query']);
      if(sizeof($params) > 0){
        forEach($params as $param){
          $pair = explode('=', $param);
          switch($pair[0]){
            case 'p':
              $ret['page'] = intval($pair[1]); 
              break;
            case 's': 
              $ret['slug'] = $pair[1];
              $sql = "SELECT * FROM projects WHERE slug LIKE BINARY \"{$ret['slug']}\"";
              $res = mysqli_query($link, $sql);
              if(mysqli_num_rows($res)){
                $row = mysqli_fetch_assoc($res);
                $ret['name'] = $row['name'];
                $ret['created'] = prettyDate($row['created']);
                $ret['updated'] = prettyDate($row['updated']);
                $userID = $row['userID'];
                
                $sql = "SELECT name FROM users WHERE id = $userID";
                $res = mysqli_query($link, $sql);
                $row = mysqli_fetch_assoc($res);
                $ret['userName'] = $row['name'];
              }else{
                $ret['name'] = '';
                $ret['created'] = '';
                $ret['updated'] = '';
                $userID = '';
                $ret['userName'] = '';
              }
              break;
          }
        }
      }
    }
    return $ret;
  }
  
  function search($search, $userID, $passhash){
    global $link;
    $success = false;
    $memo = [];
    $sanUID = intval($userID);
    $sql = "SELECT * FROM projects WHERE userID = $sanUID OR private = 0";
    $res = mysqli_query($link, $sql);
    $hits = [];
    $lsearch = strtolower($search);
    for($i = 0; $i < mysqli_num_rows($res); ++$i){
      $row = mysqli_fetch_assoc($res);
      $data = strtolower($row['data']);
      if(strpos($data, $lsearch) !== false){
        $success = true;
        $pages = explode('<pagebreak', $data);
        $ct = 0;
        forEach($pages as $page){
          if(strpos($page, $lsearch) !== false){
            if(!isset($memo[$row['userID']])){
              $uid = $row['userID'];
              $sql = "SELECT * FROM users WHERE id = $uid";
              $res2 = mysqli_query($link, $sql);
              $row2 = mysqli_fetch_assoc($res2);
              $memo[$row['userID']] = [
                'avatar' => $row2['avatar'],
                'userName' => $row2['name'],
              ];
            }
            $ct++;
            $hits[] = [
              "userID"   => $row["userID"],
              "slug"     => $row["slug"],
              "created"  => $row["created"],
              "updated"  => $row["updated"],
              "name"     => $row["name"],
              "avatar"   => $memo[$row['userID']]['avatar'],
              "userName" => $memo[$row['userID']]['userName'],
              "page"     => $ct,
            ];
          }
        }
      }
    }
    
    $ret = "";
    
    forEach($hits as $hit){
      $ret .= "<div class=\"projectMenuItem\">
                 <button
                  class=\"projectButton\"
                  onclick=\"window.LoadProject('{$hit['slug']}')\"
                 >{$hit['name']}</button>";
      if($sanUID == $hit['userID']){
        $ret .= "<button
                   class=\"deleteButton\"
                   title=\"delete this project? -> {$hit['name']}\"
                   onclick=\"deleteProject('{$hit['slug']}',
                                           '{$hit['name']}')\"
                 ></button>";
      }else{
        $bg = $hit['avatar'];
        $un = $hit['userName'];
        $ret .= "<button
                   class=\"deleteButton\"
                   style=\"background-image: url($bg);\"
                   title=\"user: $un\"
                 ></button>";
      }
      $ret .= "</div>";
    }
    
    return [
      'success' => $success,
      'hits' => $hits,
      'html' => $ret,
      'name' => 'search results',
    ];
  }
  
  function updateProject($slug, $content, $userID, $passhash){
    
    
      if(mysqli_query($link, $sql)){
		    $updated = date("Y-m-d H:i:s",strtotime("now"));
        $sql = "UPDATE projects SET updated = \"$updated\" WHERE slug LIKE BINARY \"$slug\"";
        mysqli_query($link, $sql);
      }
  }
  
  
  function updateProjectData($slug, $userID, $passhash, $data) {
    global $link;
    if(authed($userID, $passhash)){
      $sanUID = intval($userID);
      $sanData = mysqli_real_escape_string($link, $data);
      $sql = "UPDATE projects SET data = \"$sanData\" WHERE slug LIKE BINARY \"$slug\" AND userID = $sanUID";
      if(mysqli_query($link, $sql)){
        return [ 'error'   => '', 'success' => true ];
      }else{
        return [ 'error'   => 'could not update data', 'success' => false ];
      }
    }else{
      return [ 'error'   => 'auth failure on update', 'success' => false ];
    }
  }

  
  function deleteProject($slug, $userID, $passhash) {
    global $link;
    if(authed($userID, $passhash)){
      $sql = "DELETE FROM projects WHERE slug LIKE BINARY \"$slug\"";
      mysqli_query($link, $sql);
    }
    return [ 'name'    => "create or search projects",
             'error'   => '',
             'userID'  => $userID,
             'success' => true,
             'data'    => renderProjectMenu(getProjects($userID, $passhash))];
  }
  
  function fullCurrentURL() {
    return ($_SERVER['HTTPS'] ? 'https' : 'http') .
            '://' . $_SERVER['SERVER_NAME'] .
            $_SERVER['REQUEST_URI'];
  }

  function renderProjectMenu($projects){
    $ret = '<div class="projectList"><br><br>my projects<br><br>';
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
      $ret .= '<br><br><br><div style="color: #888;"> you have no projects </div><br>';
      $ret .= '<div style="color: #888;"> create a new one by clicking \'new\' above </div>';
    }
    $ret .= '</div>';
    return $ret;
  }
  
  function login($userName, $password, $passhash){
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
  
  function getProjects($userID, $passhash){
    global $link;
    $projects = [];
    if(authed($userID, $passhash)){
      $sanUID      = intval($userID);
      $sql = "SELECT * FROM projects WHERE userID = $sanUID";
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
  
  function authed($userID, $passhash){
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

  function privateDoc($slug) {
    global $link;
    $sanSlug = mysqli_real_escape_string($link, $slug);
    $sql = "SELECT private FROM projects WHERE slug LIKE BINARY \"$sanSlug\"";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)){
      $row = mysqli_fetch_assoc($res);
      return !!intval($row['private']);
    }else{
      return false;
    }
  }
  
  function genID($len = 16){
    global $link;
    do{
      $ret = '';
      for($i = 0; $i<$len; ++$i) $ret .= rand(1,9);
      $sql = "SELECT id FROM projects WHERE id = $ret";
      $res = mysqli_query($link, $sql);
    }while(mysqli_num_rows($res));
    return $ret;
  }
    
  function newProjectTemplate(){
    return "<center>![an example logo](example.jpg)</center>
    
# My New Project
    
click the ***view*** button above to see the effects


## sub-sections are denoted this way, with emphasis
### sub-sections are denoted this way, with emphasis
#### sub-sections are denoted this way, with emphasis
##### sub-sections are denoted this way, with emphasis


you may insert ``code snippets`` like this

or whole code blocks...

```
myFunc = () => {
  var str = \"like this\"
  return str
}
```

One great feature of ``coordocs`` is that you can paginate your doc!

for example, use this tag to split your document
onto as many pages as you like:

<pagebreak/>

# A new page!

### links and images

links use [this format](https://github.com)<br><br>

and images, similarly ![an example logo](example.jpg)

## other formatting options
you may make **bold** text, or *italic*, or ***both***

#### special note:
changes made here are pushed immediately, so take care with keystrokes.


";
  }
  
  function createProject($userID, $passhash) {
    global $link;
    if(authed($userID, $passhash)){
      $sanUID = intval($userID);
      $newID  = genID();
      $slug   = mysqli_real_escape_string($link, decToAlpha($newID));
      $data   = mysqli_real_escape_string($link, newProjectTemplate());
      $sql    = "INSERT INTO projects (
                id,
                name,
                userID,
                data,
                slug,
                private
              ) VALUES(
                $newID,
                \"My New Project\",
                $sanUID,
                \"$data\",
                \"$slug\",
                1
              )";
      if(mysqli_query($link, $sql)){    
        $sql = "SELECT * FROM projects WHERE slug LIKE BINARY \"$slug\" AND
                                      (private = 0 OR userID = $sanUID)";
        $res = mysqli_query($link, $sql);
        if(mysqli_num_rows($res)){
          $row = mysqli_fetch_assoc($res);
          $data = str_replace('<','&lt;', $row['data']);
          $pUID = $row['userID'];
          $sql = "SELECT name FROM users WHERE id = $pUID";
          $res2 = mysqli_query($link, $sql);
          $row2 = mysqli_fetch_assoc($res2);
          $userName = $row2['name'];
          return [ 'name'     => $row['name'],
                   'slug'     => $row['slug'],
                   'private'  => intval($row['private']),
                   'userID'   => $userID,
                   'userName' => $userName,
                   'success'  => true,
                   'error'    => '',
                   'page'     => 0,
                   'data'     => $data];
        }else{
          return [ 'name'     => "create or search projects",
                   'slug'     => '',
                   'private'  => 0,
                   'error'    => 'inserted project not found',
                   'userID'   => $userID,
                   'userName' => '',
                   'success'  => false,
                   'page'     => 0,
                   'data'     => renderProjectMenu(getProjects($userID, $passhash))];
        }
      }else{
        return [ 'name'     => "create or search projects",
                 'slug'     => '',
                 'private'  => 0,
                 'error'    => 'could not insert project',
                 'userID'   => $userID,
                 'userName' => '',
                 'success'  => false,
                 'page'     => 0,
                 'data'     => renderProjectMenu(getProjects($userID, $passhash))];
      }
    }else{
      return [ 'name'     => "create or search projects",
               'slug'     => '',
               'private'  => 0,
               'error'    => 'auth failed',
               'userID'   => $userID,
               'userName' => '',
               'success'  => false,
               'page'     => 0,
               'data'     => renderProjectMenu(getProjects($userID, $passhash))];
    }
  }
  
  function pageData($slug, $page, $userID, $passhash) {
    global $link;
    if($slug){
      if(!privateDoc($slug) || authed($userID, $passhash)){
        $sanSlug = mysqli_real_escape_string($link, $slug);
        $sanUID  = intval($userID);
        $sql = "SELECT * FROM projects WHERE slug LIKE BINARY \"$sanSlug\" AND
                                      (private = 0 OR userID = $sanUID)";
        $res = mysqli_query($link, $sql);
        if(mysqli_num_rows($res)){
          $row = mysqli_fetch_assoc($res);
          $data = str_replace('<','&lt;', $row['data']);
          $pUID = $row['userID'];
          $sql = "SELECT name FROM users WHERE id = $pUID";
          $res2 = mysqli_query($link, $sql);
          $row2 = mysqli_fetch_assoc($res2);
          $userName = $row2['name'];
          return [ 'name'     => $row['name'],
                   'slug'     => $row['slug'],
                   'private'  => intval($row['private']),
                   'userID'   => $userID,
                   'userName' => $userName,
                   'success'  => true,
                   'error'    => '',
                   'page'     => intval($page),
                   'data'     => $data];
        }else{
          return [ 'name'     => "create or search projects",
                   'slug'     => $slug,
                   'private'  => 0,
                   'userID'   => $userID,
                   'userName' => '',
                   'success'  => false,
                   'error'    => "slug ($slug) not user\'s. user ok.",
                   'page'     => intval($page),
                   'data'     => renderProjectMenu(getProjects($userID, $passhash))];
        }
      }else{
        return [ 'name'     => "create or search projects",
                 'slug'     => $slug,
                 'private'  => 0,
                 'error'    => 'user auth not ok.',
                 'userID'   => $userID,
                 'userName' => '',
                 'success'  => false,
                 'page'     => intval($page),
                 'data'     => renderProjectMenu(getProjects($userID, $passhash))];
      }
    }
    return [ 'name'     => "create or search projects",
             'slug'     => $slug,
             'private'  => 0,
             'error'    => '',
             'userID'   => $userID,
             'userName' => '',
             'success'  => true,
             'page'     => intval($page),
             'data'     => renderProjectMenu(getProjects($userID, $passhash))];
 }
  
?>


