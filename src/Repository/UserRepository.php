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
        
        $id_user = $this->insert($user_data);
        
        $user = $this->findOneBy(['user__id' =>  $id_user]);
        return $user;
    }

    public function loginUser($user_data){
        if (empty($user_data['user__password'])) 
            return 'Le champ password ne peut pas etre vide.';

        if (empty($user_data['user__mail'])) 
            return 'Le champ mail ne peut pas etre vide.';

        $user = $this->findOneBy(['user__mail' =>  $user_data['user__mail']]);

        if (!$user instanceof User) 
            return 'Utilisateur inconnu.';

        var_dump($user);
        $password_authenticity = password_verify($user_data['user__password'],$user->getUser__password());

        var_dump($password_authenticity);
    }

}