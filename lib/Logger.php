<?php
define('RESET_SEQ',"\033[0m");
define('COLOR_SEQ',"\033[");
define('BOLD_SEQ', "\033[1m");
/**
 * Description of Logger
 *
 * @author Andreas Kollaros
 */
class Logger {

  public static $start_time;
  public static $log_path;
  
  private static $handler;

  static function &getHandler(){
    if (!self::$handler){
      register_shutdown_function(array('Logger','end_logging'));
      
      if ( null === self::$log_path )
        self::$log_path = realpath(dirname(__FILE__)) . "/../log/ak_mailer.log";

      if ( !is_writable(self::$log_path) ) return false;
      
      self::$handler = fopen(self::$log_path, 'a');
      return self::$handler;
    }
    return self::$handler;
  }

  static public function start_logging(){
    self::$start_time = microtime(true);
    self::log (COLOR_SEQ ."1;32m"
               ."Started at : [".date('H:i:s d-m-Y', time())."]"
               .RESET_SEQ);
  }

  static public function warn($string){
    self::log (COLOR_SEQ . "1;37m" . "!! WARNING: " . $string . RESET_SEQ);
  }

  static public function log($string){
    $fp = &self::getHandler();
    if ($fp) fwrite($fp, $string."\n");
  }

  static public function error($string){
    self::log( COLOR_SEQ."1;31m".$string.RESET_SEQ);
  }

  static public function end_logging(){
    $buffer = COLOR_SEQ."1;32mParse time: ("
              .number_format( (microtime(true) - self::$start_time) * 1000,'4')
              ."ms)".RESET_SEQ;
    self::log($buffer);
    if (is_resource(self::$handler) )
      fclose(self::$handler);
  }
}
?>
