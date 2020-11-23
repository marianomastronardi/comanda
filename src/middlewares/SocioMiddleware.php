<?php

namespace App\Middlewares;

use App\Controllers\UserController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Seguridad\iToken;
use App\Models\User;
use App\Models\Persona;
use App\Models\Socio;
use App\Models\Puesto;

class SocioMiddleware
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
            $user = User::find($jwt["email"]);
            $persona = Persona::where('email', $user->email)->get();
            $id = $persona[0]['id'];
            $socio = Socio::where('persona_id', $id)->get();

            if (!isset($socio[0]['persona_id'])) {
                $resp = new Response();
                $resp->getBody()->write(json_encode(array('message' => 'Partner does not exist')));
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
