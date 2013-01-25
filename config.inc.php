<?php
/**
 * Codemirror2 be_style Plugin for Redaxo
 *
 * @version 1.2.0
 * @link https://github.com/marijnh/CodeMirror2
 * @author Redaxo be_style plugin: rexdev.de
 * @package redaxo 4.3.x/4.4.x
 */

$mypage = 'rex_codemirror';

$REX['ADDON']['version'][$mypage]     = '1.2.0';
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
  // AUTOENABLED BACKEND PAGES - ANY TEXTAREA WILL GET CODEMIRROR
  'autoenabled_pages' => array(
      array('page'=>'template'),
      array('page'=>'module'),
      array('page'=>'xform', 'subpage'=>'email'),
      array('page'=>'xform', 'subpage'=>'form_templates'),
    ),
  // TRIGGER CLASS - WILL ENABLE CODEMIRROR OUTSIDE AUTOENABLED PAGES
  'trigger_class' => 'rex-codemirror',
  'foldmode'        =>'tagRangeFinder', // @html: tagRangeFinder, @php: braceRangeFinder
  );


// CHECK IF ENABLED PAGE
////////////////////////////////////////////////////////////////////////////////
$enabled_page = false;
foreach($REX[$mypage]['settings']['autoenabled_pages'] as $def){
  foreach ($def as $k => $v) {
    $enabled_page = (rex_request($k,'string')===$v) ? true : false;
  }
  if($enabled_page===true){
    break;
  }
}

$REX[$mypage]['settings']['selector'] = $enabled_page===true
                                      ? 'textarea'
                                      : 'textarea.'.$REX[$mypage]['settings']['trigger_class'];

if($REX['REDAXO'])
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
    var RCM_selector   = "'.$REX['rex_codemirror']['settings']['selector'].'";
    var RCM_theme      = "'.$REX['rex_codemirror']['settings']['theme'].'";
    var RCM_extra_keys = {"'.$REX['rex_codemirror']['settings']['keys']['enter_fullscreen'].'": function(cm){setFullScreen(cm, !isFullScreen(cm));}, "'.$REX['rex_codemirror']['settings']['keys']['leave_fullscreen'].'": function(cm){if (isFullScreen(cm)) setFullScreen(cm, false);}};
    var RCM_fold_func  = CodeMirror.newFoldFunction(CodeMirror.'.$REX['rex_codemirror']['settings']['foldmode'].');
  </script>
  <script src="../files/addons/be_style/plugins/rex_codemirror/rex_codemirror.js"></script>
<!-- end rex_codemirror -->
';

    return str_replace('</body>',$script.'</body>',$params['subject']);
  }
}
