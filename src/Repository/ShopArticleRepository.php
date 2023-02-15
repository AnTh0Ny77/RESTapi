<?php

namespace Src\Repository;

require  '././vendor/autoload.php';

use DateTime;
use Src\Database;
use PDO;
use Src\Repository\BaseRepository;
use Src\Entities\Client;
use Src\Services\ResponseHandler;

class ShopArticleRepository  extends BaseRepository{


    public function findAll()
    {
        $clause = 'SELECT sar__ref_id , sar__ref_constructeur , sar__description , sar__marque , k.kw__lib as famille , sar__image from shop_article
        LEFT JOIN keyword as k ON ( sar__famille = k.kw__value )  
        where 1 =1 limit 8000';
        $request = $this->Db->Pdo->query($clause);
        return  $request->fetchAll(PDO::FETCH_ASSOC);
    }
}
