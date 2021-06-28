<?php

/**
 * This example shows settings to use when sending via Google's Gmail servers.
 */

namespace helper;

use config\Constants as Constants;
use config\Loader as Loader;

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
class Mail {

    private $mail;

    public function __construct() {
//        Loader::load_library(Loader::$PHPMAILER);
        //Create a new PHPMailer instance
        require 'libs/PHPMailer-master/PHPMailerAutoload.php';
        $this->mail = new \PHPMailer;

        //Tell PHPMailer to use SMTP
        $this->mail->isSMTP();

        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $this->mail->SMTPDebug = 0;

        //Ask for HTML-friendly debug output
        $this->mail->Debugoutput = 'html';

        //Set the hostname of the mail server
        $this->mail->Host = 'smtp.gmail.com';
        // use
        // $mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $this->mail->Port = 587;

        //Set the encryption system to use - ssl (deprecated) or tls
        $this->mail->SMTPSecure = 'tls';

        //Whether to use SMTP authentication
        $this->mail->SMTPAuth = true;

        //Username to use for SMTP authentication - use full email address for gmail
        $this->mail->Username = "siame.belgrano@gmail.com";

        //Password to use for SMTP authentication
        $this->mail->Password = "39019775";

        //Set who the message is to be sent from
        $this->mail->setFrom('siame.belgrano@gmail.com', 'ADMIN');

        //Set an alternative reply-to address
        //$mail->addReplyTo('replyto@example.com', 'First Last');
        //Set who the message is to be sent to
//        $this->mail->addAddress('estebansantillan96@gmail.com', 'Esteban Santilan');
        //Set an alternative reply-to address
        $this->mail->addReplyTo('siame.belgrano@gmail.com', 'ADMINISTRADOR DEL SISTEMA');
    }

    public function send_email($subject = '', $address = array(), $path_file_html = '', $alt_body = '') {
        //Set the subject line
        $this->mail->Subject = $subject;

        //Set who the message is to be sent to
        foreach ($address as $key => $value) {
            $this->mail->addAddress($key, $value);
        }

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        //$mail->msgHTML("Hola desde PHP!");
        $msg = '<html><body><section class="w3-container"><article id="main" style="padding:0.01em 16px;box-shadow:0  2px 4px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12)!important;border-radius:4px!important;"><p>';
        if ($path_file_html) {
            $msg = file_get_contents(Constants::$ROOT . 'view' . Constants::$DS . 'public' . Constants::$DS . 'html' . Constants::$DS . $path_file_html . '.html');
        } else {
            $msg .= $alt_body . '</p></article></section></body></html>';
        }
        
        $base_img_path = Constants::$ROOT . 'view\public\img';
        $this->mail->msgHTML($msg, $base_img_path);
        //Replace the plain text body with one created manually
        $this->mail->AltBody = $alt_body;
        //Attach an image file
//        $this->mail->addAttachment(Constants::$ROOT . 'view\public\img\logo.png');
//        $this->mail->addAttachment('D:\ISOs\4-Desarrollo\Proyectos\Notas\DatePicker _ DateRangePicker.docx');
        //send the message, check for errors
        if(!$this->mail->send()){
            throw new \Exception("No se ha podido enviar el correo.");
        }
        
    }

}
