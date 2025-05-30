<?php
  require_once('functions.php');
  $pageData = PageData();
  $passhash = "samplePasshash";
?>
<!DOCTYPE html>
<html>
  <head>
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
        background: #000;
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
      .navButtons{
        cursor: pointer;
        border-radius: 10px;
        background: #333;
        color: #111;
        width: 50px;
        height: 26px;
        margin: 5px;
        line-height: 0;
        padding: 0;
        font-size: 20px;
        font-weight: 900;
        border: 1px solid #fff1;
      }
      .pageNo{
        min-width: 70px;
        display: inline-block;
      }
      .textInput:focus{
        outline: none;
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
        background-color: #200;
        width: 30px;
        height: 30px;
      }
      .textInput{
        font-size: 16px;
        background: #000;
        color: #4f8;
        border-radius: 5px;
        border: 1px solid #fff1;
        width: 300px;
      }
    </style>
  </head>
  <body>
    <div class="header">
      <span onclick="location.href=location.origin+location.pathname">
        <?=$pageData['name']?>
      </span>
      <div
        class="coordocsLogo"
        onclick="navToURL('https://github.com/srmcgann/coordocs')"
        title="visit system repository on Github.com"
       ></div>
    </div>
    <div class="toolbar">
      <div class="toolbarComponent">
        <input
          type="text"
          class="textInput"
          placeholder="search for something"
          spellcheck="false"
        >
      </div>
      <div class="toolbarComponent">
        <button class="navButtons" title="navigate to first page">|&lt;</button>
        <button class="navButtons" title="go back one page">&lt;</button>
        <span class="pageNo">0</span>
        <button class="navButtons" title="advance one page">&gt;</button>
        <button class="navButtons" title="navigate to last page">&gt;|</button>
      </div>
    </div>
    <div class="main"><?=$pageData['data']?></div>
    <script>
    
      var passhash
      var URLbase = '/coordocs'
      var main = document.querySelector('.main')
    
      const navToURL = url => {
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
          params = '?'+param+'=' + value + (params ? '&' : '') + params
        }else{
          params = '?'+param+'=' + value
        }
        var newURL = location.href.split('?')[0] + params
        if(returnVal){
          return newURL
        }else{
          history.replaceState({}, document.title, newURL)
        }
      }

      const loadProject = slug => {
        location.href = updateURL('p', slug, true)
      }

      const deleteProject = slug => {
        
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
      
      checkLogin = () => {
        passhash = "<?=$passhash?>"
      }
      
      checkLogin()
    </script>
  </body>
</html>
