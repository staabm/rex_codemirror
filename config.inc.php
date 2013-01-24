<?php
/**
 * Codemirror2 be_style Plugin for Redaxo
 *
 * @version 1.0.8
 * @link https://github.com/marijnh/CodeMirror2
 * @author Redaxo be_style plugin: rexdev.de
 * @package redaxo 4.3.x/4.4.x
 */

$mypage = 'rex_codemirror';

$REX['ADDON']['version'][$mypage]     = '1.0.8';
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
    <link rel="stylesheet" href="../files/addons/be_style/plugins/'.$mypage.'/vendor/lib/codemirror.css">
    <link rel="stylesheet" href="../files/addons/be_style/plugins/'.$mypage.'/vendor/theme/'.$theme.'.css">
    <link rel="stylesheet" href="../files/addons/be_style/plugins/'.$mypage.'/rex_codemirror_backend.css">
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
  <script src="../files/addons/be_style/plugins/rex_codemirror/vendor/lib/codemirror.js"></script>
  <script src="../files/addons/be_style/plugins/rex_codemirror/custom/lib/util/foldcode.js"></script>
  <script src="../files/addons/be_style/plugins/rex_codemirror/vendor/mode/xml/xml.js"></script>
  <script src="../files/addons/be_style/plugins/rex_codemirror/vendor/mode/javascript/javascript.js"></script>
  <script src="../files/addons/be_style/plugins/rex_codemirror/vendor/mode/css/css.js"></script>
  <script src="../files/addons/be_style/plugins/rex_codemirror/vendor/mode/clike/clike.js"></script>
  <script src="../files/addons/be_style/plugins/rex_codemirror/vendor/mode/php/php.js"></script>
  <script type="text/javascript">
    var RCM_foldmode   = "'.$REX['rex_codemirror']['settings']['foldmode'].'";
    var RCM_blacklist  = ["'.implode('","',$REX['rex_codemirror']['settings']['disabled_textarea_classes']).'"];
    var RCM_theme      = "'.$REX['rex_codemirror']['settings']['theme'].'";
    var RCM_extra_keys = {"'.$REX['rex_codemirror']['settings']['keys']['enter_fullscreen'].'": function(cm){setFullScreen(cm, !isFullScreen(cm));}, "'.$REX['rex_codemirror']['settings']['keys']['leave_fullscreen'].'": function(cm){if (isFullScreen(cm)) setFullScreen(cm, false);}};
  </script>
  <script src="../files/addons/be_style/plugins/rex_codemirror/rex_codemirror.js"></script>
<!-- end rex_codemirror -->
';

    return str_replace('</body>',$script.'</body>',$params['subject']);
  }
}
