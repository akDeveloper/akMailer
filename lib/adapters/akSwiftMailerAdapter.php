<?php 

require_once AK_MAILERS . 'swiftmailer/swift_required.php';

class akSwiftMailerAdapter extends akMailerAdapter {
  
  protected $message;
  protected $logger;

  protected function setMailer() {
    switch ($this->transport_type) {
    case 'smtp':
      $this->transport = Swift_SmtpTransport::newInstance($this->host, $this->port)
        ->setUsername($this->username)
        ->setPassword($this->password);
      if ( !empty($this->encryption) ) $this->transport->setEncryption($this->encryption);
      break;
    case 'mail':
      $this->transport = Swift_MailTransport::newInstance();
      break;
    case 'sendmail':
      $this->transport = Swift_SendmailTransport::newInstance();     
      break;
    }

    $this->mailer = Swift_Mailer::newInstance($this->transport);
    $this->message = Swift_Message::newInstance();
  }

  protected function setLogger() {
    $this->logger = new Swift_Plugins_Loggers_ArrayLogger();
    $this->mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($this->logger));
  }

  public function setHeader($name, $value) {
    $header = $this->message->getHeaders();
    $header->addTextHeader($name, $value);
  }

  public function addAttachment($filepath, $filename) {
    $attachment = Swift_Attachment::fromPath($filepath);
    if ( "" !== $filename ) $attachment->setFilename($filename);
    $this->message->attach($attachment);
  }

  public function embed($filepath) {
    return $this->message->embed(Swift_Image::fromPath($filepath));
  }

  public function from($email, $name) {
    $this->message->setFrom(array($email => $name));
  }

  public function sendTo($email, $name) {
    $this->message->addTo($email, $name);
  }

  public function subject($subject) {
    $this->message->setSubject($subject);
  }

  public function body($body, $content_type) { 
    $this->message->setBody($body, $content_type);
  }
  
  public function send() {
    try {  
      return $this->mailer->send($this->message);
    } catch (SwiftException $e) {
      throw new akMailerException($e->getMessage());
    }
  }

  public function addCc($email, $name) {
    $this->message->addCc($email, $name); 
  }

  public function addBcc($email, $name) {
    $this->message->addBcc($email, $name);
  }

  public function log() {
    if ($this->logger)
      return $this->logger->dump() . "\r\n" . $this->message;
  }

}

?>
