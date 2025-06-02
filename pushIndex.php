<?
$file = <<<'FILE'
<!--
  to do
  * search function
  * create project form
  * working login/reg & project <-> user assoc
  * docs page for docs
  * copy button @ all code blocks
  * markdown editor tool w/ toggle
-->
<?php
  //require_once('functions.php');
  //$pageData = PageData();
?>
<!DOCTYPE html>
<html>
  <head>
    <title>coordocs - better docs</title>
    <style>
      body, html{
        width: 100vw;
        min-height: 100vh;
        margin: 0;
        background: #080810;
        color: #edc;
        font-family: verdana;
        font-size: 16px;
        overflow: hidden;
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
        position: fixed;
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
        border: 1px solid #fff2;
        padding: 10px;
        overflow-y: auto;
        overflow-x: hidden;
        background: #123;
        max-height: calc(100vh - 98px);
        color: #cfe;
        font-family: verdana;
        background: #080810;
      }
      .main img{
        display: block;
        border: 1px solid #fff4;
        border-radius: 5px;
      }
      .toolbarComponent{
        border-radius: 5px;
        background: #002;
        height: 35px;
        border: 1px solid #fff1;
        display: inline-block;
        vertical-align: top;
        position: relative;
        text-align: center;
        padding-left: 10px;
        padding-right: 10px;
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
        background: #4f8;
        color: #021;
      }
      .disabledButton{
        background: #333;
        color: #111;
      }
      .navButtons{
        border-radius: 10px;
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
      }
      .projectButton{
        width: 200px;
        border: 1px solid #4f84;
        border-radius: 2px;
        margin: 5px;
        font-size: 20px;
        font-family: verdana;
        padding: 3px;
        background: #208;
        color: #fff;
        text-shadow: 2px 2px 3px #000;
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
        margin-left: 10px;
      }
      .loginInput{
        float: right;
      }
      .textInput{
        font-size: 16px;
        background: #000;
        color: #4f8;
        border-radius: 5px;
        border: 1px solid #fff1;
        width: 300px;
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
        font-family: verdana;
        font-size: 16px;
        margin: 5px;
      }
      .projectList{
        text-align: center;
      }
      a{
        color: #f08;
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
        height: 500px;
        background: #103;
        text-align: center;
        padding: 10px;
        color: #fff;
      }
      .loginLabel{
        color: #888;
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
        background-size: contain;
        background-color: #000;
        background-repeat: no-repeat;
        border-radius: 10px;
        display: inline-block;
        vertical-align: middle;
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
        <br>login / register<br><br><br>
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
        </div><br><br>
        <button
          id="loginButton"
          class="modalButtons navButtons disabledButton"
          onclick="login()"
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
        onclick="navToURL('https://github.com/srmcgann/coordocs')"
        title="visit system repository on Github.com"
       ></div>
    </div>
    <div class="toolbar">
      <div class="toolbarComponent">
        <span
          id="screenName"
          class="textInput"
        ></span>
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
        <input
          type="text"
          class="textInput"
          id="searchField"
          placeholder="search for something"
          spellcheck="false"
          onkeyup="searchMaybe(event)"
        >
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
          console.log('searching for: ', searchField.value)
          searchField.value = ''
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
        console.log('submitting login', sendData)
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
            console.log('logged in successfully')
            UpdateCookie()
            closePrompts()
          } else {
            console.log('login failed', data)
            loggedin = false
            passhash = ''
            userName = ''
            avatar   = ''
          }
          UpdateLoginWidget()
          GetPageData()
        })
      }
      
      window.loginInput = e => {
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
          loginEl.appendChild(loggedInNameEl)
          var avatarEl = document.createElement('div')
          avatarEl.className = 'avatar'
          avatarEl.style.backgroundImage = `url(${avatar})`
          loginEl.appendChild(avatarEl)
          var logoutButton = document.createElement('button')
          logoutButton.className = 'authButtons'
          logoutButton.onclick = window.Logout
          logoutButton.innerHTML = 'logout'
          loginEl.appendChild(logoutButton)
        }else{
          var loginButton = document.createElement('button')
          loginButton.className = 'authButtons'
          loginButton.onclick = () => showPrompt('login')
          loginButton.innerHTML = 'login'
          loginEl.appendChild(loginButton)
          var registerButton = document.createElement('button')
          registerButton.className = 'authButtons'
          registerButton.onclick = window.Register
          registerButton.innerHTML = 'register'
          loginEl.appendChild(registerButton)
        }
      }
      
      const UpdateNavWidget = () => {
        document.querySelectorAll('.main')[0].scroll(0,0)
        document.querySelector('#pageNo').innerHTML = `page<br>${CurPage()} of ${totalPages+1}`
        
        document.querySelector('#page1Button').style.background = CurPage() > 1 ? 
                                                 '#4f8' : '#333'
        document.querySelector('#page1Button').style.color = CurPage() > 1 ? 
                                                 '#032' : '#111'
        
        document.querySelector('#pageBackButton').style.background = CurPage() > 1 ? 
                                                 '#4f8' : '#333'
        document.querySelector('#pageBackButton').style.color = CurPage() > 1 ? 
                                                 '#032' : '#111'
        
        
        document.querySelector('#pageAdvButton').style.background = CurPage() < totalPages + 1 ? 
                                                 '#4f8' : '#333'
        document.querySelector('#pageAdvButton').style.color = CurPage() < totalPages + 1 ? 
                                                 '#032' : '#111'
        
        document.querySelector('#pageLastButton').style.background = CurPage() < totalPages + 1 ? 
                                                 '#4f8' : '#333'
        document.querySelector('#pageLastButton').style.color = CurPage() < totalPages + 1 ? 
                                                 '#032' : '#111'
      }

      window.deleteProject = (slug, name) => {
        if(confirm('delete this project? -> ' + name)){
          let sendData = { slug, passhash }
          var url = URLbase + '/deleteProject.php'
          fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(sendData),
          }).then(res => res.text()).then(data => {
            console.log(`response to delete req: ${data}`)
            main.innerHTML = data
          })
        }
      }
      
      window.navToPage = pg => {
        var tgt
        switch(pg){
          case '1': tgt = 1; break
          case '+1': tgt = Math.min(totalPages + 1, CurPage() + 1); break
          case '-1': tgt = Math.max(1, CurPage() - 1); break
          case 'last': tgt = totalPages + 1; break
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

      var mainEl     = document.querySelectorAll('.main')[0]
      var slug       = GetURLParam('s')
      var page       = GetURLParam('p')
      var html       = ''
      var totalPages = 0
      
      const GetPageData = () => {
        let sendData = { passhash, userID, slug, page }
        var url = URLbase + '/getPageData.php'
        console.log('sendData', sendData)
        fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(sendData),
        }).then(res => res.json()).then(data => {
          console.log(`getPageData.php response: `, data)
          if(!data.success) updateURL('s', '')
          html = data.data
          Refresh(data.success)
        })
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
        console.log('submitting login from cookie', userName, passhash)
        SubmitLogin('', passhash)
      }
      
      const Refresh = (success = true) => {
        if(CurPage() == 0) navToPage('+1')
        if(slug){
          var htmlObj = success ? 
            MarkdownToHTML.Convert(html, CurPage()) : { html, totalPages: 1 }
          mainEl.innerHTML = htmlObj.html
          totalPages = htmlObj.totalPages
          if(success) hljs.highlightAll()
        }else{
          mainEl.innerHTML = html
        }
        
        if(CurPage() > totalPages+1) {
          updateURL('p', totalPages+1)
          Refresh()
        }
        if(CurPage() < 1) {
          updateURL('p', 1)
          Refresh()
        }
        UpdateNavWidget()
      }
      
      CheckLogin()
      Refresh()
    </script>
  </body>
</html>



FILE;
file_put_contents('../../coordocs/index.php', $file);
?>