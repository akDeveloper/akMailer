<?php

class akZendMailerAdapter extends akMailerAdapter {

  protected $message;
  protected $logger;

  protected function setMailer() {
    switch ($this->transport_type) {
    case 'smtp':
      $conf = array(
        'auth'     => 'login',
        'port'     => $this->port,
        'username' => $this->username,
        'password' => $this->password
      );
      if ( !empty($this->encryption) ) $conf['ssl'] = $this->encryption;
      $this->transport = new Zend_Mail_Transport_Smtp($this->host, $conf);
      break;
    case 'mail':
      break;
    case 'sendmail':
      break;
    }

    $this->mailer = new Zend_Mail();

  }

  protected function setLogger() {

  }

  public function setHeader($name, $value) {
    $this->mailer->addHeader($name, $value);
  }

  public function addAttachment($filepath, $filename) {
    $binary = file_get_contents($filepath);
    $at = $this->mailer->createAttachment($binary);
    $at->disposition = Zend_Mime::DISPOSITION_INLINE;
    $at->encoding    = Zend_Mime::ENCODING_BASE64;
    if ( "" !== $filename) {
      $at->filename    = $filename;
    } else {
      $fileinfo = pathinfo($filepath);
      $at->filename = $fileinfo['basename'];
    }

  }

  public function embed($filepath) {
    $binary = file_get_contents($filepath);
    $at = $this->mailer->createAttachment($binary);
    $at->disposition = Zend_Mime::DISPOSITION_INLINE;
    $at->encoding    = Zend_Mime::ENCODING_BASE64;
    $fileinfo = pathinfo($filepath);
    $at->filename = $fileinfo['basename'];
    $at->id = "cid_" . substr(uniqid(rand(), true), 0, 10);
    return "cid:{$at->id}";
  }

  public function from($email, $name) {
    $this->mailer->setFrom($email, $name);
  }

  public function sendTo($email, $name) {
    $this->mailer->addTo($email,$name);
  }

  public function subject($subject) {
    $this->mailer->setSubject($subject);
  }

  public function body($body, $content_type) {
    if ("text/plain" === $content_type) {
      $this->mailer->setBodyText = $body;
    } elseif ("text/html" === $content_type) {
      $this->mailer->setBodyHtml($body);
    }
  }

  public function send() {
    $this->mailer->send($this->transport);
  }

  public function addCc($email, $name) {
    $this->mailer->addCc($email, $name);
  }

  public function addBcc($email, $name) {
    $this->mailer->addBcc($email, $name);
  }

  public function log() {
    $log = "Message-ID: " . $this->mailer->getMessageId() . "\n";
    $log .= "Date: " . $this->mailer->getDate() . "\n";
    $log .= "Subject: " . $this->mailer->getSubject() . "\n";
    $log .= "From: " . $this->mailer->getFrom() . "\n";
    $log .= "To: " . implode(', ', $this->mailer->getRecipients()) . "\n";
    $log .= "Content-Type: " . $this->mailer->getType() . "; " . $this->mailer->getCharset() . "\n";
    $log .= "Content-Transfer-Encoding: " . $this->mailer->getHeaderEncoding() . "\n";
    $log .= $this->mailer->getBodyHtml(true) . "\n";
    return $log;
  }

}
?>
