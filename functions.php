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
  
  function incrementViews($slug){
    global $link;
    $sanSlug = mysqli_real_escape_string($link, $slug);
    $sql = "UPDATE projects SET views = views + 1 WHERE slug LIKE BINARY \"$sanSlug\"";
    mysqli_query($link, $sql);
  }

  function getAvatar($userID){
    global $link;
    $success = false;
    $sanUID = intval($userID);
    $sql = "SELECT avatar, name FROM users WHERE id = $sanUID";
    if($res = mysqli_query($link, $sql)){
      $row = mysqli_fetch_assoc($res);
      $success = true;
      return [
        'success' => $success,
        'avatar'  => $row['avatar'],
        'name'    => $row['name'],
      ];
    }else{
      return [
        'success' => $success,
        'avatar' => '',
        'name'   => '',
      ];
    }
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

  function updateTooltips($tooltips, $userID, $passhash){
    global $link;
    $success = false;
    if(authed($userID, $passhash)){
      $sanTooltips = intval($tooltips);
      $sanUID  = intval($userID);
      $sql = "UPDATE users SET tooltips = $sanTooltips WHERE id = $sanUID";
      $success = mysqli_query($link, $sql);
    }
    return ['success' => $success, 'tooltips' => $tooltips];
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
      'name'     => '',
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

  function search($search, $userID, $passhash, $exact, 
                  $searchMode, $caseSensitive,
                  $projectUserID='', $slug=''){
                    
    global $link;
    $success = false;
    $memo = [];
    $authed = authed($userID, $passhash);
    $sanUID = $authed ? intval($userID) : -1;
    switch($searchMode){
      case 'userSearch':
        if($authed && $userID == $projectUserID){
          $sql = "SELECT * FROM projects WHERE userID = $sanUID";
        }else if($projectUserID){
          $sanProjectUserID = intval($projectUserID);
          $sql = "SELECT * FROM projects WHERE userID = $sanProjectUserID AND private = 0";
        }else{
          $sql = "SELECT * FROM projects WHERE userID = $sanUID AND private = 0";
        }
      break;
      case 'curDocSearch':
        $sanSlug = mysqli_real_escape_string($link, $slug);
        if($authed){
          $sql = "SELECT * FROM projects WHERE (private = 0 OR userID = $sanUID) AND slug LIKE BINARY \"$sanSlug\"";
        }else{
          $sql = "SELECT * FROM projects WHERE private = 0 AND slug LIKE BINARY \"$sanSlug\"";
        }
      break;
      case 'everythingSearch':
        if($authed){
          $sql = "SELECT * FROM projects WHERE private = 0 OR userID = $sanUID";
        }else{
          $sql = "SELECT * FROM projects WHERE private = 0";
        }
      break;
    }
    $res = mysqli_query($link, $sql);
    $hits = [];
    $esearch = urlencode($search);
    $esearch = str_replace('+', '%20', $esearch);
    $esearch = str_replace('_', '%2F', $esearch);
    $esearch = str_replace('.', '%2E', $esearch);
    $esearch = str_replace('-', '%2D', $esearch);
    if($exact){
      $lsearch = $caseSensitive ? $search : strtolower($search);
      for($i = 0; $i < mysqli_num_rows($res); ++$i){
        $row = mysqli_fetch_assoc($res);
        $data = $caseSensitive ? $row['data'] : strtolower($row['data']);
        if(strpos($data, $lsearch) !== false){
          $success = true;
          $epages = explode('<pagebreak', $data);
          $page = 0;
          $hitsInPage = 0;
          $hitsInProj = 0;
          forEach($epages as $pg){
            if(strpos($pg, $lsearch) !== false){
              $hitsInProj += sizeof(explode($lsearch, $pg))-1;
            }
          }
          forEach($epages as $pg){
            $page++;
            if(strpos($pg, $lsearch) !== false){
              $hitsInPage = sizeof(explode($lsearch, $pg))-1;
              if(!isset($memo[$row['userID']])){
                $uid = $row['userID'];
                $sql = "SELECT * FROM users WHERE id = $uid";
                $res2 = mysqli_query($link, $sql);
                $row2 = mysqli_fetch_assoc($res2);
                $memo[$row['userID']] = [
                  'avatar'   => $row2['avatar'],
                  'userName' => $row2['name'],
                  'userID'   => $uid,
                ];
              }
              $hits[] = [
                "userID"        => $row["userID"],
                "slug"          => $row["slug"],
                "created"       => prettyDate($row["created"]),
                "updated"       => prettyDate($row["updated"]),
                "name"          => $row["name"],
                "avatar"        => $memo[$row['userID']]['avatar'],
                "userName"      => $memo[$row['userID']]['userName'],
                "userID"        => $memo[$row['userID']]['userID'],
                "hitsInPage"    => $hitsInPage,
                "hitsInProj"    => $hitsInProj,
                "exact"         => $exact,
                "caseSensitive" => $caseSensitive,
                "page"          => $page,
                "token"         => $lsearch,
                "links"         => [],
              ];
            }
          }
        }
      }
    }else{
      $lsearchTokens = explode(' ', $caseSensitive ? $search : strtolower($search));
      $ct   = 0;
      $memoa = [];
      $memo2 = [];
      forEach($lsearchTokens as $lsearch){
        $ct++;
        for($i = 0; $i < mysqli_num_rows($res); ++$i){
          if($ct==1){
            $row = mysqli_fetch_assoc($res);
            $memoa[$i] = $row;
          }else{
            $row = $memoa[$i];
          }
          $data = $caseSensitive ? $row['data'] : strtolower($row['data']);
          if(strpos($data, $lsearch) !== false){
            $success = true;
            $epages = explode('<pagebreak', $data);
            $page = 0;
            $hitsInPage = 0;
            $hitsInProj = 0;
            forEach($epages as $pg){
              if(strpos($pg, $lsearch) !== false){
                $hitsInProj += sizeof(explode($lsearch, $pg))-1;
              }
            }
            forEach($epages as $pg){
              if($ct == 1) $memo2[$i] = [];
              $page++;
              if(strpos($pg, $lsearch) !== false){
                $hitsInPage = sizeof(explode($lsearch, $pg))-1;
                if(!isset($memo[$row['userID']])){
                  $uid = $row['userID'];
                  if($ct == 1){
                    $sql = "SELECT * FROM users WHERE id = $uid";
                    $res2 = mysqli_query($link, $sql);
                    $row2 = mysqli_fetch_assoc($res2);
                    $memo2[$i][$page-1] = $row2;
                  }else{
                    $row2 = $memo2[$i][$page-1];
                  }
                  $memo[$row['userID']] = [
                    'avatar'   => $row2['avatar'],
                    'userName' => $row2['name'],
                    'userID'   => $uid,
                  ];
                }
                $hits[] = [
                  "userID"        => $row["userID"],
                  "slug"          => $row["slug"],
                  "created"       => prettyDate($row["created"]),
                  "updated"       => prettyDate($row["updated"]),
                  "name"          => $row["name"],
                  "avatar"        => $memo[$row['userID']]['avatar'],
                  "userName"      => $memo[$row['userID']]['userName'],
                  "userID"        => $memo[$row['userID']]['userID'],
                  "exact"         => $exact,
                  "caseSensitive" => $caseSensitive,
                  "hitsInPage"    => $hitsInPage,
                  "hitsInProj"    => $hitsInProj,
                  "page"          => $page,
                  "token"         => $lsearch,
                  "links"         => [],
                ];
              }
            }
          }
        }
      }
    }
    
    $nHits = [];
    $memo  = [];
    forEach($hits as $hit){
      if(!isset($memo[$hit['slug']])){
        $nHits[$hit['slug']]['tokens'] = [];
        $nHits[$hit['slug']]['hitsInPage'] = 0;
        $memo[$hit['slug']] = 1;
        forEach($hit as $key => $val) {
          if($key != "hitsInPage") $nHits[$hit['slug']][$key] = $val;
        }
      }
      $nHits[$hit['slug']]['tokens'][] = $hit['token'];
      $nHits[$hit['slug']]['hitsInPage'] += $hit['hitsInPage'];
    }
    
    $memo2 = [];
    forEach($hits as $hit){
      if(!isset($memo2[$hit['slug']][$hit['page']])){
        $memo2[$hit['slug']][$hit['page']] = 1;
        $tokens = implode(',', $nHits[$hit['slug']]['tokens']);
        $nHits[$hit['slug']]['links'][] = [
          'href' => 
            "./?h=$tokens&s={$hit['slug']}&p={$hit['page']}",
          'text' => "page link (<font style=\"color: #484\">pg# {$hit['page']}</font>)",
          'hitsInPage' => $hit['hitsInPage'],
          ];
      }
    }
    
    $ret = "<br><br>SEARCH RESULTS MATCHING:<br>&quot;$search&quot;<br><br>";
    
    forEach($nHits as $key => $hit){
      $bg = $hit['avatar'];
      $un = $hit['userName'];
      $ret .= "<div class=\"projectMenuItem\">
                 <div class=\"projectTools\">";
      if($sanUID == $hit['userID']){
        $ret .= "<button
                   class=\"toolButton editButton\"
                   style=\"background-image: url(edit.png)\"
                   data-customtooltip=\"edit -> {$hit['name']}\"
                   onclick=\"editProject('{$hit['slug']}',
                                           '{$hit['name']}')\"
                 ></button>
                 <button
                   class=\"toolButton deleteButton\"
                   style=\"background-image: url(delete.png)\"
                   data-customtooltip=\"delete -> {$hit['name']}\"
                   onclick=\"deleteProject('{$hit['slug']}',
                                           '{$hit['name']}')\"
                 ></button>
                 ";
      }
      $ret .= "</div>
                 <button
                  class=\"projectButton\"
                  data-customtooltip=\"view project: {$hit['name']}\"
                  onclick=\"window.LoadProject('{$hit['slug']}',{$hit['page']})\"
                 >{$hit['name']}</button>";
      $ret .= "<div class=\"userProjectCluster\">
                 <button
                   class=\"projectAvatar\"
                   style=\"background-image: url($bg);\"
                   onclick=\"location.href=location.origin+location.pathname+'?u={$hit['userID']}'\"
                   data-customtooltip=\"user: $un\"
                 ></button><br>
                 <span class=\"userName\">{$hit['userName']}</span>
               </div><br>
               <div class=\"projectDetails\">
                 <table class=\"projectDetailsTable\">
                   <tr>
                     <td class=\"projectDetailLabel\">user</td>
                     <td class=\"projectDetailItem\">{$hit['userName']}</td>
                   </tr>
                   <tr>
                     <td class=\"projectDetailLabel\">slug</td>
                     <td class=\"projectDetailItem\">{$hit['slug']}</td>
                   </tr>
                   <tr>
                     <td class=\"projectDetailLabel\">updated</td>
                     <td class=\"projectDetailItem\">{$hit['updated']}</td>
                   </tr>
                   <tr>
                     <td class=\"projectDetailLabel\">created</td>
                     <td class=\"projectDetailItem\">{$hit['created']}</td>
                   </tr>
                   <tr>
                     <td class=\"projectDetailLabel\">total hits</td>
                     <td class=\"projectDetailItem\">{$hit['hitsInProj']}</td>
                   </tr>";
      $ct = 0;
      forEach($hit['links'] as $link){
        $ct++;
           $pl = intval($link['hitsInPage']) > 1 ? 's' : '';
           $ret .= "<tr>
                     <td class=\"projectDetailLabel\"><font style=\"color: #484\">{$link['hitsInPage']} hit$pl</font> link $ct</td>
                     <td class=\"projectDetailItem\">
                       <a href=\"{$link['href']}\">{$link['text']}</a>
                     </td>
                   </tr>";
      }
      $ret .= "</table></div></div>";
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
             'data'    => renderProjectMenu(getProjects($userID, $passhash), $userID)];
  }
  
  function fullCurrentURL() {
    return ($_SERVER['HTTPS'] ? 'https' : 'http') .
            '://' . $_SERVER['SERVER_NAME'] .
            $_SERVER['REQUEST_URI'];
  }

  function renderProjectMenu($projects, $userID, $user=''){
    global $link;
    
    $sanUID = intval($user ? $user : $userID);
    $sql = "SELECT * FROM users WHERE id = $sanUID";
    $res = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($res);
    $userName = $row['name'];
    
    $ret = "<div class=\"projectList\"><br>$userName docs<br><br>";
    if(sizeof($projects) > 0){
      forEach($projects as $project){
        $ret .= "<div class=\"projectMenuItem\">
                  <div class=\"projectTools\">";
        $ret .=   "<button
                     class=\"toolButton editButton\"
                     style=\"background-image: url(edit.png)\"
                     data-customtooltip=\"edit -> {$project['name']}\"
                     onclick=\"editProject('{$project['slug']}',
                                             '{$project['name']}')\"
                   ></button>
                   <button
                     class=\"toolButton deleteButton\"
                     style=\"background-image: url(delete.png)\"
                     data-customtooltip=\"delete -> {$project['name']}\"
                     onclick=\"deleteProject('{$project['slug']}',
                                             '{$project['name']}')\"
                   ></button>
                   </div>";
        
        $ret .=   "<button
                    class=\"projectButton\"
                    data-customtooltip=\"view project: {$project['name']}\"
                    onclick=\"window.LoadProject('{$project['slug']}')\"
                   >{$project['name']}</button>
                   <div class=\"userProjectCluster\">
                     <button
                       class=\"projectAvatar\"
                       style=\"background-image: url({$project['avatar']});\"
                       onclick=\"location.href=location.origin+location.pathname+'?u={$project['userID']}'\"
                       data-customtooltip=\"user: {$project['user']}\"
                     ></button><br>
                     <span class=\"userName\">{$project['user']}</span>
                   </div><br>";
                   
        $pvt = '<font style="color:'.($project['private']?'#f02;">private':'#2f8;">public').'</font>';
        $ret .="<div class=\"projectDetails\">
                 <table class=\"projectDetailsTable\">
                   <tr>
                     <td class=\"projectDetailLabel\">user</td>
                     <td class=\"projectDetailItem\">{$project['user']}</td>
                   </tr>
                   <tr>
                     <td class=\"projectDetailLabel\">slug</td>
                     <td class=\"projectDetailItem\">{$project['slug']}</td>
                   </tr>
                   <tr>
                     <td class=\"projectDetailLabel\">views</td>
                     <td class=\"projectDetailItem\">{$project['views']}</td>
                   </tr>
                   <tr>
                     <td class=\"projectDetailLabel\">updated</td>
                     <td class=\"projectDetailItem\">{$project['updated']}</td>
                   </tr>
                   <tr>
                     <td class=\"projectDetailLabel\">created</td>
                     <td class=\"projectDetailItem\">{$project['created']}</td>
                   </tr>
                   <tr>
                     <td class=\"projectDetailLabel\">visibility</td>
                     <td class=\"projectDetailItem\">$pvt</td>
                   </tr>
                 </table></div>";
        $ret .= "</div>";
      }
    }else{
      $ret = '<div class="projectList" style="display: inline-block; width: 100%; position: absolute; top: calc(50% - 100px); left: 50%; transform: translate(-50%, -50%);"><br>my projects<br><br>';
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
    $tooltips     = '';
    $userID       = '';
    $sql = "SELECT * FROM users WHERE name LIKE BINARY \"$sanUserName\"";
    $res = mysqli_query($link, $sql);
    if(mysqli_num_rows($res)) {
      $row = mysqli_fetch_assoc($res);
      $tooltips   = $row['tooltips'];
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
      'tooltips' => $tooltips,
    ]);
  }
  
  function updatePassword($userID, $passhash, $oldPassword, $newPassword){
    global $link;
    $success = false;
    if(authed($userID, $passhash) && password_verify($oldPassword, $passhash)){
      $sanUID = intval($userID);
      $newPasshash = password_hash($newPassword, PASSWORD_DEFAULT);
      $sql = "UPDATE users SET passhash = \"$newPasshash\" WHERE id = $sanUID";
      if(mysqli_query($link, $sql)){
        $success = true;
        return [ 'success' => $success, 'passhash' => $newPasshash];
      }
    }
    return [ 'success' => $success, 'passhash' => $passhash ];
  }
  function updateAvatar($userID, $passhash, $avatar){
    global $link;
    $success = false;
    if(authed($userID, $passhash)){
      $sanUID = intval($userID);
      $sanAvatar = mysqli_real_escape_string($link, $avatar);
      $sql = "UPDATE users SET avatar = \"$sanAvatar\" WHERE id = $sanUID";
      if(mysqli_query($link, $sql)){
        $success = true;
      }
    }
    return [ 'success' => $success ];
  }
  
  function register($regUserName, $regPassword){
    global $link;
    if(nameIsAvailable($regUserName) == '<span class="nameAvailable">name is available</span>' && strlen($regPassword) >= 3){
      $passhash = password_hash($regPassword, PASSWORD_DEFAULT);
      $sanName  = mysqli_real_escape_string($link, $regUserName);
      $avatar   = 'defaultAvatar.jpg';
      $enabled  = 1;
      $tooltips = 1;
      $data     = [];
      $sql = "INSERT INTO users (name, passhash, avatar, enabled, tooltips) VALUES(\"$sanName\", \"$passhash\", \"$avatar\", $enabled, $tooltips)";
      if(mysqli_query($link, $sql)){
        $userID = mysqli_insert_id($link);
        return [ 'name'     => "create or search projects",
                 'slug'     => '',
                 'private'  => 0,
                 'error'    => '',
                 'userID'   => $userID,
                 'userName' => $regUserName,
                 'success'  => true,
                 'page'     => 0,
                 'data'     => renderProjectMenu(getProjects($userID, $passhash))];
      }else{
        return [ 'success' => false, $data ];
      }
    }else{
      return [ 'success' => false, $data ];
    }
  }
  
  function nameIsAvailable($name){
    global $link;
    $ret = true;
    $sql = "SELECT name FROM users";
    if($res = mysqli_query($link, $sql)){
      $lname = strtolower($name);
      for($i=0; $ret && $i<mysqli_num_rows($res); ++$i){
        $row = mysqli_fetch_assoc($res);
        $n = strtolower($row['name']);
        if($n == $lname) $ret = false;
      }
      if($ret){
        return '<span class="nameAvailable">name is available</span>';
      }else{
        return '<span class="nameTaken">this name is taken!</span>';
      }
    }else{
      return '<span class="nameTaken">this name is taken!</span>';
    }
  }
  
  function getProjects($userID, $passhash, $user = ''){
    global $link;
    $projects = [];
    $authed = authed($userID, $passhash);
    if($user || $authed){
      if($user){
        $sanUID      = intval($user);
        $sanUID2      = intval($userID);
        if($sanUID == $sanUID2 && $authed){
          $sql = "SELECT * FROM projects WHERE userID = $sanUID";
        }else {
          $sql = "SELECT * FROM projects WHERE userID = $sanUID AND private = 0";
        }
      }else{
        $sanUID      = intval($userID);
        $sql = "SELECT * FROM projects WHERE userID = $sanUID";
      }
      $res = mysqli_query($link, $sql);
      if(mysqli_num_rows($res)){
        $sql = "SELECT * FROM users WHERE id = $sanUID";
        $res2 = mysqli_query($link, $sql);
        $row2 = mysqli_fetch_assoc($res2);
        $userName = $row2['name'];
        $avatar = $row2['avatar'];
        for($i = 0; $i < mysqli_num_rows($res); ++$i){
          $row = mysqli_fetch_assoc($res);
          $projects[] = [
            'user'    => $userName,
            'avatar'  => $avatar,
            'userID'  => $sanUID,
            'name'    => $row['name'],
            'slug'    => $row['slug'],
            'views'   => $row['views'],
            'created' => prettyDate($row['created']),
            'updated' => prettyDate($row['updated']),
            'private' => $row['private'],
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
                   'data'     => renderProjectMenu(getProjects($userID, $passhash), $userID)];
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
                 'data'     => renderProjectMenu(getProjects($userID, $passhash), $userID)];
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
               'data'     => renderProjectMenu(getProjects($userID, $passhash), $userID)];
    }
  }
  
  function pageData($slug, $page, $userID, $passhash, $user) {
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
          incrementViews($slug);
          return [ 'name'     => $row['name'],
                   'slug'     => $row['slug'],
                   'private'  => intval($row['private']),
                   'userID'   => $pUID,
                   'userName' => $userName,
                   'success'  => true,
                   'error'    => '',
                   'page'     => intval($page),
                   'data'     => $data];
        }else{
          $pUID = intval($user ? $user : $userID);
          $sql = "SELECT name FROM users WHERE id = $pUID";
          $res = mysqli_query($link, $sql);
          $row = mysqli_fetch_assoc($res);
          $userName = $row['name'];
          return [ 'name'     => "create or search projects",
                   'slug'     => $slug,
                   'private'  => 0,
                   'userID'   => $pUID,
                   'userName' => $userName,
                   'success'  => false,
                   'error'    => "slug ($slug) not user\'s. user ok.",
                   'page'     => intval($page),
                   'data'     => renderProjectMenu(getProjects($userID, $passhash), $userID, $user)];
        }
      }else{
        $pUID = intval($user ? $user : $userID);
        $sql = "SELECT name FROM users WHERE id = $pUID";
        $res = mysqli_query($link, $sql);
        $row = mysqli_fetch_assoc($res);
        $userName = $row['name'];
        return [ 'name'     => "create or search projects",
                 'slug'     => $slug,
                 'private'  => 0,
                 'error'    => 'user auth not ok.',
                 'userID'   => $pUID,
                 'userName' => $userName,
                 'success'  => false,
                 'page'     => intval($page),
                 'data'     => renderProjectMenu(getProjects($userID, $passhash), $userID, $user)];
      }
    }
    $pUID = intval($user ? $user : $userID);
    $sql = "SELECT name FROM users WHERE id = $pUID";
    $res = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($res);
    $userName = $row['name'];
    return [ 'name'     => "$userName docs",
             'slug'     => $slug,
             'private'  => 0,
             'error'    => '',
             'userID'   => $pUID,
             'userName' => $userName,
             'success'  => true,
             'page'     => intval($page),
             'data'     => renderProjectMenu(getProjects($userID, $passhash, $user), $userID, $user)];
 }
  
?>





