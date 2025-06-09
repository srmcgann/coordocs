<!--
  to do
  ✔ working login & project <-> user assoc
  ✔ meta / social data
  ✔ create-project form
  ✔ markdown editor tool w/ toggle
  ✔ docs page for docs
  ✔ page break syntax for markdown
  ✔ delete button on doc itself, plus list page
  ✔ search function
  ✔ copy button @ all code blocks
  ✔ highlighting / remove highlighting
  ✔ registration
  ✔ user profile settings screen, w/ avatar, password change, delete acct, etc.
  * theme selector
-->
<?php
  require_once('functions.php');
  $pageMetaData = pageMetaData();
  if($pageMetaData['slug']){
    $pg = intval($pageMetaData['page']) > 1 ? "  -  page {$pageMetaData['page']}" : '';
    $pageTitle = "{$pageMetaData['name']}  -  {$pageMetaData['userName']}$pg  -  created {$pageMetaData['created']}  -  updated {$pageMetaData['updated']}";
  }else{
    $pageTitle = "{$pageMetaData['name']} - coordocs, better docs!";
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <link rel="icon" type="image/png" href="doc.png">
    <meta charset="UTF-8">
    <title><?=$pageTitle?></title>
    <style>
      body, html{
        width: 100vw;
        min-height: 100vh;
        margin: 0;
        background: #111;
        color: #edc;
        font-family: monospace, verdana;
        font-size: 16px;
        overflow: hidden;
      }
      h1, h2{
        border-bottom: 1px solid #fff4;
      }
      li{
        line-height: 1em;
      }
      .header{
        z-index: 10;
        position: fixed;
        top: 0;
        width: 100vw;
        font-size: 24px;
        padding-left: 4px;
        height: 32px;
        background: linear-gradient(90deg, #208, #1644, #000c, #011c, #111c);
        color: #fff;
        text-shadow: 2px 2px 2px #000;
        border-bottom: 1px solid #4ff3;
      }
      .coordocsLogo{
        display: inline-block;
        top: 0;
        right: 0;
        width: 130px;
        height: calc(130px * .24);
        background-image: url(coordocs_logo.png);
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
        background-color: transparent;
        cursor: pointer;
      }
      .main img{
        margin: 5px;
        border: 1px solid #fff1;
        border-radius: 5px;
      }
      .main{
        height: calc(100vh - 200px);
        border: none;
        padding: 10px;
        padding-top: 0;
        overflow-y: auto;
        overflow-x: hidden;
        background: #111;
        color: #ffd;
        font-family: monospace, verdana;
        padding-bottom: 200px;
      }
      .coordocsSearchResult{
        background: #8f4;
        color: #132;
      }
      .main img{
        display: block;
        /*border: 1px solid #fff4;*/
        border-radius: 5px;
      }
      .toolbarComponent{
        border-radius: 10px;
        background: #000;
        height: 35px;
        /* border: 1px solid #82f4; */
        display: inline-block;
        vertical-align: top;
        position: relative;
        text-align: center;
        padding-left: 10px;
        padding-right: 10px;
        margin: 2px;
        margin-top: 0;
      }
      .icon{
        display: inline-block;
        width: 32px;
        height: 32px;
        vertical-align: middle;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center center;
      }
      [data-customtooltip] {
        position: relative;
        cursor: help;
      }
      [data-customtooltip].hideTooltips{
        cursor: pointer!important;
      }
      [data-customtooltip].hideTooltips::after{
        display: none!important;
      }
      [data-customtooltip]::after{
        position: absolute;
        opacity: 0;
        pointer-events: none;
        content: attr(data-customtooltip);
        left: 50%;
        top: calc(100% - 5px);
        background-color: #206a;
        color: #af4;
        border-radius: 8px;
        z-index: 10000;
        padding: 8px;
        font-size: 16px;
        line-height: 1em;
        max-width: 250px;
        min-width: 150px;
        transform: translate(-50%, -30px);
        transition: all 150ms cubic-bezier(.25, .8, .25, 1);
      }
      [data-customtooltip]:hover::after{
        opacity: 1;
        transform: translate(-50%, 0);
        transition-duration: 300ms;
      }
      .centeredPlist{
        top: calc(50% - 70px);
        position: absolute;
        transform: translate(-50%, -50%);
        left: 50%;
        width: 400px;
      }
      .projectDetailsTable{
        border-collapse: collapse;
        font-size: 16px;
        width: 100%;
        border-radius: 10px;
        background: #000;
      }
      .toolbar{
        top: 36px;
        width: 100%;
        margin-top: -3px;
        position: fixed;
        font-size: 16px;
        padding-top: 2px;
        background: #111;
        text-align: left;
      }
      .enabledButton{
        background: #4f8!important;
        color: #021;
      }
      .disabledButton{
        background: #333!important;
        text-shadow: 0 0 8px #fff!important;
        color: #111;
      }
      .navButtons{
        text-shadow: 0 0 8px #4f8;
        border-radius: 10px;
        background: #222;
        width: 50px;
        height: 26px;
        margin: 5px;
        line-height: 0;
        padding: 0;
        font-size: 20px;
        border: 1px solid #fff1;
        margin-left: 0;
        margin-right: 0;
      }
      #pageNo{
        font-size: 13px;
        min-width: 90px;
        display: inline-block;
        vertical-align: sub;
        
      }
      .textInput:focus{
        outline: none;
      }
      .headerTitle{
        margin-top: 2px;
        background: #4f84;
        padding-left: 5px;
        padding-right: 5px;
        border-radius: 0px;
        font-size: 20px;
      }
      .projectMenuItem{
        padding: 2px;
        font-size: 0;
        background: #333;
        border-radius: 8px;
        display: inline-block;
        margin: 10px;
        vertical-align: middle;
      }
      .highlight{
        color: #000;
        background: #af6;
      }
      .projectAvatar{
        width: 75px;
        height: 75px;
        border-radius: 50%;
        border: none;
        background-color: #222;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center center;
      }
      .projectDetailLabel{
        border-bottom: 1px solid #8884;
        border-right: 1px solid #8884;
        color: #48f;
        padding-right: 3px;
        text-align: right;
      }
      .projectDetailItem{
        border-bottom: 1px solid #8884;
        color: #f84;
        padding-left: 3px;
        text-align: left;
      }
      .projectTools{
        height: 100px;
        width: 60px;
        background: #000;
        margin: 3px;
        display: inline-block;
        vertical-align: top;
        border-radius: 20px;
      }
      .userProjectCluster{
        display: inline-block;
        vertical-align: top;
        margin: 5px;
      }
      .userName{
        font-size: 16px;
        color: #f00;
      }
      .projectButton{
        width: 200px;
        border: none;
        border-radius: 16px;
        margin: 5px;
        font-size: 20px;
        font-family: monospace, verdana;
        padding: 3px;
        background-color: #111;
        background-image: url(doc.png);
        background-repeat: no-repeat;
        background-size: contain;
        background-position: center center;
        color: #fff;
        text-shadow: 0px 0px 8px #0ff;
        min-height: 100px;
        font-weight: 900;
      }
      .deleteButton{
        background-color: #211;
      }
      .editButton{
        background-color: #132;
      }
      #copyConfirmation{
        display: none;
        position: absolute;
        width: 100vw;
        height: 100vh;
        top: 0;
        left: 0;
        background: #012d;
        color: #8ff;
        opacity: 1;
        text-shadow: 0 0 5px #fff;
        font-size: 46px;
        text-align: center;
        z-index: 10000;
      }
      #innerCopied{
        position: absolute;
        top: 50%;
        width: 100%;
        z-index: 1020;
        text-align: center;
        transform: translate(0, -50%) scale(2.0, 1);
      }
      .copyCodeAppendage{
        display: inline-block;
        vertical-align: top;
      }
      .copyButton{
        background-image: url(clippy.svg);
        background-color: transparent;
        margin-left: -25px!important;
      }
      .toolButton{
        background-repeat: no-repeat;
        background-position: center center;
        background-size: 35px 35px;
        border: none;
        width: 40px;
        height: 40px;
        margin: 5px;
        border-radius: 10px;
      }
      .loginInput{
        float: right;
        text-align: left!important;
        min-width: 300px;
      }
      .passwordChangeInput{
        float: right;
        text-align: left!important;
        min-width: 200px;
      }
      .textInput{
        font-size: 16px;
        text-align: center;
        background: #004;
        color: #4f8;
        /* border-radius: 5px; */
        border: none;
        padding-left: 5px;
        padding-right: 5px;
        vertical-align: middle;
        display: inline-block;
        border-bottom: 1px solid #00f;
      }
      blockquote{
        border-left: 5px solid #888;
        margin: 1.5em 10px;
        padding: .5em 10px;
      }
      .authButtons{
        border-radius: 5px;
        background: #0f8;
        color: #000;
        min-width: 75px;
        border: none;
        font-family: monospace, verdana;
        font-size: 16px;
        margin: 5px;
      }
      .projectList{
        text-align: center;
        overflow-y: hidden;
        overflow-x: hidden;
      }
      a{
        color: #f80;
        display: inline-block;
      }
      a:visited{
        color: #804;
      }
      #updatePasswordError{
        display: none;
        position: absolute;
        width: 300px;
      }
      #overlay{
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        overflow: hidden;
        background: #001d;
        z-index: 1000;
      }
      #loginInner, #regInner, #prefs{
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border-radius: 5px;
        border: 1px solid #4f82;
        width: 500px;
        height: 250px;
        background: #103;
        text-align: center;
        padding: 10px;
        color: #fff;
      }
      .loginLabel{
        color: #888;
        font-size: 16px;
      }
      .loginSection{
        width: 400px;
        display: inline-block;
      }
      .closeButton{
        border: none;
        border-radius: 5px;
        width: 25px;
        height: 18px;
        font-size: 16px;
        background: #f88;
        color: #400;
        float: right;
      }
      #textEditor:focus{
        outline: none;
      }
      #textEditor{
        resize: none;
        text-align: left;
        border: none;
        padding: 0;
        margin: 0;
        word-break: break-word;
        background: #333;
        width: calc(100vw - 20px);
        height: calc(100vh - 140px);
        padding: 10px;
        display: block; 
        color: #ffe;
      }
      .loggedinName{
        height: 20px;
        min-width: 100px;
        font-size: 14px;
        color: #fff;
        background: #408;
        border-radius: 5px;
        margin: 5px;
        border: 1px solid #4f84;
        display: inline-block;
        vertical-align: middle;
      }
      .avatar{
        width: 40px;
        height: 30px;
        background-position: center center;
        background-size: cover;
        background-color: #000;
        background-repeat: no-repeat;
        border-radius: 10px;
        display: inline-block;
        vertical-align: middle;
      }
      #newDocButton{
        background: #fa4;
        color: #210;
      }
      .vSpc{
        width 2px;
        height: 36px;
        display: inline-block;
        background: #111;
        vertical-align: middle;
        width: 2px;
      }
      #regUsernameError{
        display: none;
        font-size: 20px;
        position: absolute;
        width: 100%;
      }
      .nameAvailable{
        color: #4f48;
      }
      .nameTaken{
        color: #f448;
      }
      #loginError, #regError{
        position: absolute;
        width: 100%;
        display: none;
        color: #f44;
        font-size: 20px;
      }
      button{
        cursor: pointer;
      }
      button:focus{
        outline: none;
      }
      .modalButtons{
        min-width: 75px;
        font-size: 16px;
      }

      .checkmarkContainer {
        display: inline-block;
        position: relative;
        padding-left: 35px;
        vertical-align: middle;
        line-height: 24px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        margin-top: 4px;
      }

      .checkmarkContainer input {
        position: absolute;
        opacity: 0;
        height: 0;
        width: 0;
      }

      .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: #021;
        border-radius: 5px;
        border: 1px solid #042;
      }
      
      #mainAvatar{
        display: inline-block;
        float: left;
        width: 200px;
        height: 200px;
        border: 1px solid #fff1;
        border-radius: 25%;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center center;
      }

      .checkmarkContainer:hover input ~ .checkmark {
        background-color: #444;
      }

      .checkmarkContainer input:checked ~ .checkmark {
        background-color: #200;
        border: 1px solid #400;
      }

      .checkmark:after {
        content: "";
        position: absolute;
        display: none;
      }

      .checkmarkContainer input:checked ~ .checkmark:after {
        display: block;
      }

      .checkmarkContainer .checkmark:after {
        left: 9px;
        top: 5px;
        width: 5px;
        height: 10px;
        border: solid #f00;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
      }
      
    </style>
    <link rel="stylesheet" href="./highlight.js/violet.min.css">
    <script src="./highlight.js/highlight.min.js"></script>
  </head>
  <body>
    <div id="copyConfirmation"><div id="innerCopied">COPIED!</div></div>
    <div id="overlay">
      <div id="prefs">
        <button class="closeButton" data-customtooltip="close" onclick="closePrompts()">
          X
        </button>
        <div id="mainAvatar">
        </div>
        <div style="float: left; display: inline-block; width: 270px; line-height; 19px;">
          <div>change password</div><br>
          <input
            maxlength="128"
            type="password"
            id="oldPassword"
            style="font-size: 12px;"
            spellcheck="false"
            placeholder="old password"
            class="textInput newPasswordInput"
          /><br><br>
          <input
            maxlength="128"
            type="password"
            id="newPassword"
            style="font-size: 12px;"
            onkeyup="updatePasswordInput(event)"
            spellcheck="false"
            placeholder="new password"
            class="textInput newPasswordInput"
          /><br><br>
          <input
            maxlength="128"
            type="password"
            id="confirmNewPassword"
            onkeyup="updatePasswordInput(event)"
            style="font-size: 12px;"
            spellcheck="false"
            placeholder="confirm new password"
            class="textInput newPasswordInput"
          /><br>
          <div id="updatePasswordError"></div>
          <br><br>
          <button
            id="updatePasswordButton"
            class="modalButtons navButtons disabledButton"
            style="width: 166px"
            onclick="updatePassword()"
            data-customtooltip="login to an existing profile"
          >
            update password
          </button>
        </div>
        <div style="clear:both;"></div>
        <div style="text-align: left;font-size:14px;padding-left: 62px;">
          my avatar URL
        </div>
        <input
          maxlength="2048"
          id="avatarLink"
          style="width: calc(100% - 10px); font-size: 12px;"
          spellcheck="false"
          oninput="updateAvatar(this)"
          placeholder="enter avatar URL"
          class="textInput loginInput"
        />
      </div>
      <div id="loginInner">
        <button class="closeButton" data-customtooltip="close" onclick="closePrompts()">
          X
        </button>
        <br>login<br><br>
        <div class="loginSection">
          <label for="userName" class="loginLabel">
            user name
            <input
              maxlength="64"
              id="userName"
              spellcheck="false"
              onkeyup="loginInput(event)"
              placeholder="user name"
              class="textInput loginInput"
            />
          </label><br><br>
          <label for="password" class="loginLabel">
            password 
            <input
              maxlength="128"
              id="password"
              type="password"
              onkeyup="loginInput(event)"
              placeholder="password"
              class="textInput loginInput"
            />
          </label>
        </div><br>
        <div id="loginError">bad username or password</div>
        <br><br>
        <button
          id="loginButton"
          class="modalButtons navButtons disabledButton"
          onclick="login()"
          data-customtooltip="login to an existing profile"
        >
          login
        </button>
      </div>
      
      <!--        registration section        -->
      <div id="regInner">
        <button class="closeButton" data-customtooltip="close" onclick="closePrompts()">
          X
        </button>
        <br>register<br><br>
        <div class="loginSection">
          <label for="regUserName" class="loginLabel">
            user name
            <input
              maxlength="64"
              id="regUserName"
              spellcheck="false"
              onkeyup="regInput(event)"
              placeholder="user name"
              class="textInput loginInput"
            />
          </label><br>
          <div id="regUsernameError"></div>
          <br><br>
          <label for="regPassword" class="loginLabel">
            password 
            <input
              maxlength="128"
              id="regPassword"
              type="password"
              onkeyup="regInput(event)"
              placeholder="password"
              class="textInput loginInput"
            />
            <br><br>
            confirm 
            <input
              maxlength="128"
              id="regConfirmPassword"
              type="password"
              onkeyup="regInput(event)"
              placeholder="password"
              class="textInput loginInput"
            />
          </label>
        </div><br>
        <div id="regError"></div>
        <br>
        <button
          id="submitButton"
          class="modalButtons navButtons disabledButton"
          onclick="submitReg()"
          data-customtooltip="submit these credentials"
        >
          submit
        </button>
      </div>
      
      <!---------------------------------------->
      
    </div>
    <div class="header">
      <span
        class="headerTitle"
        data-customtooltip="go to home page"
        onclick="location.href=location.origin+location.pathname">
        coordocs, better docs
      </span>
      <div
        class="coordocsLogo"
        style="vertical-align: top; float: right; margin-right: 5px;"
        onclick="navToURL('https://github.com/srmcgann/coordocs')"
        data-customtooltip="visit system repository on Github.com"
       ></div>
    </div>
    <div class="toolbar">
      <div
        class="toolbarComponent"
        style="padding-top:7px; height:28px;"
        data-customtooltip="the title of the item(s) featured here"
      >
        <label for="screenName">
          <span class="loginLabel">this doc</span>
          <input
            maxlength="1024"
            oninput="updateProjectName(this.value)"
            id="screenName"
            class="textInput"
            placeholder="give this doc a name"
          ></input>
        </label>
      </div>
      <div class="toolbarComponent" style="display: none;">
        <button
          class="navButtons"
          id="editDocButton"
          data-customtooltip="edit doc"
          onclick="toggleEditMode('edit')"
        >edit</button>
        <button
          class="navButtons"
          id="viewDocButton"
          data-customtooltip="view contents as others will see it"
          onclick="toggleEditMode('view')"
        >view</button>
        <div class="vSpc"></div>
        <button
          class="navButtons newDocButton"
          id="newDocButton"
          data-customtooltip="create a new doc"
          onclick="createDoc()"
        >new</button>
      </div>
      <div
        class="toolbarComponent"
        style="display: none;"
        style="min-width: 100px; text-align: left;"
      >
        <label class="checkmarkContainer" data-customtooltip="toggle link visibility">
          <span id="privateCheckLabel">private</span>
          <input
            id="privacyCheck"
            type="checkbox"
            oninput="togglePrivate(this)"
          />
          <span class="checkmark"></span>
        </label>
      </div>
      <div class="toolbarComponent" style="display: none;">
        <button
          class="toolButton deleteButton"
          style="background-image: url(delete.png); background-size: 25px 25px; width: 30px; height: 30px;margin: 3px;"
          id="toolButton"
          data-customtooltip="delete this project?"
          onclick="deleteSingleProject()"
        ></button>
      </div>
      <div class="toolbarComponent">
        <button
          class="navButtons"
          id="page1Button"
          data-customtooltip="navigate to first page"
          onclick="navToPage('1')"
        >|&lt;</button>
        <button
          class="navButtons"
          id="pageBackButton"
          data-customtooltip="go back one page"
          onclick="navToPage('-1')"
        >&lt;</button>
        <span id="pageNo">0</span>
        <button
          class="navButtons"
          id="pageAdvButton"
          data-customtooltip="advance one page"
          onclick="navToPage('+1')"
        >&gt;</button>
        <button
          class="navButtons"
          id="pageLastButton"
          data-customtooltip="navigate to last page"
          onclick="navToPage('last')"
        >&gt;|</button>
      </div>
      <div
        class="toolbarComponent"
        style="padding-top:2px; height: 33px;"
        data-customtooltip="search your projects and public projects"
      >
        <label for="searchField">
          <div class="icon" style="background-image: url(search.png);"></div>
          <input
            maxlength="1024"
            type="text"
            class="textInput"
            id="searchField"
            autofocus
            placeholder="search for something"
            spellcheck="false"
            onkeyup="searchMaybe(event)"
          />
        </label>
      </div>
      <div class="toolbarComponent">
        <div id="loginContainer"></div>
      </div>
      <div
        class="toolbarComponent"
        id="tooltipsContainer"
        style="min-width: 132px; text-align: left;"
      >
        <label class="checkmarkContainer" data-customtooltip="toggle tooltips @ hover">
          <span id="tooltipCheckLabel">show tooltips</span>
          <input
            id="tooltipCheck"
            type="checkbox"
            checked
            oninput="toggleTooltips(this)"
          />
          <span class="checkmark"></span>
        </label>
      </div>
      <div
        class="toolbarComponent"
        id="removeHighlightingContainer"
        style="display: none;"
      >
        <button
          style="background: #8f4; min-width: 132px; font-size: 14px;"
          class="modalButtons navButtons"
          id="removeHighlightingButton"
          data-customtooltip="remove the highlighting from search"
          onclick="removeHighlighting()"
        >remove highlighting</button>
      </div>
      <div style="border-bottom: 1px solid #fff3;" id="mainDivider"></div>
      <div class="main"></div>
    </div>

    <script type="module">
      import * as MarkdownToHTML from "./md2html.js"
    
      var passhash
      var URLbase = location.pathname
      var main = document.querySelector('.main')
    
      var passhash   = ''
      var userID     = ''
      var userName   = ''
      var avatar     = ''
      var tooltips   = true
      var loggedin   = false

      window.navToURL = url => {
        var l = document.createElement('a')
        l.href = url
        l.target = '_blank'
        l.click()
      }

      const updateURL = (param, value, returnVal=false) => {
        var params = location.href.split('?')
        if(params.length > 1){
          params = params[1].split('&').filter(v=>{
            return v.toLowerCase().indexOf(param + '=') == -1
          }).join('&')
          params = '?'+ (value ? (param+'=' + value + (params ? '&' : '')) : '') + params
        }else{
          params = '?'+ (value ? (param+'=' + value) : '')
        }
        var newURL = location.href.split('?')[0] + params
        if(returnVal){
          return newURL
        }else{
          history.replaceState({}, document.title, newURL)
        }
      }

      window.LoadProject = (slug, page=1, edit=false) => {
        location.href = updateURL('p', page, false)
        if(edit) location.href = updateURL('e', 1, false)
        location.href = updateURL('s', slug, true)
      }
      
      window.searchMaybe = e => {
        var searchField = document.querySelector('#searchField')
        if(e.keyCode == 13) {
          toggleEditMode('view', true)
          slug = ''
          page = ''
          var search = searchField.value
          updateURL('h', encodeURIComponent(search))
          searchField.value = ''
          
          let sendData = { search, userID, passhash }
          var url = URLbase + 'search.php'
          fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(sendData),
          }).then(res => res.json()).then(data => {
            if(data.success){
              html = data.html
              
              updateURL('s', '')
              updateURL('p', '')
              setTimeout(() => {
                document.querySelector('#screenName').value = data.name
                curPageName = data.name
                Refresh(true)
              }, 0)
            }else{
              html = `<div 
                       style="position:relative; top:calc(50vh - 200px); width: 500px; left: 50%; transform: translate(-50%);"
                       class="projectList"
                       id="projList"
                      >
                       <div
                         class="coordocsLogo"
                         style="vertical-align: middle;"
                         onclick="navToURL('https://github.com/srmcgann/coordocs')"
                         data-customtooltip="visit system repository on Github.com"
                        ></div>
                        found no hits... sorry<br><br>
                      </div>`
              document.querySelector('#screenName').value = data.name
              curPageName = data.name
              Refresh()
            }

          })
        }
      }
      
      window.copyB64 = str => {
        str = atob(str)
        let copyEl = document.createElement('pre')
        copyEl.innerHTML = str
        copyEl.style.opacity = .01
        copyEl.style.position = 'absolute'
        document.body.appendChild(copyEl)
        var range = document.createRange()
        range.selectNode(copyEl)
        window.getSelection().removeAllRanges()
        window.getSelection().addRange(range)
        document.execCommand("copy")
        window.getSelection().removeAllRanges()
        copyEl.remove()
        let el = document.querySelector('#copyConfirmation')
        el.style.display = 'block';
        el.style.opacity = 1
        let reduceOpacity = () => {
          if(+el.style.opacity > 0){
            el.style.opacity -= .02 * 2
            if(+el.style.opacity<.1){
              el.style.opacity = 1
              el.style.display = 'none'
            }else{
              setTimeout(()=>{
                reduceOpacity()
              }, 10)
            }
          }
        }
        setTimeout(()=>{reduceOpacity()}, 250)
      }
      
      const SetCookie = (key, val) => {
        document.cookie = `${key}=${val}; expires=` + (new Date((new Date()).getTime() + 3600*24*365*1000).toUTCString())
      }
      
      const DelCookie = (key, val) => {
        document.cookie = `${key}=${val}; expires=` + (new Date(0).toUTCString())
      }
      
      const ModifyTooltipVisibility = () => {
        const maxDepth = 10
        const recurseElements = (el, depth=0) => {
          if(depth >= maxDepth) return
          if((typeof el.hasAttribute == 'function') &&
               el.hasAttribute('data-customtooltip')){
            el.classList[tooltips ? 'remove' : 'add']('hideTooltips')
            el.style.cursor = tooltips ? 'help!important' : 'pointer!important'
          }
          el.childNodes.forEach( node => recurseElements(node, depth+1) )
        }
        recurseElements(document.body)
      }

      const UpdateCookie = () => { 
        SetCookie('passhash', passhash)
        SetCookie('userName', userName)
        SetCookie('tooltips', tooltips)
        SetCookie('avatar', avatar)
      }

      const ClearCookie = () => { 
        DelCookie('passhash', passhash)
        DelCookie('userName', userName)
        DelCookie('tooltips', tooltips)
        DelCookie('avatar', avatar)
      }
      
      const SubmitLogin = (password, subPasshash='') => {
        let sendData = {
          userName,
          password,
          passhash: subPasshash
        }
        var url = URLbase + 'login.php'
        fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        }).then(res => res.json()).then(data => {
          if(data.success){
            passhash = data.passhash
            userID   = data.userID
            avatar   = data.avatar
            html     = data.data
            tooltips = !!(+data.tooltips)
            loggedin = true
            UpdateCookie()
            closePrompts()
          } else {
            loggedin = false
            passhash = ''
            tooltips = true
            userName = ''
            avatar   = ''
            if(password){
              document.querySelector('#loginError').style.display = 'block'
            }
          }
          UpdateLoginWidget()
          GetPageData()
          var checkbox = document.querySelector('#tooltipCheck')
          checkbox.checked = tooltips
          document.querySelector('#tooltipCheckLabel').innerHTML = checkbox.checked ? 'tooltips' : 'no tooltips'
        })
      }

      var uAvail = false
      const ValidateRegistration = pass => uAvail && pass.length >= 3

      window.updatePassword = () => {
        var newPassword = document.querySelector('#newPassword').value
        var oldPassword = document.querySelector('#oldPassword').value
        var confirmNewPassword = document.querySelector('#confirmNewPassword').value
        var newPasswordErrorEl = document.querySelector('#updatePasswordError')
        if(confirmNewPassword.length >= 3 && newPassword.length >= 3){
          if(confirmNewPassword == newPassword){
            let sendData = { userID, passhash, oldPassword, newPassword}
            console.log(sendData)
            var url = URLbase + 'updatePassword.php'
            fetch(url, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify(sendData),
            }).then(res => res.json()).then(data => {
              console.log(data)
              if(data.success){
                passhash = data.passhash
                UpdateCookie()
                alert('password updated successfully')
                closePrompts()
              }else{
                alert('there was an error updating the password!')
              }
            })
          }
        }
      }
      
      window.submitReg = () => {
        var regPassword = document.querySelector('#regPassword').value
        if(ValidateRegistration(regPassword)){
          var regUserName = document.querySelector('#regUserName').value
          let sendData = { regUserName, regPassword }
          var url = URLbase + 'register.php'
          fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(sendData),
          }).then(res => res.json()).then(data => {
            if(data.success){
              
              passhash = data.passhash
              userID   = data.userID
              avatar   = data.avatar
              html     = data.data
              tooltips = !!(+data.tooltips)
              loggedin = true
              UpdateCookie()
              closePrompts()
              
              document.querySelector('#userName').value = regUserName
              document.querySelector('#password').value = regPassword
              window.login()

            }else{
              alert('there was an error registering!')
            }
          })          
        }else{
          console.log('registration was not validated')
        }
      }
      
      window.regInput = e => {
        if(e.keyCode == 27){
          closePrompts()
        }
        var regButton = document.querySelector('#regButton')
        var userNameField = document.querySelector('#regUserName')
        var passwordField = document.querySelector('#regPassword')
        var confirmPasswordField = document.querySelector('#regConfirmPassword')
        uAvail = false
        if(userNameField.value){

          // check user name availability
          let sendData = { regUserName: userNameField.value }
          var url = URLbase + 'nameIsAvailable.php'
          fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(sendData),
          }).then(res => res.json()).then(data => {
            document.querySelector('#regUsernameError').style.display = 'block'
            document.querySelector('#regUsernameError').innerHTML = data
          })
        }else{
          document.querySelector('#regUsernameError').style.display = 'none'
        }
        if(userNameField.value &&
           passwordField.value &&
           confirmPasswordField.value){
          uAvail = true
        } else{
          regButton.className = 'modalButtons navButtons disabledButton'
        }
        if(passwordField.value){
          if(passwordField.value != confirmPasswordField.value){
            document.querySelector('#regError').style.display = 'block'
            document.querySelector('#regError').style.color = '#f448'
            document.querySelector('#regError').innerHTML = 'passwords don\'t match'
            regButton.className = 'modalButtons navButtons disabledButton'
          }else{
            document.querySelector('#regError').style.display = 'block'
            document.querySelector('#regError').style.color = '#4f48'
            document.querySelector('#regError').innerHTML = 'passwords match'
            if(userNameField.value && uAvail){
              regButton.className = 'modalButtons navButtons enabledButton'
              if(e.keyCode == 13){
                userName = userNameField.value
                window.submitReg()
              }
            }else{
              regButton.className = 'modalButtons navButtons disabledButton'
            }
          }
        }
      }

      window.updatePasswordInput = e => {
        updatePasswordButton.className = 'modalButtons navButtons disabledButton'
        var newPassword = document.querySelector('#newPassword').value
        var confirmNewPassword = document.querySelector('#confirmNewPassword').value
        var newPasswordErrorEl = document.querySelector('#updatePasswordError')
        if(confirmNewPassword.length >= 3 && newPassword.length >= 3){
          newPasswordErrorEl.style.display = 'block'
          if(confirmNewPassword == newPassword){
            newPasswordErrorEl.style.color = '#4f48'
            newPasswordErrorEl.innerHTML = 'passwords match'
            updatePasswordButton.className = 'modalButtons navButtons enabledButton'
            if(e.keyCode == 13){
              updatePassword()
            }
          }else{
            newPasswordErrorEl.style.color = '#f448'
            newPasswordErrorEl.innerHTML = 'passwords don\'t match'
            console.log("passwords don't match")
          }
        }else{
          newPasswordErrorEl.style.display = 'none'
        }
      }

      window.login = () => {
        var passwordField = document.querySelector('#password')
        SubmitLogin(passwordField.value)
      }
      
      window.loginInput = e => {
        if(e.keyCode == 27){
          closePrompts()
        }
        var loginButton = document.querySelector('#loginButton')
        var userNameField = document.querySelector('#userName')
        var passwordField = document.querySelector('#password')
        if(userNameField.value && passwordField.value){
          loginButton.className = 'modalButtons navButtons enabledButton'
          if(e.keyCode == 13){
            userName = userNameField.value
            SubmitLogin(passwordField.value)
          }
        } else{
          loginButton.className = 'modalButtons navButtons disabledButton'
        }
        document.querySelector('#loginError').style.display = 'none'
      }
      
      window.Logout = () => {
        ClearCookie()
        location.reload()
      }

      window.closePrompts = () => {
        document.querySelector('#overlay').style.display = 'none'
        document.querySelector('#overlay').childNodes.forEach(node=>{
            if(typeof node.style != 'undefined') node.style.display = 'none'
        })
      }
      
      window.showPrompt = screen => {
        document.querySelector('#overlay').style.display = 'block'
        switch(screen){
          case 'login':
            document.querySelector('#loginInner').style.display = 'block'
            document.querySelector('#userName').focus()
          break
          case 'register':
            document.querySelector('#regInner').style.display = 'block'
            document.querySelector('#regUserName').focus()
          break
          case 'prefs':
            document.querySelector('#prefs').style.display = 'block'
            document.querySelector('#avatarLink').value = avatar
            document.querySelector('#avatarLink').focus()
          break
        }
      }

      const UpdateLoginWidget = () => {
        var loginEl = document.querySelector('#loginContainer')
        loginEl.innerHTML = ''
        if(loggedin){
          var loggedInNameEl = document.createElement('div')
          loggedInNameEl.className = 'loggedinName'
          loggedInNameEl.innerHTML = userName
          loggedInNameEl.setAttribute('data-customtooltip', 'go to my projects')
          loggedInNameEl.onclick = () => location.href = 
                location.href=location.origin+location.pathname
                
          loginEl.appendChild(loggedInNameEl)
          var avatarEl = document.createElement('div')
          avatarEl.setAttribute('data-customtooltip', 'magage your preferences')
          document.querySelector('#mainAvatar').style=`background-image: url(${avatar})`
          avatarEl.onclick = () => showPrompt('prefs')
          avatarEl.className = 'avatar'
          avatar
          avatarEl.style.backgroundImage = `url(${avatar})`
          loginEl.appendChild(avatarEl)
          var logoutButton = document.createElement('button')
          logoutButton.setAttribute('data-customtooltip', 'logout')
          logoutButton.className = 'authButtons'
          logoutButton.onclick = window.Logout
          logoutButton.innerHTML = 'logout'
          loginEl.appendChild(logoutButton)
        }else{
          var loginButton = document.createElement('button')
          loginButton.className = 'authButtons'
          loginButton.onclick = () => showPrompt('login')
          loginButton.innerHTML = 'login'
          loginButton.setAttribute('data-customtooltip', 'login to an existing profile')
          loginEl.appendChild(loginButton)
          var registerButton = document.createElement('button')
          registerButton.className = 'authButtons'
          registerButton.id = 'regButton'
          registerButton.setAttribute('data-customtooltip', 'create a new profile')
          registerButton.onclick = () => showPrompt('register')
          registerButton.innerHTML = 'register'
          loginEl.appendChild(registerButton)
        }
      }
      
      const UpdateNavWidget = () => {
        document.querySelectorAll('.main')[0].scroll(0,0)
        document.querySelector('#pageNo').innerHTML = `page<br>${CurPage()} of ${totalPages()+1}`
        
        document.querySelector('#page1Button').style.background = CurPage() > 1 ? 
                                                 '#142' : '#333'
        document.querySelector('#page1Button').style.color = CurPage() > 1 ? 
                                                 '#4f8' : '#111'
        document.querySelector('#page1Button').style.textShadow = CurPage() > 1 ? 
                                                 '0 0 8px #4f8' : '0 0 8px #fff'
        
        document.querySelector('#pageBackButton').style.background = CurPage() > 1 ? 
                                                 '#142' : '#333'
        document.querySelector('#pageBackButton').style.color = CurPage() > 1 ? 
                                                 '#4f8' : '#111'
        document.querySelector('#pageBackButton').style.textShadow = CurPage() > 1 ? 
                                                 '0 0 8px #4f8' : '0 0 8px #fff'
        
        
        document.querySelector('#pageAdvButton').style.background = CurPage() < totalPages() + 1 ? '#142' : '#333'
        document.querySelector('#pageAdvButton').style.color = CurPage() < totalPages() + 1 ? '#4f8' : '#111'
        document.querySelector('#pageAdvButton').style.textShadow = CurPage() < totalPages() + 1 ? '0 0 8px #4f8' : '0 0 8px #fff'
        
        document.querySelector('#pageLastButton').style.background = CurPage() < totalPages() + 1 ? '#142' : '#333'
        document.querySelector('#pageLastButton').style.color = CurPage() < totalPages() + 1 ? '#4f8' : '#111'
        document.querySelector('#pageLastButton').style.textShadow = CurPage() < totalPages() + 1 ? '0 0 8px #4f8' : '0 0 8px #fff'

        document.querySelector('#pageNo').parentNode.style.display = totalPages() > 0 ? 'inline-block' : 'none'
      }

      window.deleteSingleProject = () => {
        deleteProject(slug, curPageName)
      }
      
      window.editProject = (slug, name) => LoadProject(slug, page=1, true)
      
      window.deleteProject = (slug, name) => {
        var response = prompt('delete this project? -> ' + name + "\n>>> THIS IS IRREVERSIBLE <<<\ntype 'yes' to confirm")
        if(response == 'yes'){
          let sendData = { slug, userID, passhash }
          var url = URLbase + 'deleteProject.php'
          fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(sendData),
          }).then(res => res.json()).then(data => {
            //html = data.data
            updateURL('s', '')
            updateURL('p', '')
            location.reload()
          })
        }
      }
      
      const ShowWelcomeScreen = () => {
        //var rb = document.querySelector('#regButton').cloneNode(true)
        html = `<div 
                 style="position:relative; top:calc(50vh - 200px); width: 500px; left: 50%; transform: translate(-50%);"
                 class="projectList"
                 id="projList"
                >
                 <div
                   class="coordocsLogo"
                   style="vertical-align: middle;"
                   onclick="navToURL('https://github.com/srmcgann/coordocs')"
                   data-customtooltip="visit system repository on Github.com"
                  ></div>
                  is a markdown prettifier, with pages<br><br>
                  <ol style="text-align: left;">
                    <li>pick a user name</li>
                    <li>create a doc</li>
                    <li>paste GitHub style markdown, e.g. README.md contents</li>
                    <li>add &lt;pagebreak/> tags to split it</li>
                  </ol>
                </div>`
        setTimeout(()=>{

          var exLink = document.createElement('a')
          exLink.href = './?s=example'
          exLink.target = '_blank'
          exLink.innerHTML = 'click here for an example doc'
          document.querySelector('#projList').innerHTML += '<br><br><br>'
          document.querySelector('#projList').appendChild(exLink)
        }, 0)

      }
      
      window.navToPage = pg => {
        var tgt
        switch(pg){
          case '1':
            if(CurPage() == 1) return
            tgt = 1
          break
          case '+1': tgt = Math.min(totalPages() + 1, CurPage() + 1); break
          case '-1':
            if(CurPage() == 1) return
            tgt = Math.max(1, CurPage() - 1);
          break
          case 'last': tgt = totalPages() + 1; break
        }
        updateURL('p', tgt, false)
        Refresh()
      }
      
      const CurPage = () => {
        var ret
        var l = location.href.split('?')
        if(l.length > 1){
          l = l[1].split('p=')
          if(l.length > 1){
            return +(l[1].split('&')[0])
          }else{
            return 0
          }
        }else{
          return 0
        }
      }
      
      const GetURLParam = param => {
        var ret = ''
        var params = location.href.split('?')
        if(params.length > 1){
          params[1].split('&').forEach(prm => {
            var pair = prm.split('=')
            if(pair[0] == param) ret = pair[1]
          })
        }
        return ret
      }

      var mainEl          = document.querySelectorAll('.main')[0]
      var slug            = GetURLParam('s')
      var page            = GetURLParam('p')
      var highlight       = GetURLParam('h')
      var viewMode        = GetURLParam('e') ? 'edit' : 'view'
      var projectUserName = ''
      var html            = ''
      var curPageName = ''
      var totalPages      = () => GetURLParam('s') ? html.split('&lt;pagebreak').length - 1 : 0
      
      const GetPageData = () => {
        projectUserName= ''
        let sendData = { passhash, userID, slug, page }
        var url = URLbase + 'getPageData.php'
        fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        }).then(res => res.json()).then(data => {
          
          if(data.success) {
            if(loggedin){
              html = data.data
            }else{
              if(!slug){
                ShowWelcomeScreen()
              }else{
                html = data.data
              }
            }
          }else{
            updateURL('s', '')
            ShowWelcomeScreen()
          }
          document.querySelector('#screenName').value = data.name
          curPageName = data.name

          projectUserName = data.userName
          document.querySelector('#tooltipsContainer').style.display = loggedin ? 'inline-block' : 'none'
          var checkbox = document.querySelector('#privacyCheck')
          checkbox.checked = !!(+data.private)
          document.querySelector('#privateCheckLabel').innerHTML = checkbox.checked ? 'private' : 'public'

          Refresh(data.success)
        })
      }
      
      
      window.toggleEditMode = (mode, forceReload=false, fromNewButton=false) => {
        if(mode == 'view') updateURL('e', '')
        setTimeout(()=>{ rsz() }, 0)
        if(slug && projectUserName == userName){
          switch(mode){
            case 'edit':
              document.querySelector('#editDocButton').style.background = '#2f4'
              document.querySelector('#viewDocButton').style.background = '#252'
              document.querySelector('#editDocButton').style.color = '#021'
              document.querySelector('#viewDocButton').style.color = '#2f8'
              document.querySelector('#editDocButton').style.boxShadow = '0 0 3px #40f'
              document.querySelector('#editDocButton').style.textShadow = '0 0 8px #4f8'
              document.querySelector('#viewDocButton').style.boxShadow = 'none'
              document.querySelector('#viewDocButton').style.textShadow = '0 0 8px #fff'
            break
            case 'view':
              document.querySelector('#editDocButton').style.background = '#252'
              document.querySelector('#viewDocButton').style.background = '#2f4'
              document.querySelector('#editDocButton').style.color = '#2f8'
              document.querySelector('#viewDocButton').style.color = '#021'
              document.querySelector('#editDocButton').style.boxShadow = 'none'
              document.querySelector('#editDocButton').style.textShadow = '0 0 8px #fff'
              document.querySelector('#viewDocButton').style.boxShadow = '0 0 3px #40f'
              document.querySelector('#viewDocButton').style.textShadow = '0 0 8px #4f8'
            break
          }
          if(viewMode != mode || forceReload){
            if(viewMode == 'edit'){
              if(!fromNewButton) {
                location.reload()
              }
            }
            viewMode = mode
            DisplayContent()
          }
          if(viewMode == 'view') UpdateNavWidget()
            document.querySelector('#privacyCheck').parentNode.parentNode.style.display='inline-block'
        }else{
          document.querySelector('#editDocButton').style.background = '#333'
          document.querySelector('#viewDocButton').style.background = '#333'
          document.querySelector('#editDocButton').style.color = '#111'
          document.querySelector('#viewDocButton').style.color = '#111'
          document.querySelector('#editDocButton').style.boxShadow = 'none'
          document.querySelector('#viewDocButton').style.boxShadow = 'none'
          document.querySelector('#editDocButton').style.textShadow = '0 0 8px #fff'
          document.querySelector('#viewDocButton').style.textShadow = '0 0 8px #fff'
          document.querySelector('#privacyCheck').parentNode.parentNode.style.display='none'
        }
        document.querySelector('.main').style.textAlign = slug ? 'left' : 'center'
      }
      
      window.createDoc = () => {
        let sendData = { userID, passhash }
        var url = URLbase + 'createProject.php'
        fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        }).then(res => res.json()).then(data => {
          if(!data.success) {
            updateURL('s', '')
          }else{
            projectUserName = userName
          }
          
          slug = data.slug
          html = data.data

          document.querySelector('#screenName').value = data.name
          curPageName = data.name
          
          var checkbox = document.querySelector('#privacyCheck')
          checkbox.parentNode.parentNode.style.display = 'inline-block'
          document.querySelector('#toolButton').parentNode.style.display = 'inline-block'
          checkbox.checked = !!(+data.private)
          document.querySelector('#privateCheckLabel').innerHTML = checkbox.checked ? 'private' : 'public'
          
          Refresh(data.success)
          window.toggleEditMode(viewMode = 'edit', true, true)
          updateURL('s', slug)
          updateURL('p', 1)
        })
        
      }
      
      window.togglePrivate = checkbox => {
        let sendData = { slug, userID, passhash, private: checkbox.checked }
        var url = URLbase + 'updatePrivacy.php'
        fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        }).then(res => res.json()).then(data => {
          if(!data.success) checkbox.checked = !checkbox.checked
          document.querySelector('#privateCheckLabel').innerHTML = checkbox.checked ? 'private' : 'public'
        })
      }
      
      window.removeHighlighting= checkbox => {
        updateURL('h', '')
        location.reload()
      }
      
      window.toggleTooltips = checkbox => {
        let sendData = { slug, userID, passhash, tooltips: checkbox.checked }
        var url = URLbase + 'updateTooltips.php'
        fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        }).then(res => res.json()).then(data => {
          if(!data.success) checkbox.checked = !checkbox.checked
          document.querySelector('#tooltipCheckLabel').innerHTML = checkbox.checked ? 'tooltips' : 'no tooltips'
          tooltips = checkbox.checked
          SetCookie('tooltips', tooltips)
          ModifyTooltipVisibility()
        })
      }
      
      window.updateAvatar = input => {
        var avatarURL = input.value
        if(avatarURL){
          let sendData = {userID, passhash, avatar: avatarURL}
          var url = URLbase + 'updateAvatar.php'
          fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(sendData),
          }).then(res => res.json()).then(data => {
            if(data.success){
              avatar = avatarURL
              document.querySelector('#mainAvatar').style=`background-image: url(${avatar})`
              document.querySelectorAll('.projectAvatar').forEach(el=>{
                el.style=`background-image: url(${avatar})`
              })
              document.querySelectorAll('.avatar').forEach(el=>{
                el.style=`background-image: url(${avatar})`
              })
            }
          })
        }
      }
      
      window.updateProjectName = name => {
        if(slug && projectUserName == userName){
          let sendData = {slug, name, userID, passhash}
          var url = URLbase + 'updateProjectName.php'
          fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(sendData),
          }).then(res => res.json()).then(data => {
            if(data.success){
              curPageName = name
              document.title = document.title.split(' - ').map((v, i)=>i?v:name).join(' - ')
            }else{
              document.querySelector('#screenName').value = curPageName
            }
          })
        }else{
          document.querySelector('#screenName').value = curPageName
        }
      }
      
      const CheckLogin = () => {
        var cookieParts = document.cookie.split('; ')
        cookieParts.forEach(part => {
          var pair = part.split('=')
          switch(pair[0]){
            case 'passhash': passhash = pair[1]; break
            case 'userName': userName = pair[1]; break
            case 'tooltips': tooltips = !!eval(pair[1]); break
            case 'userID': userID = pair[1]; break
            case 'avatar': avatar = pair[1]; break
          }
        })
        SubmitLogin('', passhash)
      }
      
      const HandleTextInput = textEditor => {
        
        html = textEditor.value
        
        let sendData = { slug, userID, passhash, data: html }
        var url = URLbase + 'updateProjectData.php'
        fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        }).then(res => res.json()).then(data => {
          if(!data.success) console.error(`error`, data.error)
        })
      }
      
      const DisplayContent = (success=true) => {
        if(slug){
          if(viewMode == 'edit'){
            var textEditor = document.createElement('textarea')
            textEditor.innerHTML = html
            textEditor.style.fontSize = '16px'
            textEditor.id = 'textEditor'
            textEditor.tabIndex = 0
            textEditor.spellcheck = false
            textEditor.focus()
            textEditor.oninput = () => HandleTextInput(textEditor)
            mainEl.style.padding = '0'
            mainEl.style.maxHeight = 'calc(100vh - 15px)';
            mainEl.innerHTML = ''
            mainEl.appendChild(textEditor)
            document.querySelector('#pageNo').parentNode.style.display = 'none'
          }else{
            var teEl = document.querySelector('#textEditor')
            if(teEl) teEl.remove()
            var dh = ''
            if(highlight) {
              dh = decodeURIComponent(highlight)
              document.querySelector('#removeHighlightingContainer').style.display = 'inline-block'
            }
            
            var htmlObj = success ? 
              MarkdownToHTML.Convert(html, CurPage(),
                document.querySelector('#pageNo').parentNode, mainEl) :
                { html, totalPages: totalPages() }
            
            // highlight search string maybe
            var hHtml
            if(highlight){
              var s = '', s2, ct = 0
              var splt = htmlObj.html.toLowerCase().split(dh.toLowerCase())
              splt.forEach((v, i) => {
                for(var j = 0; j < v.length; ++j){
                  s += htmlObj.html[ct]
                  ct++
                }
                if(i < splt.length - 1){
                  s2 = ''
                  for(var j = 0; j < dh.length; ++j){
                    s2 += htmlObj.html[ct]
                    ct++
                  }
                  s += (s.substr(s.length-20).toLowerCase().indexOf('src=') == -1 &&
                        s.substr(s.length-20).toLowerCase().indexOf('href=') == -1) ?
                         `<font class="coordocsSearchResult" class="highlight">${s2}</font>` : s2
                }
              })
              hHtml = s.replaceAll("\n", "<br>\n")
            }else{
              hHtml = htmlObj.html
            }
            
            mainEl.innerHTML = hHtml
            mainEl.style.padding = '10px'
            mainEl.style.maxHeight = 'calc(100vh - 215px)';
            mainEl.style.paddingBottom = '100px'
            if(success) hljs.highlightAll()
            document.querySelector('#pageNo').parentNode.style.display = totalPages() > 1 ? 'inline-block' : 'none'
            if(highlight){
              setTimeout(()=>{
                document.querySelectorAll('.coordocsSearchResult')[0].scrollIntoView({
                  behavior: 'smooth', block: 'center',
                })
              }, 0)
            }
          }
        }else{
          mainEl.innerHTML = html
        }
      }
      
      const Refresh = (success = true) => {

        if(slug) {
          document.querySelector('#screenName').style.color = projectUserName == userName ? '#4f8' : '#aaa'
          if(loggedin){
            if(projectUserName == userName){
              document.querySelector('#toolButton').parentNode.style.display = 'inline-block'
            }
            document.querySelector('#newDocButton').parentNode.style.display = 'inline-block'
            document.querySelector('.checkmarkContainer').parentNode.style.display = 'inline-block'
          }
        }else{
          document.querySelector('#screenName').style.color = '#aaa'
          document.querySelector('.checkmarkContainer').parentNode.style.display = 'none'
          if(loggedin){
            document.querySelector('#newDocButton').parentNode.style.display = 'inline-block'
          }else{
            document.querySelector('#newDocButton').parentNode.style.display = 'none'
          }
        }
        
        if(CurPage() == 0) navToPage('+1')
        DisplayContent(success)
        if(CurPage() > totalPages()+1) {
          updateURL('p', totalPages()+1)
          Refresh()
        }
        if(CurPage() < 1) {
          updateURL('p', 1)
          Refresh()
        }
        UpdateNavWidget()
        toggleEditMode(viewMode)
        ModifyTooltipVisibility()
      }
      
      CheckLogin()
      
      // adjust main div to meet page bottom
      var rsz
      window.onresize = rsz = () => {
        var l = document.querySelector('#mainDivider').getBoundingClientRect().y
        var h = `${window.innerHeight-l-3}px`
        document.querySelectorAll('.main')[0].style.height = h
        if(document.querySelector('#textEditor')){
          document.querySelector('#textEditor').style.height = h
        }
      }
      setTimeout(() => { rsz() }, 1e3)
    </script>
  </body>
</html>







