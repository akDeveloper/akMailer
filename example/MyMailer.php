<?php 

class MyMailer extends akMailer {
  
  public function example_email($options) {
    $this->from     = array("noreply@example.com" => "My example");
    $this->cc       = array("recipient_1@domain.tld" => "A name");
    $this->bcc      = array("recipient_2@domain.tld" => "Another Name");
    $this->send_to  = array( $options['recipient'] => $options['name'] );
    $this->subject  = 'My Subject';
    $attach = realpath(dirname(__FILE__) . "/tux.png");
    $embedded = $this->setEmbeddedAttachment($attach);
    # The keys of this array would be variables in your template file.
    $this->body     = array( 
      'name' => $options['name'],
      'title' => $options['title'],
      'embedded' => $embedded
    );
  }
}

?>
