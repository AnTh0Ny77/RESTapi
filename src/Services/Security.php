<?php
namespace Src\Services;
require  '././vendor/autoload.php';
use ReallySimpleJWT\Token;

Class Security {

    public $config;

    public function __construct(){
        $this->config = json_decode(file_get_contents('config.json'));
    }

	public function returnToken( int $user__id){
        $tokens = new Tokens();
        $payload = [
            'iat' => time(),
            'uid' => $user__id,
            'exp' => time() + 3600
        ];
        $secret = $this->config->security->app_secret;
        return Token::customPayload($payload, $secret);
    }

    public function verifyToken($token){
        return Token::validate($token, $this->config->security->app_secret);
    }


    
}