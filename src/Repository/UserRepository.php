<?php
namespace Src\Repository;
require  '././vendor/autoload.php';

use DateTime;
use Src\Database;
use PDO;
use Src\Repository\BaseRepository;
use Src\Repository\RoleRepository;
use Src\Entities\User;
use Src\Services\ResponseHandler;

Class UserRepository  extends BaseRepository{

    public function encrypt_password($pass){
        return  password_hash($pass, PASSWORD_DEFAULT);
    }

    public function postUser($user_data){

        $roleRepository = new RoleRepository($this->Db);
        $user = new User();
       
        $pass  = $user->setUser__password($user_data['user__password']);
        if (!$pass instanceof User) 
            return $pass;

        $user_data['user__password'] = $this->encrypt_password($user_data['user__password']);

        $mail =  $user->setUser__mail($user_data['user__mail']);
        if (!$mail instanceof User) 
            return $mail;
        
        $mail = $this->findOneBy(['user__mail' =>  $user_data['user__mail']] , true);

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
        $roleRepository->insert(['ur__user_id' => $id_user , 'ur__role' => 'USER' ]);
        $user = $this->findOneBy(['user__id' =>  $id_user] , true );
        $user = $this->getRole($user);
        return $user;
    }



    public function UpdateUser($user_data)
    {

        $user = new User();

        // $mail =  $user->setUser__mail($user_data['user__mail']);
        // if (!$mail instanceof User)
        //     return $mail;

        // $mail = $this->findOneBy(['user__mail' =>  $user_data['user__mail']], true);

        // if ($mail instanceof User and $mail->getUser__id() !=  $user_data['user__id'])
        //     return 'cet email est déja utilisé.';

        $this->update($user_data);
        $user = $this->findOneBy(['user__id' =>  $user_data['user__id']], true);
        return $user;
    }

    public function getRole( User $user){
        
        $roleRepository = new  RoleRepository($this->Db);
        $roles = [];
        $arrayRoles = $roleRepository->findBy(['ur__user_id' =>  $user->getUser__id()] , 50 , ['ur__role' => 'ASC' ]);
        
        foreach ($arrayRoles as $key => $value) {
            array_push($roles ,  $value['ur__role']);
        }
        $user->setRoles($roles);
      
        return $user;
    } 

    public function loginUser($user_data){
        if (empty($user_data['user__password'])) 
            return 'Le champ password ne peut pas etre vide.';

        if (empty($user_data['user__mail'])) 
            return 'Le champ mail ne peut pas etre vide.';
       
        $user = $this->findOneBy(['user__mail' =>  $user_data['user__mail']] , false);
        
        if (empty($user)) 
            return 'Identifiants invalides.';

        $password_authenticity = password_verify($user_data['user__password'],$user['user__password']);
        
        if ($password_authenticity == false )
             return 'Identifiants invalides.';

        $user = $this->findOneBy(['user__mail' =>  $user_data['user__mail']] , true);
        
        $user = $this->getRole($user);
        
        return $user;
        
    }

   

}