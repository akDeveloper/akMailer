<?php 
abstract class akMailerAdapter {

  /**
   * SMTP server
   */
  protected $host;

  /**
   * SMTP port
   */
  protected $port = 25;

  /**
   * SMTP username
   */
  protected $username;

  /**
   * SMTP username
   */
  protected $password;

  /**
   * encryption type
   */
  protected $encryption;

  /**
   * transport type
   */
  protected $trasport_type;

  /**
   * transport instance
   */
  protected $transport;
  
  /**
   * mailer instance
   */
  protected $mailer;

  protected $log = false;

  public function __construct($options = array()) {
    $this->required_options('transport_type',$options);
    $this->transport_type = $options['transport_type'];

    if ( 'smtp' === $options['transport_type'] ) {
      
      $this->required_options('host, username, password',$options);

      $this->host       = $options['host'];
      $this->port       = isset( $options['port'] ) ? $options['port'] : 25;
      $this->username   = $options['username'];
      $this->password   = $options['password'];
      $this->encryption = isset($options['encryption']) ? 
        $options['encryption'] : null;
    }

    if ( isset($options['log']) && true === $options['log']) {
      $this->log = $options['log'];
    }

    $this->setMailer();
    
    if ($this->log) $this->setLogger();

  }

  abstract protected function setMailer();

  abstract protected function setLogger();
  
  abstract public function from($email, $name);
  
  abstract public function sendTo($email, $name);
  
  abstract public function subject($subject);

  abstract public function body($body, $content_type);

  abstract public function addCc($email, $name);

  abstract public function addBcc($email, $name);
  
  abstract public function addAttachment($filepath, $filename);
  
  abstract public function embed($filepath);

  abstract public function setHeader($name, $value);

  abstract public function log();

  /**
   * RequiresParameters
   * @param string comma seperated parameters. Represent keys of $options array
   * @param array the key/value hash of options to compare with
   */
  protected function required_options($required, $options = array()) {
    $required = explode(',', $required);
    foreach ($required as $r) {
      if (!array_key_exists(trim($r), $options)) {
        throw new akMailerException($r . " parameter is required!");
        break;
        return false;
      }
    }
    return true;
  }
}
?>
