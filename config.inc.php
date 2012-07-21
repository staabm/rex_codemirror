<?php
/**
 * Codemirror2 be_style Plugin for Redaxo
 *
 * @version 1.0.5
 * @link https://github.com/marijnh/CodeMirror2
 * @author Redaxo be_style plugin: rexdev.de
 * @package redaxo 4.3.x/4.4.x
 */

$mypage = 'rex_codemirror';

$REX['ADDON']['version'][$mypage]     = '1.0.5';
$REX['ADDON']['author'][$mypage]      = 'jdlx';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';


// SETTINGS
////////////////////////////////////////////////////////////////////////////////
/* THEMES:
 * ambiance, blackboard, cobalt, eclipse, elegant, erlang-dark,
 * lesser-dark, monokai, neat, night, rubyblue, vibrant-ink, xq-dark,
 * custom: jdlx
 */
$REX[$mypage]['settings'] = array(
  'theme'          =>'jdlx',
  'keys' => array(
    'enter_fullscreen' => 'F11',
    'leave_fullscreen' => 'Esc',
    ),
  // WHITELIST: ENABLED BACKEND PAGES
  'enabled_pages' => array(
      array('page'=>'content'),
      array('page'=>'template'),
      array('page'=>'module'),
      array('page'=>'xform', 'subpage'=>'email'),
      array('page'=>'xform', 'subpage'=>'form_templates'),
    ),
  // BLACKLIST: NO CODEMIRROR TEXTAREA CLASS NAMES
  'disabled_textarea_classes' => array(
    'no-codemirror','markitup'
    ),
  'foldmode'          =>'tagRangeFinder', // @html: tagRangeFinder, @php: braceRangeFinder
  );


// CHECK IF ENABLED PAGE
////////////////////////////////////////////////////////////////////////////////
$enabled = false;
foreach($REX[$mypage]['settings']['enabled_pages'] as $def){
  foreach ($def as $k => $v) {
    $enabled = (rex_request($k,'string')===$v) ? true : false;
  }
  if($enabled===true){
    break;
  }
}


if($REX['REDAXO'] && $enabled===true)
{
  // INCLUDE JS/CSS ASSETS @ HEAD
  //////////////////////////////////////////////////////////////////////////////
  $theme = $REX[$mypage]['settings']['theme'];
  $header = '
  <!-- '.$mypage.' -->
    <link rel="stylesheet" href="../files/addons/be_style/plugins/'.$mypage.'/lib/lib/codemirror.css">
    <link rel="stylesheet" href="../files/addons/be_style/plugins/'.$mypage.'/lib/theme/'.$theme.'.css">
    <link rel="stylesheet" href="../files/addons/be_style/plugins/'.$mypage.'/rex_codemirror_backend.css">
    <script src="../files/addons/be_style/plugins/'.$mypage.'/lib/lib/codemirror.js"></script>
    <script src="../files/addons/be_style/plugins/'.$mypage.'/custom/lib/util/foldcode.js"></script>
    <script src="../files/addons/be_style/plugins/'.$mypage.'/lib/mode/xml/xml.js"></script>
    <script src="../files/addons/be_style/plugins/'.$mypage.'/lib/mode/javascript/javascript.js"></script>
    <script src="../files/addons/be_style/plugins/'.$mypage.'/lib/mode/css/css.js"></script>
    <script src="../files/addons/be_style/plugins/'.$mypage.'/lib/mode/clike/clike.js"></script>
    <script src="../files/addons/be_style/plugins/'.$mypage.'/lib/mode/php/php.js"></script>
  <!-- end '.$mypage.' -->
  ';
  $header_include = 'return $params["subject"].\''.$header.'\';';
  rex_register_extension('PAGE_HEADER', create_function('$params',$header_include));


  // CODEMIRROR ENABLER SCRIPT @ BODY END
  //////////////////////////////////////////////////////////////////////////////
  rex_register_extension('OUTPUT_FILTER', 'codemirror_enabler');

  function codemirror_enabler($params)
  {
    global $REX;
    $script = '
<!-- rex_codemirror -->
<script type="text/javascript">

var codemirrors = {};
var foldFunc = CodeMirror.newFoldFunction(CodeMirror.'.$REX['rex_codemirror']['settings']['foldmode'].');

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

  var blacklist = ["'.implode('","',$REX['rex_codemirror']['settings']['disabled_textarea_classes']).'"];
  i = 1;

  $("textarea").each(function(){
    area = $(this);

    // CHECK BLACKLIST CLASSES
    skip = false;
    $.each(blacklist, function(i,v) {
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
        theme:"'.$REX['rex_codemirror']['settings']['theme'].'",
        matchBrackets: true,
        mode: "application/x-httpd-php",
        indentUnit: 2,
        indentWithTabs: true,
        enterMode: "keep",
        tabMode: "shift",
        onGutterClick: foldFunc,
        extraKeys: {
          "'.$REX['rex_codemirror']['settings']['keys']['enter_fullscreen'].'": function(cm) {
            setFullScreen(cm, !isFullScreen(cm));
          },
          "'.$REX['rex_codemirror']['settings']['keys']['leave_fullscreen'].'": function(cm) {
            if (isFullScreen(cm)) setFullScreen(cm, false);
          }
        }
      });

      // (RE)APPLY TEXTAREA DIMENSIONS
      codemirrors[id].getWrapperElement().style.width = w+"px";
      codemirrors[id].getWrapperElement().style.marginLeft = ml;
      codemirrors[id].getScrollerElement().style.height = h+"px";
      codemirrors[id].refresh()
    }

    i++;
  }); // textarea.each

////////////////////////////////////////////////////////////////////////////////
})(jQuery); // END NOCONFLICT ONLOAD ///////////////////////////////////////////

</script>
<!-- end rex_codemirror -->
';

    return str_replace('</body>',$script.'</body>',$params['subject']);
  }
}
