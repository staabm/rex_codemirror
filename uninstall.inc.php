<?php
/**
 * Codemirror JS Plugin
 *
 * @version 1.0.0
 * @link https://github.com/marijnh/CodeMirror2
 * @author Redaxo be_style plugin: rexdev.de
 * @package redaxo 4.3.x/4.4.x
 */

$error = '';

if ($error != '') {
  $REX['ADDON']['installmsg']['rex_codemirror'] = $error;
} else {
  $REX['ADDON']['install']['rex_codemirror'] = 0;
}
