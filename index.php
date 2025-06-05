<!--
  to do
  ✔ working login & project <-> user assoc
  ✔ meta / social data
  ✔ create-project form
  ✔ markdown editor tool w/ toggle
  ✔ docs page for docs
  ✔ page break syntax for markdown
  ✔ delete button on doc itself, plus list page
  * registration
  * search function
  * user profile settings screen, w/ avatar, password change, etc.
  * copy button @ all code blocks
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
    <meta charset="UTF-8">
    <title><?=$pageTitle?></title>
    <style>
      body, html{
        width: 100vw;
        min-height: 100vh;
        margin: 0;
        background: #080810;
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
      .main{
        text-align: left;
        height: calc(100vh - 100px);
        border: none;
        padding: 10px;
        padding-top: 0;
        overflow-y: auto;
        overflow-x: hidden;
        background: #222;
        color: #ffd;
        font-family: monospace, verdana;
      }
      .main img{
        display: block;
        /*border: 1px solid #fff4;*/
        border-radius: 5px;
      }
      .toolbarComponent{
        border-radius: 24px;
        background: #002;
        height: 35px;
        border: 1px solid #82f4;
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
      .toolbar{
        top: 36px;
        width: 100%;
        margin-top: 36px;
        font-size: 25px;
        padding: 2px;
        background: #000;
        text-align: left;
      }
      .enabledButton{
        background: #4f8!important;
        color: #021;
      }
      .disabledButton{
        background: #333!important;
        color: #111;
      }
      .navButtons{
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
        font-size: 12px;
        min-width: 90px;
        display: inline-block;
        vertical-align: middle;
      }
      .textInput:focus{
        outline: none;
      }
      .headerTitle{
        margin-top: 2px;
        cursor: pointer;
        background: #4f84;
        padding-left: 5px;
        padding-right: 5px;
        border-radius: 0px;
        font-size: 20px;
      }
      .projectMenuItem{
        padding: 2px;
        background: #333;
        display: inline-block;
        margin: 10px;
        vertical-align: middle;
      }
      .projectButton{
        width: 200px;
        border: 1px solid #4f84;
        border-radius: 2px;
        margin: 5px;
        font-size: 20px;
        font-family: monospace, verdana;
        padding: 3px;
        background: #208;
        color: #fff;
        text-shadow: 2px 2px 3px #000;
        min-height: 32px;
      }
      .deleteButton{
        background-image: url(delete.png);
        background-repeat: no-repeat;
        background-position: center center;
        background-size: contain;
        background-color: transparent;
        border: none;
        width: 30px;
        height: 30px;
        padding: 6px;
        border-radius: 10px;
      }
      .loginInput{
        float: right;
        text-align: left!important;
        min-width: 300px;
      }
      .textInput{
        font-size: 16px;
        text-align: center;
        background: #000;
        color: #4f8;
        border-radius: 5px;
        border: 1px solid #fff1;
        padding-left: 5px;
        padding-right: 5px;
        vertical-align: middle;
        display: inline-block;
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
        overflow-y: auto;
      }
      a{
        color: #f80;
        display: inline-block;
      }
      a:visited{
        color: #804;
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
      #loginInner{
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border-radius: 5px;
        border: 1px solid #4f82;
        width: 500px;
        height: 220px;
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
        cursor: pointer;
      }
      .avatar{
        width: 40px;
        height: 30px;
        background-position: center center;
        background-size: contain;
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
        background: #82f4;
        vertical-align: middle;
        width: 2px;
      }
      #loginError{
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
        cursor: pointer;
        vertical-align: middle;
        line-height: 24px;
        font-size: 22px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        margin-top: -2px;
      }

      .checkmarkContainer input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
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
    <div id="overlay">
      <div id="loginInner">
        <button class="closeButton" title="close" onclick="closePrompts()">
          X
        </button>
        <br>login<br><br>
        <div class="loginSection">
          <label for="userName" class="loginLabel">
            user name
            <input
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
              id="password"
              type="password"
              onkeyup="loginInput(event)"
              placeholder="password"
              class="textInput loginInput"
            />
          </label>
        </div><br>
        <div id="loginError">bad username or password</div>
        <br>
        <button
          id="loginButton"
          class="modalButtons navButtons disabledButton"
          onclick="login()"
          title="login to an existing profile"
        >
          login
        </button>
      </div>
    </div>
    <div class="header">
      <span
        class="headerTitle"
        onclick="location.href=location.origin+location.pathname">
        coordocs, better docs
      </span>
      <div
        class="coordocsLogo"
        style="vertical-align: top; float: right; margin-right: 5px;"
        onclick="navToURL('https://github.com/srmcgann/coordocs')"
        title="visit system repository on Github.com"
       ></div>
    </div>
    <div class="toolbar">
      <div class="toolbarComponent">
        <label for="screenName">
          <span class="loginLabel">this doc</span>
          <input
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
          title="edit doc"
          onclick="toggleEditMode('edit')"
        >edit</button>
        <button
          class="navButtons"
          id="viewDocButton"
          title="view contents as others will see it"
          onclick="toggleEditMode('view')"
        >view</button>
        <div class="vSpc"></div>
        <button
          class="navButtons newDocButton"
          id="newDocButton"
          title="create a new doc"
          onclick="createDoc()"
        >new</button>
      </div>
      <div class="toolbarComponent" style="display: none;">
        <label class="checkmarkContainer" title="toggle link visibility">
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
          class="deleteButton"
          id="deleteButton"
          title="delete this project?"
          onclick="deleteSingleProject()"
        ></button>
      </div>
      <div class="toolbarComponent">
        <button
          class="navButtons"
          id="page1Button"
          title="navigate to first page"
          onclick="navToPage('1')"
        >|&lt;</button>
        <button
          class="navButtons"
          id="pageBackButton"
          title="go back one page"
          onclick="navToPage('-1')"
        >&lt;</button>
        <span id="pageNo">0</span>
        <button
          class="navButtons"
          id="pageAdvButton"
          title="advance one page"
          onclick="navToPage('+1')"
        >&gt;</button>
        <button
          class="navButtons"
          id="pageLastButton"
          title="navigate to last page"
          onclick="navToPage('last')"
        >&gt;|</button>
      </div>
      <div class="toolbarComponent">
        <label for="searchField">
          <div class="icon" style="background-image: url(search.png);"></div>
          <input
            type="text"
            class="textInput"
            id="searchField"
            placeholder="search for something"
            spellcheck="false"
            onkeyup="searchMaybe(event)"
          />
        </label>
      </div>
      <div class="toolbarComponent">
        <div id="loginContainer"></div>
      </div>
    </div>

    <div class="main"></div>

    <script type="module">
      import * as MarkdownToHTML from "./md2html.js"
    
      var passhash
      var URLbase = '/coordocs'
      var main = document.querySelector('.main')
    
      var passhash   = ''
      var userID     = ''
      var userName   = ''
      var avatar     = ''
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

      window.LoadProject = slug => {
        location.href = updateURL('p', 1, false)
        location.href = updateURL('s', slug, true)
      }
      
      window.searchMaybe = e => {
        var searchField = document.querySelector('#searchField')
        if(e.keyCode == 13) {
          var search = searchField.value
          console.log('searching for: ', search)
          searchField.value = ''
          
          let sendData = { search, userID, passhash }
          var url = URLbase + '/search.php'
          fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(sendData),
          }).then(res => res.json()).then(data => {
            if(data.success){
              html = data.html
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
                         title="visit system repository on Github.com"
                        ></div>
                        found no hits... sorry<br><br>
                      </div>`
            }

            document.querySelector('#screenName').value = data.name
            curPageName = data.name

            projectUserName = data.userName
            
            var checkbox = document.querySelector('#privacyCheck')
            checkbox.checked = !!(+data.private)
            document.querySelector('#privateCheckLabel').innerHTML = checkbox.checked ? 'private' : 'public'

            Refresh(data.success)
          })
        }
      }
      
      
      const SetCookie = (key, val) => {
        document.cookie = `${key}=${val}; expires=` + (new Date((new Date()).getTime() + 3600*24*365*1000).toUTCString())
      }
      
      const DelCookie = (key, val) => {
        document.cookie = `${key}=${val}; expires=` + (new Date(0).toUTCString())
      }
      
      
      const UpdateCookie = () => { 
        SetCookie('passhash', passhash)
        SetCookie('userName', userName)
        SetCookie('avatar', avatar)
      }

      const ClearCookie = () => { 
        DelCookie('passhash', passhash)
        DelCookie('userName', userName)
        DelCookie('avatar', avatar)
      }
      
      const SubmitLogin = (password, subPasshash='') => {
        let sendData = {
          userName,
          password,
          passhash: subPasshash
        }
        var url = URLbase + '/login.php'
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
            loggedin = true
            UpdateCookie()
            closePrompts()
          } else {
            loggedin = false
            passhash = ''
            userName = ''
            avatar   = ''
            if(password){
              document.querySelector('#loginError').style.display = 'inline-block'
            }
          }
          UpdateLoginWidget()
          GetPageData()
        })
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
        }
      }

      window.Register = () => {
        console.log('registering...')
      }

      const UpdateLoginWidget = () => {
        var loginEl = document.querySelector('#loginContainer')
        loginEl.innerHTML = ''
        if(loggedin){
          var loggedInNameEl = document.createElement('div')
          loggedInNameEl.className = 'loggedinName'
          loggedInNameEl.innerHTML = userName
          loggedInNameEl.title = 'go to my projects'
          loggedInNameEl.onclick = () => location.href = 
                location.href=location.origin+location.pathname
                
          loginEl.appendChild(loggedInNameEl)
          var avatarEl = document.createElement('div')
          avatarEl.className = 'avatar'
          avatarEl.style.backgroundImage = `url(${avatar})`
          loginEl.appendChild(avatarEl)
          var logoutButton = document.createElement('button')
          logoutButton.title = 'logout'
          logoutButton.className = 'authButtons'
          logoutButton.onclick = window.Logout
          logoutButton.innerHTML = 'logout'
          loginEl.appendChild(logoutButton)
        }else{
          var loginButton = document.createElement('button')
          loginButton.className = 'authButtons'
          loginButton.onclick = () => showPrompt('login')
          loginButton.innerHTML = 'login'
          loginButton.title = 'login to an existing profile'
          loginEl.appendChild(loginButton)
          var registerButton = document.createElement('button')
          registerButton.className = 'authButtons'
          registerButton.id = 'regButton'
          registerButton.title = 'create a new profile'
          registerButton.onclick = window.Register
          registerButton.innerHTML = 'register'
          loginEl.appendChild(registerButton)
        }
      }
      
      const UpdateNavWidget = () => {
        document.querySelectorAll('.main')[0].scroll(0,0)
        document.querySelector('#pageNo').innerHTML = `page<br>${CurPage()} of ${totalPages()+1}`
        
        document.querySelector('#page1Button').style.background = CurPage() > 1 ? 
                                                 '#4f8' : '#333'
        document.querySelector('#page1Button').style.color = CurPage() > 1 ? 
                                                 '#032' : '#111'
        
        document.querySelector('#pageBackButton').style.background = CurPage() > 1 ? 
                                                 '#4f8' : '#333'
        document.querySelector('#pageBackButton').style.color = CurPage() > 1 ? 
                                                 '#032' : '#111'
        
        
        document.querySelector('#pageAdvButton').style.background = CurPage() < totalPages() + 1 ? '#4f8' : '#333'
        document.querySelector('#pageAdvButton').style.color = CurPage() < totalPages() + 1 ? '#032' : '#111'
        
        document.querySelector('#pageLastButton').style.background = CurPage() < totalPages() + 1 ? '#4f8' : '#333'
        document.querySelector('#pageLastButton').style.color = CurPage() < totalPages() + 1 ? '#032' : '#111'
        document.querySelector('#pageNo').parentNode.style.display = totalPages() > 0 ? 'inline-block' : 'none'
      }

      window.deleteSingleProject = () => {
        deleteProject(slug, curPageName)
      }
      
      window.deleteProject = (slug, name) => {
        var response = prompt('delete this project? -> ' + name + "\n>>> THIS IS IRREVERSIBLE <<<\ntype 'yes' to confirm")
        if(response == 'yes'){
          let sendData = { slug, userID, passhash }
          var url = URLbase + '/deleteProject.php'
          fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(sendData),
          }).then(res => res.json()).then(data => {
            html = data.data
            updateURL('s', '')
            updateURL('p', '')
            location.reload()
          })
        }
      }
      
      const ShowWelcomeScreen = () => {
        var rb = document.querySelector('#regButton').cloneNode(true)
        html = `<div 
                 style="position:relative; top:calc(50vh - 200px); width: 500px; left: 50%; transform: translate(-50%);"
                 class="projectList"
                 id="projList"
                >
                 <div
                   class="coordocsLogo"
                   style="vertical-align: middle;"
                   onclick="navToURL('https://github.com/srmcgann/coordocs')"
                   title="visit system repository on Github.com"
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
          rb.onclick = window.Register
          document.querySelector('#projList').appendChild(rb)
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
      var viewMode        = 'view'
      var projectUserName = ''
      var html            = ''
      var curPageName = ''
      var totalPages      = () => GetURLParam('s') ? html.split('&lt;pagebreak').length - 1 : 0
      
      const GetPageData = () => {
        projectUserName= ''
        let sendData = { passhash, userID, slug, page }
        var url = URLbase + '/getPageData.php'
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
          
          var checkbox = document.querySelector('#privacyCheck')
          checkbox.checked = !!(+data.private)
          document.querySelector('#privateCheckLabel').innerHTML = checkbox.checked ? 'private' : 'public'

          Refresh(data.success)
        })
      }
      
      
      window.toggleEditMode = (mode, forceReload=false, fromNewButton=false) => {
        if(slug && projectUserName == userName){
          switch(mode){
            case 'edit':
              document.querySelector('#editDocButton').style.background = '#2f4'
              document.querySelector('#viewDocButton').style.background = '#252'
              document.querySelector('#editDocButton').style.color = '#021'
              document.querySelector('#viewDocButton').style.color = '#2f8'
              document.querySelector('#editDocButton').style.boxShadow = '0 0 3px #40f'
              document.querySelector('#viewDocButton').style.boxShadow = 'none'
            break
            case 'view':
              document.querySelector('#editDocButton').style.background = '#252'
              document.querySelector('#viewDocButton').style.background = '#2f4'
              document.querySelector('#editDocButton').style.color = '#2f8'
              document.querySelector('#viewDocButton').style.color = '#021'
              document.querySelector('#editDocButton').style.boxShadow = 'none'
              document.querySelector('#viewDocButton').style.boxShadow = '0 0 3px #40f'
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
          document.querySelector('#privacyCheck').parentNode.parentNode.style.display='none'
        }
      }
      
      window.createDoc = () => {
        let sendData = { userID, passhash }
        var url = URLbase + '/createProject.php'
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
          document.querySelector('#deleteButton').parentNode.style.display = 'inline-block'
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
        var url = URLbase + '/updatePrivacy.php'
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
      
      window.updateProjectName = name => {
        if(slug && projectUserName == userName){
          let sendData = { slug, userID, passhash, name }
          var url = URLbase + '/updateProjectName.php'
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
            case 'userID': userID = pair[1]; break
            case 'avatar': avatar = pair[1]; break
          }
        })
        SubmitLogin('', passhash)
      }
      
      const HandleTextInput = textEditor => {
        
        html = textEditor.value
        
        let sendData = { slug, userID, passhash, data: html }
        var url = URLbase + '/updateProjectData.php'
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
            var htmlObj = success ? 
              MarkdownToHTML.Convert(html, CurPage(),
                document.querySelector('#pageNo').parentNode, mainEl) :
                { html, totalPages: totalPages() }
            mainEl.innerHTML = htmlObj.html
            mainEl.style.padding = '10px'
            mainEl.style.maxHeight = 'calc(100vh - 215px)';
            mainEl.style.paddingBottom = '100px'
            //totalPages() = htmlObj.totalPages ? htmlObj.totalPages : totalPages()
            if(success) hljs.highlightAll()
            document.querySelector('#pageNo').parentNode.style.display = totalPages() > 1 ? 'inline-block' : 'none'

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
              document.querySelector('#deleteButton').parentNode.style.display = 'inline-block'
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
      }
      
      CheckLogin()
      //window.toggleEditMode(viewMode)
    </script>
  </body>
</html>


