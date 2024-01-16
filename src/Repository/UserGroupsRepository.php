<?php
namespace Src\Repository;
require  '././vendor/autoload.php';

use DateTime;
use Src\Database;
use PDO;
use Src\Repository\BaseRepository;
use Src\Repository\ClientRepository;
use Src\Entities\Client;
use Src\Services\ResponseHandler;

Class UserGroupsRepository  extends BaseRepository {

    public function getUserGroups($user__id){
        $clients = $this->findBy(['lug__user__id' => $user__id ], 50 , [ 'lug__user__id' => 'ASC'] );
        $responses = [];
        foreach ($clients as $value) {
            
            array_push($responses , $value['lug__user__grp']);
        }
        array_push($responses , $user__id);
        return $responses;
    }

}