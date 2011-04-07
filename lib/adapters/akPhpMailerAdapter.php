<?php 
require_once AK_MAILERS . 'phpmailer/class.phpmailer.php';

class akPhpMailerAdapter extends akMailerAdapter{

  protected function setMailer() {
    $this->mailer = new PHPMailer();
    switch ($this->transport_type) {
    case 'smtp':
      $this->mailer->IsSMTP();
      $this->mailer->SMTPAuth = true;
      $this->mailer->Host = $this->host;
      $this->mailer->Port = $this->port;
      $this->mailer->Username = $this->username;
      $this->mailer->Password = $this->password;
      if (!empty($this->encryption)) $this->mailer->SMTPSecure = $this->encryption;
      if (true === $this->log) $this->mailer->SMTPDebug = 1;
      break;
    case 'mail':
      $this->mailer->IsMail();
      break;
    case 'sendmail':
      $this->mailer->IsSendmail();
      break;   
    }
  }

  protected function setLogger(){
  }

  public function setHeader($name, $value) {
    $this->mailer->addCustomHeader("{$name}:{$value}");
  }
  
  public function addAttachment($filepath, $filename) {
    $this->mailer->AddAttachment($filepath, $filename);
  }

  public function embed($filepath) {
    $cid = substr(uniqid(rand(), true), 0, 10);
    $this->mailer->AddEmbeddedImage($filepath, $cid, $filepath);
    return "cid:{$cid}";
  }

  public function from($email, $name) {
    $this->mailer->SetFrom($email, $name);
  }

  public function sendTo($email, $name) {
    $this->mailer->AddAddress($email, $name);
  }

  public function subject($subject) {
    $this->mailer->Subject = $subject;
  }

  public function body($body, $content_type) { 
    $this->mailer->MsgHTML($body);
  }
  
  public function send() {
    return $this->mailer->Send();
  }

  public function addCc($email, $name) {
    $this->mailer->AddCC($email, $name); 
  }

  public function addBcc($email, $name) {
    $this->mailer->AddBCC($email, $name);
  }

  public function log() {
    return $this->mailer->ErrorInfo;
  }
 
}
?>
