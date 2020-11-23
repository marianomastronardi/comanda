<?php

namespace App\Middlewares;

use App\Controllers\UserController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Seguridad\iToken;

class AdminMiddleware
{

    public function __invoke(Request $request, RequestHandler $handler)
    {

        $arr = $request->getHeader('token');
        if (count($arr) > 0) $token = $arr[0];
        $jwt = isset($token) ? iToken::decodeUserToken($token) : false; // VALIDAR EL TOKEN

        if (!$jwt) {
            $response = new Response();

            $rta = array("rta" => "No tiene permisos");

            $response->getBody()->write(json_encode($rta));

            return $response;
        } else {
            if ($jwt["email"] !== 'admin@gmail.com') {
                $resp = new Response();
                $resp->getBody()->write(json_encode(array('message' => 'You are not an admin user')));
                return $resp;
            }else{
                $response = $handler->handle($request);
                $existingContent = (string) $response->getBody();

                $resp = new Response();
                $resp->getBody()->write($existingContent);
    
                return $resp;
            }
        }
    }
}
