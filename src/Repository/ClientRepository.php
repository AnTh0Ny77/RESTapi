<?php
namespace Src\Repository;
require  '././vendor/autoload.php';

use DateTime;
use Src\Database;
use PDO;
use Src\Repository\BaseRepository;
use Src\Entities\Client;
use Src\Services\ResponseHandler;

Class ClientRepository  extends BaseRepository {

    public function postClient($client_data){

        $verifyIfExist =  $this->findOneBy(['cli__nom' => $this->clean($client_data['cli__nom'])] , true );
        if ($verifyIfExist instanceof Client) 
            return 'une société possède deja ce nom';
       
        $client = New Client();

        $nom = $client->setCli__nom( $this->cleanKeepSpace($client_data['cli__nom']));
        if (!$nom instanceof Client) 
            return $nom;

        $adr1 = $client->setCli__adr1($client_data['cli__adr1']);
        if (!$adr1 instanceof Client) 
            return $adr1;

        $adr2 = $client->setCli__adr2($client_data['cli__adr2']);
        if (!$adr2 instanceof Client) 
            return $adr2;

        $cp = $client->setCli__cp($client_data['cli__cp']);
        if (!$cp instanceof Client) 
            return $cp;

        $ville = $client->setCli__ville($this->cleanKeepSpace($client_data['cli__ville']));
        if (!$ville instanceof Client) 
            return $ville;

        $pays = $client->setCli__pays($client_data['cli__pays']);
        if (!$pays instanceof Client) 
            return $pays;

        $tel = $client->setCli__tel( $client_data['cli__tel']);
        if (!$tel instanceof Client) 
            return $tel;

        $mail = $client->setCli__email($client_data['cli__email']);
        if (!$mail instanceof Client) 
            return $mail;

        $client_data['cli__nom'] = mb_strtoupper($this->cleanKeepSpace($client_data['cli__nom']));
        $client_data['cli__ville'] = mb_strtoupper($this->cleanKeepSpace($client_data['cli__ville']));

        $id_client = $this->insert($client_data);
        $client = $this->findOneBy(['cli__id' =>  $id_client] , true );
        return$client;
        
    }

    public function UpdateOne($client_data){

        if (empty($client_data['cli__id'])) 
            return 'Le champs cli__id doit etre rendeigné';
        
        $verifyIfExist =  $this->findOneBy(['cli__id' => $this->clean($client_data['cli__id'])] , true );
        if (!$verifyIfExist instanceof Client) 
            return 'Le client n existe pas ';

            $client = New Client();

            if ($client_data['cli__nom']) {
                $nom = $client->setCli__nom( $this->cleanKeepSpace($client_data['cli__nom']));
                if (!$nom instanceof Client) 
                    return $nom;

                $client_data['cli__nom'] = mb_strtoupper($this->cleanKeepSpace($client_data['cli__nom']));
            }
           
            if (!empty($client_data['cli__adr1'])) {
                $adr1 = $client->setCli__adr1($client_data['cli__adr1']);
                if (!$adr1 instanceof Client) 
                    return $adr1;
            }

            if (!empty($client_data['cli__adr2'])) {
                $adr2 = $client->setCli__adr2($client_data['cli__adr2']);
                if (!$adr2 instanceof Client) 
                    return $adr2;
            }
           
            if (!empty($client_data['cli__cp'])) {
                $cp = $client->setCli__cp($client_data['cli__cp']);
                if (!$cp instanceof Client) 
                    return $cp;
            }
           
            if (!empty($client_data['cli__ville'])) {
                $ville = $client->setCli__ville($client_data['cli__ville']);
                if (!$ville instanceof Client) 
                    return $ville;
                
                $client_data['cli__ville'] = mb_strtoupper($this->cleanKeepSpace($client_data['cli__ville']));
            }
            
            if (!empty($client_data['cli__pays'])) {
                $pays = $client->setCli__pays($client_data['cli__pays']);
                if (!$pays instanceof Client) 
                    return $pays;
            }

            if (!empty($client_data['cli__tel'])) {
                $tel = $client->setCli__tel( $client_data['cli__tel']);
                if (!$tel instanceof Client) 
                    return $tel;
            }
            
    
            $id_client = $this->update($client_data);
            $client = $this->findOneBy(['cli__id' =>  $client_data['cli__id']] , true );
            return $client;
    }

}