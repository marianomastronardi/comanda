<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use App\Models\Persona;
use App\Models\Socio;

class SocioController
{

    private $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $rta = Socio::get();
        //$body = $request->getParsedBody();
        $response->getBody()->write(json_encode($rta));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getOneById(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $socio = Socio::find($args["id"]);
        if($socio)$response->getBody()->write(json_encode($socio));
        return $response->withHeader('Content-Type', 'application/json');
        return $socio;
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
            json_encode(array('message' => 'sin parametros definidos'));
        }
        if (isset($persona)) $socio = Socio::where('id_persona', $persona->id)->first();
        $response->getBody()->write(json_encode($socio));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function add(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $persona = Persona::where('nombre', $body["nombre"])->where('apellido', $body["apellido"])->first();
        if(!isset($persona)){
            //no existe la persona
            $model = new Persona;
            $model->nombre = $body["nombre"];
            $model->apellido = $body["apellido"];
            $model->save();
            $persona = Persona::where('nombre', $body["nombre"])->where('apellido', $body["apellido"])->first();
        }
        $socio = new Socio;
        $socio->id_persona = $persona->id;
        $socio->save();
        $response->getBody()->write('Partner Saved');

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $socio = Socio::find($args["id"]);
        if(isset($socio)){
            $persona = Persona::find($socio->id_persona);
            $body = $request->getParsedBody();
            if(isset($body["nombre"])) $persona->nombre = $body["nombre"];
            if(isset($body["apellido"])) $persona->apellido = $body["apellido"];
            $persona->save();
            $response->getBody()->write('Partner Updated');
        }
        return $response->withHeader('Content-Type', 'application/json');
    } 

}
