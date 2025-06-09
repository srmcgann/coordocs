// 'md2html.js', a Markdown converter
// Scott McGann - whitehotrobot@gmail.com
// all rights reserved - ©2025

const MaxLinesPerPage = 1e5;

const Convert = (src, curPage=0, navEl = '', contEl = '') => {
  var linePos = 0, pageCt = 0;
  var wSrc = src.replaceAll("\r\n", "\n")
  //var wSrc = wSrc.replaceAll("<br>", "\n")
  var ret = '', inCodeBlock = false
  var pageBreakRequested = false, codeBuffer
  
  const pageFilter = () => curPage == pageCt+1
  const totalPages = () => src.split("&lt;pagebreak").length
  
  src.split("\n").forEach(line => {
    if(inCodeBlock){
      if(line.substr(0, 3) == '```'){
        var B64 = btoa(unescape(encodeURIComponent(codeBuffer)))
        ret += pageFilter() ? '</code></pre><div data-customtooltip="copy code section to clipboard" class="copyCodeAppendage"><button class="toolButton copyButton" onclick="copyB64(\''+B64+'\')"></button></div><br><br>' : ''
        inCodeBlock = false
      }else{
        codeBuffer += pageFilter() ? line + "\n" : ''
        ret += pageFilter() ? line + "\n" : ''
      }
    }else if(1||line){
      line = line.replaceAll('&lt;','<')
      if(pageBreakRequested){
        pageBreakRequested = false
        pageCt++
        if(pageFilter()){
          if(navEl && contEl) {
            setTimeout(()=>{
              if(document.querySelectorAll('#md2html_nav').length == 0){
                var apEl = navEl.cloneNode(true)
                apEl.id = "md2html_nav"
                apEl.style.float = 'none'
                apEl.style.left = '50%'
                apEl.style.transform = 'translate(-50%)'
                contEl.innerHTML += '<br><br><br><br><br><center><span style="color: #888;">'+(totalPages() == curPage ? 'end of document' : 'continued on the next page...')+'</span><br><br><br>'
                contEl.appendChild(apEl)
              }
            }, 0)
          }
        }
      }
      if(line.substr(0, 10) == '<pagebreak'){
        tagName = ''
        closingTag = '<br>'
        pageBreakRequested = true
        if(curPage == 1 && pageFilter()){
          ret += '<br><br><br><br><br><center><span style="color: #888;">continued on the next page...</span><br><br><br>'
          if(navEl && contEl) {
            setTimeout(()=>{
              if(document.querySelectorAll('#md2html_nav').length == 0){
                var apEl = navEl.cloneNode(true)
                apEl.id = "md2html_nav"
                apEl.style.float = 'none'
                apEl.style.left = '50%'
                apEl.style.transform = 'translate(-50%)'
                contEl.appendChild(apEl)
              }
            }, 0)
          }
          ret += '</center>'
        }
      }else if(line.substr(0, 5) == '```js'){
        ret += pageFilter() ? '<br><br><pre><code language="javascript" style="line-height: initial;">' : ''
        inCodeBlock = true
        codeBuffer = ''
      }else if(line.substr(0, 3) == '```'){
        ret += pageFilter() ? '<br><br><pre><code style="line-height: initial;">' : ''
        inCodeBlock = true
        codeBuffer = ''
      }else{
        var fontSize = "1em"
        //var tagName = '<div>'
        //var closingTag = '</div>'
        var tagName = ''
        //line = line.replaceAll('<br>', '')
        var closingTag = ''
        var skipShift = false
        var isLi = false
        /*
        var o = true
        line = line.split('').map((v, i)=>{
          var ch = v
          if(o){
            if(ch == ' ') ch == '&nbsp;'
          }else{
            o = false
          }
          return ch
        }).join('')
        */
        var tok1 = line.trim().split(' ')
        if(tok1.length > 0){
          for(var i = 1e4; i--;) if(tok1[0] == `${i+1}.`) {
            tagName = `<ol start="${i+1}">`
            closingTag = '</ol>'
            isLi = true
          }
          switch(tok1[0]){
            case '>': tagName = '<blockquote>', closingTag = '</blockquote>'; break
            case '`code`':
              tagName = '<pre class="inline-pre"><code>'
              closingTag = '</code></pre>'
            break
            case '*': tagName = '<ul>', closingTag = '</ul>'; isLi=true; break
            case '---': tagName = '<hr>', closingTag = '</hr>'; break
            case '-': tagName = '<ul>', closingTag = '</ul>'; isLi=true; break
            case '#': tagName = '<H1>', closingTag = '</H1>'; break
            case '##': tagName = '<H2>', closingTag = '</H2>'; break
            case '###': tagName = '<H3>', closingTag = '</H3>'; break
            case '####': tagName = '<H4>', closingTag = '</H4>'; break
            case '#####': tagName = '<H5>', closingTag = '</H5>'; break
            default: if(!isLi) skipShift = true; break
          }
          if(!skipShift) tok1.shift()
          line = tok1.join(' ')
          if(isLi) line = `<li>${line}</li>`
        }
        //var rLine = tagName

        // images
        if(line.split('![').length > 1 && line.split(']').length > 1 &&
           line.split('](').length > 1 && line.split(')').length > 1){
          tagName = ''
          closingTag = ''
          var links = []
          line.split('![').forEach((p1, idx) =>{
            if(idx){
              var title = p1.split(']')[0]
              var url = p1.split('(')[1].split(')')[0]
              links = [...links, {title, url}]
            }
          })
          var s = ''
          var tog = true
          var ct = 0
          line.split('').forEach(chr => {
            if(chr == '!') tog =false
            if(tog) s += chr
            if(!tog && chr == ')') {
              s+=`<img title="${links[ct].title}" style="max-width: 600px; margin: 10px;" src="${links[ct].url}" alt="${links[ct].title}"/>`
              tog = true
              ct++
            }
          })
          line = s
        }

        // links
        if(line.split('[').length > 1 && line.split(']').length > 1 &&
           line.split('](').length > 1 && line.split(')').length > 1){
          tagName = ''
          closingTag = ''
          var links = []
          line.split('[').forEach((p1, idx) =>{
            if(idx){
              var title = p1.split(']')[0]
              var url = p1.split('(')[1].split(')')[0]
              links = [...links, {title, url}]
            }
          })
          var s = ''
          var tog = true
          var ct = 0
          line.split('').forEach(chr => {
            if(chr == '[') tog =false
            if(tog) s += chr
            if(!tog && chr == ')') {
              s+=`<a "target="_blank" href="${links[ct].url}">${links[ct].title}</a>`
              tog = true
              ct++
            }
          })
          line = s
        }

        // bold text
        if(line.split('**').length > 1 && line.split('**').length%2==1){
          tagName = ''
          closingTag = ''
          var v = ''
          var l = line.split('**')
          l.forEach((part, idx) => {
            v += part + (idx < l.length-1 ? (idx%2?'</b>':'<b>') : '')
          })
          line = v
        }

        // italic text
        if(line.split('*').length > 1 && line.split('*').length%2==1){
          tagName = ''
          closingTag = ''
          var v = ''
          var l = line.split('*')
          l.forEach((part, idx) => {
            v += part + (idx < l.length-1 ? (idx%2?'</i>':'<i>') : '')
          })
          line = v
        }

        // code shorthand (``)
        if(line.split('``').length > 1 && line.split('``').length%2==1){
          tagName = ''
          closingTag = ''
          var v = ''
          var l = line.split('``')
          l.forEach((part, idx) => {
            v += part +
                   (idx < l.length-1 ?
                     (idx%2?'</code></pre>':
                       '<pre style="display: inline-block; vertical-align: middle;"><code>') :
                         '')
          })
          line = v
        }
        
        // code shorthand (`)
        if(line.split('`').length > 1 && line.split('`').length%2==1){
          tagName = ''
          closingTag = ''
          var v = ''
          var l = line.split('`')
          l.forEach((part, idx) => {
            v += part +
                   (idx < l.length-1 ?
                     (idx%2?'</code></pre>':
                       '<pre style="display: inline-block; vertical-align: middle;"><code>') :
                         '')
          })
          line = v
        }
        
        var rLine = tagName + line
        rLine += closingTag
        if(isLi) rLine = rLine.replaceAll('<br>','')
        if(rLine.indexOf('<') == -1 && line.indexOf('>') == -1) rLine = rLine.replaceAll(' ', '&nbsp;') + '<br>'
        ret += pageFilter() ? rLine : ''
        linePos++
      }
    }else{
      if(line) linePos++
    }
  })
  return {
    html: ret, 
    totalPages: totalPages()
  }
}

export {
  Convert
}








