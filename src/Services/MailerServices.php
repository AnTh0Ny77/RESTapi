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


    public function bodyNewPassword($link){
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
                    width: 350px;
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

    public function renderBodyCommande($cmd, $ligne , $nom_client , $user){

        $table_ligne = '';
        $total = 0 ;
        foreach ($ligne as $key => $value) {
            $total_ligne = intval($value['scl__qte']) * floatval($value['scl__prix_unit']);
            $gar= '';
            if (!empty($value['scl__gar_mois'])) {
                //incorporer une nouvelle ligne sup :  
                $gar= ' <br> garantie ' .$value['scl__gar_mois'] . ' mois ';
                $gar_prix = intval($value['scl__qte']) * floatval($value['scl__gar_prix']);
                //$total_ligne +=  $gar_prix;
            }
            $total += $total_ligne;
            $total += $gar_prix;
            $table_ligne .= '<tr>

                    <td width="47%">
                    <b>'. $value['temp']['sar__marque'] . ' ' . $value['temp']['sar__model'] . '</b><br>
                    REF '. $value['temp']['sar__ref_constructeur'] . $gar . ' 
                    </td>

                    <td>
                      <b> Qté  '.$value['scl__qte'].' </b>
                    </td>

                    <td>
                       <b> '.number_format($total_ligne, 2, ',', ' '). ' € HT </b><br>
                       '.number_format($gar_prix, 2, ',', ' '). ' € HT
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
                    margin: 50px auto 50px;
                    width: 350px;
                    text-align : center;
                }
                a:link {text-decoration: none;}
            
                a:visited {text-decoration: none;}
            
                a:hover {text-decoration: none;}
            
                a:active {text-decoration: none;}

            </style>
            <span  style=" width: 350px; margin: 50px auto 50px; ">
                <p style="text-align: center;"><!--StartFragment--><span style="font-size:14px"><span style="font-weight:bold">Récapitulatif de votre commande  MY RECODE  <br /> '.$cmd['scm__id'].'
                   </span></span>
                    <br/>
                    &nbsp;
               </p>
               <p>
                Societe : '. $nom_client.'<br>
                Passée par : '. $user->getUser__nom().'  '.$user->getUser__prenom() .'<br>
                Ref : '. $cmd['scm__ref_client'].'<br>
               </p>
                    <br />
                    <br />
                    <br />
                    <table style="border-spacing: 5px 10px 10px; margin: auto;" >
                        '.$table_ligne .'
                        <tr>
                            <td>
                        
                            </td>
        
                            <td>
                                <b>Sous-total </b>
                            </td>
        
                            <td>
                                <b>'. number_format($total, 2, ',', ' '). ' € HT </b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                        
                            </td>
        
                            <td>
                                Frais de livraison
                            </td>
        
                            <td>
                                '.number_format($cmd['scm__prix_port'] , 2 , ',' , ' '). ' € HT 
                            </td>
                        </tr>
                        <tr>
                            <td>
                        
                            </td>
        
                            <td>
                               <b> Total </b>
                            </td>
        
                            <td>
                              <b>  '.number_format($total + floatval($cmd['scm__prix_port']), 2 ,',' , ' ') . ' € HT  </b>
                            </td>
                        </tr>
                    </table>
                    <br />
                    <br />
                    <br />
                    <br />
                    <span style="font-size:16px" style="font-weight:bold;  margin: auto; text-align: center;">Pour suivre l etat de votre commande livrée ou facturée</span><br />
                    <span style="font-size:16px" style="font-weight:bold;  margin: auto; text-align: center;">rendez-vous dans l onglet "Mes documents"</span>
            </span>';
    } 


    public function renderBodyCommande2($cmd, $ligne){

        $table_ligne = '';
        $total = 0 ;
        foreach ($ligne as $key => $value) {
            $total_ligne = intval($value['scl__qte']) * floatval($value['scl__prix_unit']);
            $gar= '';
            if (!empty($value['scl__gar_mois'])) {
                //incorporer une nouvelle ligne sup :  
                $gar= ' <br> garantie ' .$value['scl__gar_mois'] . ' mois ';
                $gar_prix = intval($value['scl__qte']) * floatval($value['scl__gar_prix']);
                //$total_ligne +=  $gar_prix;
            }
            $total += $total_ligne;
            $total += $gar_prix;
            $table_ligne .= '<tr>
                            <td><b>'. $value['temp']['sar__marque'] . ' ' . $value['temp']['sar__model'] . '</b> REF '. $value['temp']['sar__ref_constructeur'] . $gar . ' mois</td>
                            <td align=center><b> Qte  '.$value['scl__qte'].'</b></td>
                            <td><b>'.number_format($gar_prix, 2, ',', ' '). ' € HT</b><br>'.number_format($gar_prix, 2, ',', ' '). ' € HT</td>
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
                    margin: 50px auto 50px;
                    width: 350px;
                    text-align : center;
                }
                a:link {text-decoration: none;}
            
                a:visited {text-decoration: none;}
            
                a:hover {text-decoration: none;}
            
                a:active {text-decoration: none;}

            </style>
            <img width="150" src="https://myrecode.fr/img/logo_myrecode.png" style="display:block;" alt="MyRecode" title="MyRecode" ><br><br>
            <p style="text-align: center;">
            <span style="font-size:24px">
            <b>Récapitulatif de votre commande MY RECODE <br/> 3241509 </b>
            </span>
            <br><br><br>

            <table style="border-spacing: 5px 10px 10px; margin: auto; border: 1px solid black; border-collapse : collapse;" >
                 '.$table_ligne.'
                 <tr>
                    <td></td>
                    <td><b>Sous-total </b></td>
                    <td><b>'. number_format($total, 2, ',', ' '). ' € HT</b></td>
                    </tr>
                    <tr>
                    <td></td>
                    <td>Frais de livraison</td>
                    <td>'.number_format($cmd['scm__prix_port'] , 2 , ',' , ' '). ' € HT</td>
                    </tr>
                    <tr>
                    <td></td>
                    <td><b> Total </b></td>
                    <td><b>'.number_format($total + floatval($cmd['scm__prix_port']), 2 ,',' , ' ') . ' € HT</b></td>
                    </tr>
                 <tr style="border: 1px solid black;" >
                    <td COLSPAN=3>Livraison : nom société + CP + ville<br>Contact : xyxyxyxy (contact de livraison)</td>
                </tr>
            </table>

            <br><br><br><br>

            <span style="font-size:16px">Pour suivre l etat de votre commande livrée ou facturée<br>rendez-vous dans l onglet "Mes documents" sur MyRecode.fr</span>';
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

    public function RenderbodyAbsence($user , $type , $motif , $date , $dateR){
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
                <p style="text-align: center;"><!--StartFragment--><span style="font-size:14px"><span style="font-weight:bold">Demande<br />
                    D absence</span></span>
                    <br/>
                    &nbsp;
                </p>
                    <p style="text-align: center;"> '.$user.' à effectué une demande d abscence pour le motif suivant : 
                    <br />
                    <br />
                        '.$type.' : '.$motif.'
                    <br />
                        Du  : '.$date.'
                    <br />
                        Au  : '.$dateR.'
                    <br />
                </p>
            </div>';
    }


    public function RenderbodyAnnulAbsence($user , $type , $motif , $date , $dateR){
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
                <p style="text-align: center;"><!--StartFragment--><span style="font-size:14px"><span style="font-weight:bold">ANNULATION<br />
                    D absence</span></span>
                    <br/>
                    &nbsp;
                </p>
                    <p style="text-align: center;"> '.$user.' à annulé/refusé la demande d absence pour le motif suivant  : 
                    <br />
                    <br />
                        '.$type.' : '.$motif.'
                    <br />
                        Du  : '.$date.'
                    <br />
                        Au  : '.$dateR.'
                    <br />
                </p>
            </div>';
    }


    public  function renderMotifString($select){
        switch ($select) {
            case 'CP':
                return 'congés payés';
                break;
            case 'NP':
                return 'non payés';
                break;
            case 'MLD':
                return 'Maladie';
                break;
            case 'INT':
                return 'intervention';
                break;
            case 'RCU':
                return 'récupération';
                break;
            case 'TT':
                return 'télétravail';
                break;
        }
    }
}