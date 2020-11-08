<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use App\Models\Persona;

class PersonaController
{

    private $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $rta = Persona::get();
        //$body = $request->getParsedBody();
        $response->getBody()->write(json_encode($rta));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getOneById(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $persona = Persona::find($args["id"]);
        if($persona)$response->getBody()->write(json_encode($persona));
        return $response->withHeader('Content-Type', 'application/json');
        return $persona;
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
        if (isset($persona)) $response->getBody()->write(json_encode($persona));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function add(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $persona = new Persona;
        $persona->nombre = $body["nombre"];
        $persona->apellido = $body["apellido"];
        $existe = Persona::where('nombre', $body["nombre"])->where('apellido', $body["apellido"])->first();
        if(!isset($existe))$persona->save();
        (!isset($existe)) ? $response->getBody()->write('Person Saved') : $response->getBody()->write('Person could not be saved');
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $persona = Persona::find($args["id"]);
        if(isset($persona)){
            $body = $request->getParsedBody();
            if($body["nombre"]) $persona->nombre = $body["nombre"];
            if($body["apellido"]) $persona->apellido = $body["apellido"];
            $persona->save();
            $response->getBody()->write('Person Updated');
        }
        return $response->withHeader('Content-Type', 'application/json');
    } 

}
