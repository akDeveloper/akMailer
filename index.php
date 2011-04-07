<?php
$currentFilePath = dirname(realpath(__FILE__));
set_include_path($currentFilePath . '/mailers/zendmailer/library/'  . PATH_SEPARATOR . get_include_path());
require_once 'Zend/Mail.php';
#$send = $mailer->send($message);
?>
