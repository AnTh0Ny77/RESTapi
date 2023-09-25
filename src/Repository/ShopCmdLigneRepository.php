<?php

namespace Src\Repository;

require  '././vendor/autoload.php';

use DateTime;
use Src\Database;
use PDO;
use Src\Repository\BaseRepository;
use Src\Entities\Client;
use Src\Services\ResponseHandler;

class ShopCmdLigneRepository  extends BaseRepository{

    public function updateFromSossuke($id , $sossuke_id){
        $request = $this->Db->Pdo->prepare('UPDATE shop_cmd_ligne SET scl__scm_id = '.$sossuke_id.' WHERE scl__scm_id = '.$id.'  ');
        $request->execute();
    }
}
