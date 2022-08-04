<?php
namespace Src\Repository;
require  '././vendor/autoload.php';

use DateTime;
use Src\Database;
use PDO;
use Src\Repository\BaseRepository;
use Src\Entities\User;
use Src\Services\ResponseHandler;

Class UserRepository  extends BaseRepository{

    public function encrypt_password($pass){
        return  password_hash($pass, PASSWORD_DEFAULT);
    }

    public function postUser($user_data){

        $user = new User();
       
        $pass  = $user->setUser__password($user_data['user__password']);
        if (!$pass instanceof User) 
            return $pass;

        $user_data['user__password'] = $this->encrypt_password($user_data['user__password']);

        $mail =  $user->setUser__mail($user_data['user__mail']);
        if (!$mail instanceof User) 
            return $mail;
        
        $mail = $this->findOneBy(['user__mail' =>  $user_data['user__mail']]);

        if ($mail instanceof User) 
            return 'vous possédez deja un compte pour cet email.';
       
        $prenom = $user->setUser__prenom($user_data['user__prenom']);

        if (!$prenom instanceof User) 
            return $prenom;
        
        $nom = $user->setUser__nom($user_data['user__nom']);

        if (!$nom instanceof User) 
            return $nom;

        $user_data['user__nom'] = strtoupper($user_data['user__nom']);

        $user_data['user__d_creat'] = date('Y-m-d H:i:s');
        
        $this->insert($user_data);
    }

}