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
            $mail->CharSet = 'UTF-8';                          
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

        return '<img width="150"  src="https://myrecode.fr/img/logo_myrecode.png" style="display:block;"  alt="MyRecode" title="MyRecode" ><br><br>';
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
                <p style="text-align: center;"><!--StartFragment--><span style="font-size:14px"><span style="font-weight:bold">Réinitialisation de votre<br />
                    MOT DE PASSE - MY RECODE</span></span>
                    <br/>
                    &nbsp;
                </p>
                    <p style="text-align: center;">Pour définir un nouveau mot de passe, utilisez simplement le bouton ci-dessous<br />
                    <br />
                    <br />
                    <a  class="success-link" target="_blank" href="'.$link.'"><span>Je réinitialise mon mot de passe </span></a><br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <span style="font-size:16px" style="font-weight:bold">A tout de suite sur votre espace client.</span><br />
                    <span style="font-size:16px" style="font-weight:bold">L équipe RECODE !</span>
                </p>
            </div>';
    }


    public function bodyNewPassword($link)
    {
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
                <p style="text-align: center;"><!--StartFragment--><span style="font-size:14px"><span style="font-weight:bold">Bienvenue chez<br />
                    MY RECODE</span></span>
                    <br/>
                    &nbsp;
                </p>
                    <p style="text-align: center;">Pour définir un nouveau mot de passe, utilisez simplement le bouton ci-dessous<br />
                    <br />
                    <br />
                    <a  class="success-link" target="_blank" href="' . $link . '"><span>je crée mon mot de passe</span></a><br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <span style="font-size:16px" style="font-weight:bold">A tout de suite sur votre espace client.</span><br />
                    <span style="font-size:16px" style="font-weight:bold">L équipe RECODE !</span>
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
                    <p style="text-align: center;">Votre Ticket à bien été transmis à '. $user .'. <br /> Vous serez informé par e-mail en cas de réponse
                    <br />
                    <br />
                   
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <span style="font-size:16px" style="font-weight:bold">A tout de suite sur votre espace client.</span><br />
                    <span style="font-size:16px" style="font-weight:bold">L équipe RECODE !</span>
                </p>
            </div>';
    } 


    public function renderBodyCommande($cmd, $ligne){

        $table_ligne = '';
        foreach ($ligne as $key => $value) {
            $table_ligne .= '<tr>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
            </tr>';
        }
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
                <p style="text-align: center;"><!--StartFragment--><span style="font-size:14px"><span style="font-weight:bold">Récapitulatif de votre commande  MY RECODE  <br /> '.$cmd['scm__id'].'
                   </span></span>
                    <br/>
                    &nbsp;
                </p>
                    <br />
                    <br />
                    <br />
                    <table>
                        '. $ligne .'
                    </table>
                    <br />
                    <br />
                    <br />
                    <br />
                    <span style="font-size:16px" style="font-weight:bold">A tout de suite sur votre espace client.</span><br />
                    <span style="font-size:16px" style="font-weight:bold">L équipe RECODE !</span>
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
                    <p style="text-align: center;"> '. $user .'. <br /> vous à répondu !
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <br />
                    <span style="font-size:16px" style="font-weight:bold">A tout de suite sur votre espace client.</span><br />
                    <span style="font-size:16px" style="font-weight:bold">L équipe RECODE !</span>
                </p>
            </div>';

    }
	
}