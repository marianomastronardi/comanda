<?php
namespace Seguridad;

//require_once '../vendor/autoload.php';
use \Firebase\JWT\JWT;

class iToken{
  
static $_key = "pro3-comanda";

function __get($name)
{
    return $this->_key;
}

static function encodeUserToken($email, $pass)
{
    $payload = array(
        "iss" => "http://example.org",
        "aud" => "http://example.com",
        "iat" => 1356999524,
        "nbf" => 1357000000,
        "email" => $email,
        "password" => $pass
    );
    
 
    $jwt = JWT::encode($payload, iToken::$_key);
    return $jwt;
}   

static function decodeUserToken($jwt){
    try {
        $decoded = JWT::decode($jwt, iToken::$_key, array('HS256'));
    return (array) $decoded;
    } catch (\Throwable $th) {
        echo "Signature Invalid";
        return false;
    }
    
}
//$decoded_array = (array) $decoded;

//JWT::$leeway = 60; // $leeway in seconds
//$decoded = JWT::decode($jwt, $key, array('HS256'));
}
?>