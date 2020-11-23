<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use App\Models\Persona;
use App\Models\Cliente;
use App\Models\User;

class ClienteController
{

    private $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $rta = Cliente::select('clientes.id', 'personas.id', 'personas.nombre', 'personas.apellido', 'personas.email')
                    ->join('personas', 'personas.id', '=', 'clientes.persona_id')
                    ->get();
        //$body = $request->getParsedBody();
        $response->getBody()->write(json_encode($rta));
        return $response; //->withHeader('Content-Type', 'application/json');
    }

    public function getOneById(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $args["id"];
        $socio = Cliente::select('clientes.id', 'clientes.persona_id', 'personas.nombre', 'personas.apellido', 'personas.email')
                    ->join('personas', 'personas.id', '=', 'clientes.persona_id')
                    ->where('clientes.id', $id)
                    ->get();
        //var_dump($socio)[0];
        if ($socio !== null) {
            $response->getBody()->write(json_encode($socio));
        } else {
            $response->getBody()->write(json_encode(array('message' => 'Partner does not exist')));
        }
        return $response; //->withHeader('Content-Type', 'application/json');
       }

    public function getOneByName(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        if (isset($args["nombre"]) && isset($args["apellido"])) {
            $persona = Persona::where('nombre', $args["nombre"])->where('apellido', $args["apellido"])->first();
        } elseif (isset($args["nombre"])) {
            $persona = Persona::where('nombre', $args["nombre"])->first();
        } elseif (isset($args["apellido"])) {
            $persona = Persona::where('apellido', $args["apellido"])->first();
        } else {
            $response->getBody()->write(json_encode(array('message' => 'sin parametros definidos')));
        }
        if (isset($persona)) {
            $socio = Cliente::where('id_persona', $persona->id)->with('persona')->first();
            $response->getBody()->write(json_encode($socio));
        } else {
            $response->getBody()->write(json_encode(array('message' => 'Partner does not exist')));
        }

        return $response; //->withHeader('Content-Type', 'application/json');
    }

    public function add(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $nombre = $body["nombre"]??'';
        $apellido = $body["apellido"]??'';
        $email = $body["email"]??'';

        if(strlen($nombre) > 0){
            if(strlen($apellido) > 0){
                if(strlen($email) > 0){
                    $user = User::find($email);
                    if ($user == null) {
                        $response->getBody()->write(json_encode(array('message' => 'Email does not exist')));
                    }else{
                        $persona = Persona::where('nombre', $nombre)->where('apellido', $apellido)->get();
                        if (!isset($persona[0])) {
                            //no existe la persona
                            $model = new Persona;
                            $model->nombre = $nombre;
                            $model->apellido = $apellido;
                            $model->email = $email;
                            $model->save();
                            $persona = Persona::where('nombre', $body["nombre"])->where('apellido', $body["apellido"])->get();
                        }
                        $socio = new Cliente;
                        $socio->persona_id = $persona[0]['id'];
                        $socio->save();
                        $response->getBody()->write('Customer has been saved');
                    }
                }else{
                    $response->getBody()->write(json_encode(array('message' => 'Email is required')));
                }
            }else{
                $response->getBody()->write(json_encode(array('message' => 'Apellido is required')));
            }
        }else{
            $response->getBody()->write(json_encode(array('message' => 'Nombre is required')));
        }
        return $response; //->withHeader('Content-Type', 'application/json');
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $socio = Cliente::find($args["id"]);
        if (isset($socio)) {
            $persona = Persona::find($socio->id_persona);
            $body = $request->getParsedBody();
            if (isset($body["nombre"])) $persona->nombre = $body["nombre"];
            if (isset($body["apellido"])) $persona->apellido = $body["apellido"];
            $persona->save();
            $response->getBody()->write(array('message' => 'Partner Updated'));
        } else {
            $response->getBody()->write(array('message' => 'Partner does not exist'));
        }
        return $response; //->withHeader('Content-Type', 'application/json');
    }
}
