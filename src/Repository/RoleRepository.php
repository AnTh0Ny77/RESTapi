<?php
namespace Src\Repository;
require  '././vendor/autoload.php';

use DateTime;
use Src\Database;
use Src\Repository\BaseRepository;
use PDO;
use Src\Services\ResponseHandler;

Class RoleRepository  extends BaseRepository {
    public string $Table;
    public  $Db;

    public function __construct($db){
        $this->Table = 'user_role';
        $this->Db = $db;
    }

}