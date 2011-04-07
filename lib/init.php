<?php 
define('AK_MAILERS',  dirname(__FILE__) . "/../mailers/");
require_once 'Logger.php';
require_once 'akMailerException.php';
require_once 'adapters/akMailerAdapter.php';
require_once 'adapters/akPhpMailerAdapter.php';
require_once 'adapters/akSwiftMailerAdapter.php';
require_once 'akMailer.php';
?>
