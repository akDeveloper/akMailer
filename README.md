# akMailer wrapper

This library is a wrapper for the most common php mailers.

You can use [swiftmailer](http://www.swiftmailer.org/), [phpmailer](http://http://phpmailer.worxware.com/) or [Zend Mail](http://framework.zend.com/manual/en/zend.mail.html) to send email.

## Features
* smpt transport support.
* Use html or plain text templates. You can create templates in seperate file and the library will use them as body for your emails.

## Example Usage
Create the class of your mailer by extending the class akMailer.

    # MyMailer.php
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

Use your new class
    # example.php
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

Define your template
    # example_email.text.html.php
    <h2>Dear <?php echo $title ?> <?php echo $name ?></h2>
    <p><img src="<?php echo $embedded ?>" alt="title"></p>
    <p><em>Inline attachment</em>
    <p>This is a demo example from akMailer</p>
    <p>Thank you.</p>
