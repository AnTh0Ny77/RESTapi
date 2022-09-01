<?php
namespace Src\Services;
require  '././vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


Class MailerServices {

    public $config;

    public function __construct(){
        $this->config = json_decode(file_get_contents('config.json'));
    }

    public function sendMail($adresse , $subject , $template){
        $mail = new PHPMailer(true);
        try {
           
                        
            $mail->isSMTP();                                           
            $mail->Host       =  $this->config->mailer->host;                     
            $mail->SMTPAuth   =  true;                                   
            $mail->Username   =  $this->config->mailer->username;                     
            $mail->Password   =  $this->config->mailer->password;                              
            $mail->SMTPSecure =  PHPMailer::ENCRYPTION_SMTPS;            
            $mail->Port       =  465;                                    
            $mail->setFrom('info@myrecode.fr', 'MyRecode');
            $mail->addAddress($adresse);    
            $mail->isHTML(true);                                  
            $mail->Subject =  $subject;
            $mail->Body    = $template;
            $mail->send();
            return true ;
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }


    public function header(){
        return 'Header...<br><br>';
    }

    public function signature(){
            return '<br><br>Signature...';
    }

    public function bodyConfirmUser($link){
        return 'Voici le lien de confirmation ( valable 24 h ) de votre compte myRecode  <a href="'.$link.'">'. $link.'</a>' ;
    }

    public function bodyResetPassword($link){
        return 'Voici le lien de confirmation ( valable 24 h ) pour la résiliation de votre mot de passe  <a href="'.$link.'">'. $link.'</a>' ;
    }

    public function renderBody($header , $body , $signature){
            return $header . $body . $signature ; 
    }
	
}