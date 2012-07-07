<?php
/**
 * Codemirror JS Plugin
 *
 * @version 1.0.1
 * @link https://github.com/marijnh/CodeMirror2
 * @author Redaxo be_style plugin: rexdev.de
 * @package redaxo 4.3.x/4.4.x
 */

$mypage = 'codemirror';

$REX['ADDON']['version'][$mypage]     = '1.0.0';
$REX['ADDON']['author'][$mypage]      = 'jdlx';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';

// INCLUDE JS/CSS
$header = '
<!-- '.$mypage.' -->
<!-- end '.$mypage.' -->
';
$header_include = 'return $params["subject"].\''.$header.'\';';
rex_register_extension('PAGE_HEADER', create_function('$params',$header_include));
