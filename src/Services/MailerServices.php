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
            $mail->setFrom('myrecode@recode.fr', 'MyRecode');
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
        $imageData = base64_encode(file_get_contents('public/img/LOGO.png'));
        $src = 'data: '.mime_content_type('public/img/LOGO.png').';base64,'.$imageData;

        return '<img width="150" height="auto" src="http://drive.google.com/uc?export=view&id=1-7IjI_qkbEb-ufKfmfPypxeg5Kssn6l8" style="display:block;" width="200" height="87"   alt="Logo" title="Logo" ><br><br>';
    }
    
    public function signature(){
            return '';
    }

    public function bodyConfirmUser($link){
        return '
            <div class="wrapper">
                <p style="text-align: center;"><!--StartFragment--><span style="font-size:14px"><span style="font-weight:bold">Validation de votre <br />
                    EMAIL - MY RECODE</span></span>
                    <br/>
                    &nbsp;
                </p>
                    <p style="text-align: center;">Pour confirmer votre adresse email, utiliser simplement le bouton ci-dessous<br/>
                    <br />
                    <br />
                    <a  style=" padding-left: 24px; padding-right: 24px;padding-top: 12px; font-weight:bold; padding-bottom: 12px;background: #1FB447;color: white; border-radius: 16px; text-decoration: none;" target="_blank" href="'.$link.'"><span>Je confirme mon adresse email </span></a><br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <span style="font-size:14px" style="font-weight:bold">A tout de suite sur votre espace client.</span><br />
                    <span style="font-size:14px" style="font-weight:bold">L equipe RECODE !</span>
                </p>
            </div>';
    }

    public function bodyMail($text)
    {
        return '
            <div class="wrapper">
                <p style="text-align: center;"><!--StartFragment--><span style="font-size:14px"><span style="font-weight:bold">
                    </span></span>
                    <br/>
                    &nbsp;
                </p>
                    <p style="text-align: center;">'. $text.'<br/>
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <span style="font-size:14px" style="font-weight:bold">A tout de suite sur votre espace client.</span><br />
                    <span style="font-size:14px" style="font-weight:bold">L equipe RECODE !</span>
                </p>
            </div>';
    }

    public function bodyResetPassword($link){
        return '<style>
                .success-link{
                    padding-left: 24px;
                    padding-right: 24px;
                    padding-top: 12px;
                    padding-bottom: 12px;
                    background: #1FB447;
                    color: white;
                    border-radius: 16px;
                }
                .wrapper{
                    margin-top: 50px;
                    margin-bottom: 50px;
                }
                a:link { text-decoration: none; }
            
                a:visited { text-decoration: none; }
            
                a:hover { text-decoration: none; }
            
                a:active { text-decoration: none; }
            </style>
            <div class="wrapper">
                <p style="text-align: center;"><!--StartFragment--><span style="font-size:14px"><span style="font-weight:bold">R??initialisation de votre<br />
                    MOT DE PASSE - MY RECODE</span></span>
                    <br/>
                    &nbsp;
                </p>
                    <p style="text-align: center;">Pour d??finir un nouveau mot de passe, utilisez simplement le bouton ci-dessous<br />
                    <br />
                    <br />
                    <a  class="success-link" target="_blank" href="'.$link.'"><span>Je r??initialise mon mot de passe </span></a><br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <span style="font-size:16px" style="font-weight:bold">A tout de suite sur votre espace client.</span><br />
                    <span style="font-size:16px" style="font-weight:bold">L ??quipe RECODE !</span>
                </p>
            </div>';
    }

    public function renderBody($header , $body , $signature){
            return $header . $body . $signature ; 
    }

    public function renderBodyTicketEnvoi($id, $user){
        return '<style>
                .success-link{
                    padding-left: 24px;
                    padding-right: 24px;
                    padding-top: 12px;
                    padding-bottom: 12px;
                    background: #1FB447;
                    color: white;
                    border-radius: 16px;
                }
                .wrapper{
                    margin-top: 50px;
                    margin-bottom: 50px;
                }
                a:link { text-decoration: none; }
            
                a:visited { text-decoration: none; }
            
                a:hover { text-decoration: none; }
            
                a:active { text-decoration: none; }
            </style>
            <div class="wrapper">
                <p style="text-align: center;"><!--StartFragment--><span style="font-size:14px"><span style="font-weight:bold">Votre Ticket '.$id.'<br />
                   MY RECODE </span></span>
                    <br/>
                    &nbsp;
                </p>
                    <p style="text-align: center;">Votre Ticket ?? bien ??t?? transmis ?? '. $user .'. <br /> Vous serez inform?? par e-mail en cas de r??ponse
                    <br />
                    <br />
                   
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <span style="font-size:16px" style="font-weight:bold">A tout de suite sur votre espace client.</span><br />
                    <span style="font-size:16px" style="font-weight:bold">L ??quipe RECODE !</span>
                </p>
            </div>';
    } 

    public function renderBodyTicketDest($id , $user){
        return '<style>
                .success-link{
                    padding-left: 24px;
                    padding-right: 24px;
                    padding-top: 12px;
                    padding-bottom: 12px;
                    background: #1FB447;
                    color: white;
                    border-radius: 16px;
                }
                .wrapper{
                    margin-top: 50px;
                    margin-bottom: 50px;
                }
                a:link { text-decoration: none; }
            
                a:visited { text-decoration: none; }
            
                a:hover { text-decoration: none; }
            
                a:active { text-decoration: none; }
            </style>
            <div class="wrapper">
                <p style="text-align: center;"><!--StartFragment--><span style="font-size:14px"><span style="font-weight:bold">Votre Ticket '.$id.'<br />
                MY RECODE </span></span>
                    <br/>
                    &nbsp;
                </p>
                    <p style="text-align: center;"> '. $user .'. <br /> vous ?? r??pondu !
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <span style="font-size:16px" style="font-weight:bold">A tout de suite sur votre espace client.</span><br />
                    <span style="font-size:16px" style="font-weight:bold">L ??quipe RECODE !</span>
                </p>
            </div>';

    }
	
}