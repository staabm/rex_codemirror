var codemirrors = {};
var foldFunc = CodeMirror.newFoldFunction(CodeMirror.RCM_foldmode);

function isFullScreen(cm) {
  return /\bCodeMirror-fullscreen\b/.test(cm.getWrapperElement().className);
}
function winHeight() {
  return window.innerHeight || (document.documentElement || document.body).clientHeight;
}
function setFullScreen(cm, full) {
  var wrap = cm.getWrapperElement(), scroll = cm.getScrollerElement();
  if (full) {
    wrap.className += " CodeMirror-fullscreen";
    scroll.style.height = winHeight() + "px";
    document.documentElement.style.overflow = "hidden";
    cm.setOption("lineWrapping", false);
  } else {
    wrap.className = wrap.className.replace(" CodeMirror-fullscreen", "");
    scroll.style.height = "";
    document.documentElement.style.overflow = "";
    cm.setOption("lineWrapping", true);
  }
  cm.refresh();
}
CodeMirror.connect(window, "resize", function() {
  var showing = document.body.getElementsByClassName("CodeMirror-fullscreen")[0];
  if (!showing) return;
  showing.CodeMirror.getScrollerElement().style.height = winHeight() + "px";
});


(function ($) { // NOCONFLICT ONLOAD ///////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

  i = 1;

  $("textarea").each(function(){
    area = $(this);

    // CHECK BLACKLIST CLASSES
    skip = false;
    $.each(RCM_blacklist, function(i,v) {
      if(area.hasClass(v)){
        skip = true;
        return false;
      }
    });


    if(skip===false){

      // ANON CSS ID IF NECESSARY
      id = area.attr("id");
      if(id=="undefined"){
        id = "cm-id-"+i;
        area.attr("id",id);
      }

      // GET TEXTAREA DIMENSIONS
      w = area.width();
      h = area.height();
      ml = area.css("margin-left");

      // INIT CODEMIRROR
      codemirrors[id] = CodeMirror.fromTextArea(area.get(0), {
        mode: "php",
        lineNumbers: true,
        lineWrapping: false,
        theme: RCM_theme,
        matchBrackets: true,
        mode: "application/x-httpd-php",
        indentUnit: 2,
        indentWithTabs: true,
        enterMode: "keep",
        tabMode: "shift",
        onGutterClick: foldFunc,
        extraKeys: RCM_extra_keys
      });

      // (RE)APPLY TEXTAREA DIMENSIONS
      codemirrors[id].getWrapperElement().style.width = w+"px";
      codemirrors[id].getWrapperElement().style.marginLeft = ml;
      codemirrors[id].getScrollerElement().style.height = h+"px";
      codemirrors[id].refresh();
    }

    i++;
  }); // textarea.each

////////////////////////////////////////////////////////////////////////////////
})(jQuery); // END NOCONFLICT ONLOAD ///////////////////////////////////////////
