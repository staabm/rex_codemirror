<?php
/**
 * Codemirror JS Plugin
 *
 * @version 1.0.1
 * @link https://github.com/marijnh/CodeMirror2
 * @author Redaxo be_style plugin: rexdev.de
 * @package redaxo 4.3.x/4.4.x
 */

$error = '';

if ($error != '') {
  $REX['ADDON']['installmsg']['codemirror'] = $error;
} else {
  $REX['ADDON']['install']['codemirror'] = true;
}
