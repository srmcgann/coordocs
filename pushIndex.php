<?
$file = <<<'FILE'
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
      #pageNo{
        min-width: 70px;
        display: inline-block;
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
        border-radius: 10px;
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
      a{
        color: #f08;
      }
      a:visited{
        color: #804;
      }
    </style>
    <link rel="stylesheet" href="./highlight.js/violet.min.css">
    <script src="./highlight.js/highlight.min.js"></script>
  </head>
  <body>
    <div class="header">
      <span
        class="headerTitle"
        onclick="location.href=location.origin+location.pathname">
        manage project documentation
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
          class="textInput"
        ><?=$pageData['name']?></span>
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
          placeholder="search for something"
          spellcheck="false"
        >
      </div>
    </div>

    <div class="main"></div>

    <script type="module">
      import * as MarkdownToHTML from "./md2html.js"
    
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

      window.LoadProject = slug => {
        location.href = updateURL('p', 1, false)
        location.href = updateURL('s', slug, true)
      }

      const UpdateNavWidget = () => {
        document.querySelectorAll('.main')[0].scroll(0,0)
        document.querySelector('#pageNo').innerHTML = CurPage()
        
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

      const deleteProject = (slug, name) => {
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

      var mainEl     = document.querySelectorAll('.main')[0]
      var slug       = `<?=$pageData['slug']?>`
      var html       = `<?=$pageData['data']?>`
      var totalPages = 0
      
      const Refresh = () => {
        if(slug){
          var htmlObj =
            MarkdownToHTML.Convert(html, CurPage())
          mainEl.innerHTML = htmlObj.html
          totalPages = htmlObj.totalPages
          hljs.highlightAll()
        }else{
          mainEl.innerHTML = html
        }
        
        UpdateNavWidget()
      }
      
      Refresh()
    </script>
  </body>
</html>



FILE;
file_put_contents('../../coordocs/index.php', $file);
?>