<?php

class akMailer {

  /**
   * the mailer name that we use for send the email. Could be 'swift_mailer',
   * 'php_mailer' or 'zend_mailer'
   */
  public static $mailer;

  /**
   * transport type of mailer. could be 'smtp', 'mail' or 'sendmail'
   */
  public static $transport_type;

  /**
   * set SMTP server
   */
  public static $host;

  /**
   * set SMTP port
   */
  public static $port = 25;

  /**
   * set SMTP username
   */
  public static $username;

  /**
   * set SMTP username
   */
  public static $password;

  /**
   * encryption type
   */
  public static $encryption;

  /**
   * log boolean value. set this to true if you want to log output
   */
  public static $log = false;

  /**
   * if true then akMailer will not actuall send any emails but will print
   * content to log file
   */
  public static $test = false;

  /**
   * folder path where template files are located
   */
  public static $templates_path;

  /**
   * the instance object of mailer
   */
  protected $instance;

  /**
   * sender email address. accepts same syntax as $from variable.
   */
  protected $from;

  /**
   * recipient(s) email address. could be an array of email addresses in format
   *
   * array(
   *  'name@example.com' => 'name',
   *  'anothername@example.com' => 'another name'
   * )
   *
   * or just a string of email
   *
   * 'name@example.com'
   *
   * this syntax also applies to $cc and $bcc variables
   *
   */
  protected $send_to;

  protected $cc;

  protected $bcc;

  /**
   * subject of email
   */
  protected $subject;

  /**
   * an array handling all variables we can use in email template message
   */
  protected $body = array();

  /**
   * template filename we use to send email(s)
   */
  private   $template_file;

  /**
   * email content type
   */
  private $_content_type;

  private $_attachments = array();

  /**
   * handles the status of send email
   */
  private $status;

  public function __construct(){
    //if (self::$log) Logger::start_logging();
    $this->_createMailerInstance();
  }

  public function __call($name, $args) {
    preg_match("/send_(.*)/", $name, $matches);
    if (!empty($matches) ) {
      $function = $matches[1];
      $this->template_file = $function;
      call_user_func_array(array($this, $function), $args);
      return $this->deliver();
    }
  }
  /**
   * add an attachment
   *
   * @param string $filepath
   * @oaram string $filename
   */
  public function addAttachment($filepath, $filename=null) {
    if (!file_exists($filepath) ) throw new akMailerException("{$filepath} does not exist!");
    if ( null === $filename ) {
      $attach = array($filepath);
    } else {
      $attach = array($filepath => $filename);
    }
    $this->_attachments = array_merge($this->_attachments, $attach);
    //if (self::$log) Logger::log("Added attachment file {$filepath}");
  }

  /**
   * add an inline attachment to message
   *
   * @param string $filepath
   */
  public function setEmbeddedAttachment($filepath) {
    if (!file_exists($filepath) ) throw new akMailerException("{$filepath} does not exist!");
    //if (self::$log) Logger::log("Added attachment file {$filepath}");
    return $this->instance->embed($filepath);
  }

  /**
   *
   */
  public function isSuccess() {
    return true == $this->status;
  }

private function deliver() {
    $this->_composeEmail();
    if ( false === self::$test )  {
      try{
         $this->status = $this->instance->send();
      } catch(Exception $e) {
        throw new akMailerException($e->getMessage());
      }
    } else {
      //Logger::log($this->instance->log());
      return true;
    }
    if (true === self::$log && false === self::$test){
      //Logger::log($this->instance->log());
    }

  }

  private function _createMailerInstance(){
    $options = array();

    if ('smtp' === self::$transport_type) {
      $options['host']       = self::$host;
      $options['port']       = self::$port;
      $options['username']   = self::$username;
      $options['password']   = self::$password;
      $options['encryption'] = self::$encryption;
    }

    $options['transport_type'] = self::$transport_type;
    $options['log']            = self::$log;

    if ( empty(self::$mailer) ) throw new akMailerException("You must set a mailer!");

    $mailer_class = "ak" . $this->_camelize(self::$mailer) . "Adapter";
    $adapter_path = dirname(__FILE__) . '/adapters/' . $mailer_class . ".php";

    if ( file_exists( $adapter_path ) ) {
      require_once $adapter_path;
    } else {
      throw new akMailerException("Could not find {$mailer_class} adapter!");
    }
    //if (self::$log) Logger::log("Using {$mailer_class}");
    $this->instance = new $mailer_class($options);
  }

  private function _composeEmail() {
    # set sender info
    $this->_passVariablesToInstance('from',$this->from);

    # set recipient(s) info
    $this->_passVariablesToInstance('sendTo',$this->send_to);

    # set recipient(s) info
    if ( "" != $this->cc )
      $this->_passVariablesToInstance('addCc',$this->cc);

    # set recipient(s) info
    if ( "" != $this->bcc )
      $this->_passVariablesToInstance('addBcc',$this->bcc);

    # set message subject
    $this->instance->subject($this->subject);

    # set the message body
    $body = $this->_getTemplate();
    $this->instance->body($body, $this->_content_type);

    # set attachments
    $this->_passVariablesToInstance('addAttachment',$this->_attachments);
  }

  private function _passVariablesToInstance($function, $variables) {
    if (is_array($variables)) {
      foreach( $variables as $key=>$value) {
        if (!is_numeric($key)) {
          $this->instance->$function($key,$value);
        } else {
          $this->instance->$function($value, "");
        }
      }
    } else {
      $this->instance->$function($variables,"");
    }
  }

  private function _getTemplate() {
    $html_file = self::$templates_path . $this->template_file . ".text.html.php";
    $plain_file = self::$templates_path . $this->template_file . ".text.plain.php";
    $default_file = self::$templates_path . $this->template_file . ".php";

    if ( file_exists( $html_file ) ){
      $file = $html_file;
      $this->_content_type = "text/html";
    } elseif ( file_exists( $plain_file ) ) {
      $file = $plain_file;
      $this->_content_type = "text/plain";
    } elseif ( file_exists( $default_file ) ) {
      $file = $defualt_file;
      $this->_content_type = "text/html";
    } else {
      throw new akMailerException ("Missing template file '{$this->template_file}.php'");
    }

    if (!empty($this->body) ) {
      foreach ( $this->body as $k=>$v ) {
        $$k = $v;
      }
    }

    ob_start();
    ob_implicit_flush(0);
    try {
      eval("?>".file_get_contents($file));
      $body = ob_get_contents();
    } catch (Exception $e) {
      ob_end_clean();
      throw $e;
    }

    ob_end_clean();
    return $body;
  }

  private function _camelize($string) {
    return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
  }
}

?>
