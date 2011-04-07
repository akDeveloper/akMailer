<?php 
/**
 * Exception class.
 *
 * @package akMailer
 * @author Andreas Kollaros
 */
class akMailerException extends Exception {

  public function __construct($message) {
    if (akMailer::$log) Logger::error($message);
    parent::__construct($message);
  }
}
?>
