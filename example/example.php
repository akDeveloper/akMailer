<?php

require_once '../lib/init.php';
require_once 'MyMailer.php';

akMailer::$host     = 'mail.domain.tld'; #Set your email host
akMailer::$port     = 25;
akMailer::$username = 'username';        # Set your username
akMailer::$password = 'password';        # Set your password
akMailer::$encryption = 'tls';           # Set the type of encryption
akMailer::$transport_type = 'smtp'; 
akMailer::$mailer   = 'swift_mailer';
akMailer::$log      = true;              # allow logging events
akMailer::$test     = false;             # Set this to false and no email will be send. It would be only logged.
akMailer::$templates_path = realpath( dirname(__FILE__) ). "/templates/";
try {
  $mailer = new MyMailer();

  $options =array(
    'recipient' => 'your_test_email@domain.tld',
    'name' => 'John Doe',
    'title' => 'Mr'
  );

  /*
   * Call the method from your Mailer object adding the prefix 'send_'.
   * so here the method in MyMailer object is example_email(), and is called
   * like send_example_email()
   */ 
  $mailer->send_example_email($options);
  if ( $mailer->isSuccess() ) {
    echo 'Successfully send email!';
  }
} catch (akMailerException $e) {
  echo $e->getMessage();
}
?>
