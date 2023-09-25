<?php

namespace Src\Repository;

require  '././vendor/autoload.php';

use DateTime;
use Src\Database;
use PDO;
use Src\Repository\BaseRepository;
use Src\Entities\Client;
use Src\Services\ResponseHandler;

class ShopCmdRepository  extends BaseRepository{
    
    public function updateFromSossuke($id , $sossuke_id){
        $request = $this->Db->Pdo->prepare('UPDATE shop_cmd SET scm__id = '.$sossuke_id.' WHERE scm__id = '.$id.'  ');
        $request->execute();
    }
}
