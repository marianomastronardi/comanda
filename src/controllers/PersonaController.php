<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use App\Models\Persona;
use App\Models\User;

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
        return $response; //->withHeader('Content-Type', 'application/json');
    }

    public function getOneById(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $persona = Persona::find($args["id"]);
        if ($persona) $response->getBody()->write(json_encode($persona));
        return $response; //->withHeader('Content-Type', 'application/json');
        //return $persona;
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
        return $response; //->withHeader('Content-Type', 'application/json');
    }

    public function add(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $nombre = $body["nombre"] ?? '';
        $apellido = $body["apellido"] ?? '';
        $email = $body["email"]??'';

        if (strlen($nombre) > 0) {
            if (strlen($apellido) > 0) {
                if (strlen($email) > 0) {
                    $user = User::find($email);
                    if ($user == null) {
                        $response->getBody()->write(json_encode(array('message' => "Wrong email")));
                    } else {
                        $persona = new Persona;
                        $persona->nombre = $nombre;
                        $persona->apellido = $apellido;
                        $persona->email = $email;
                        $existe = Persona::where('nombre', $nombre)->where('apellido', $apellido)->get();
                        //var_dump($existe);
                        if (!isset($existe[0])) {
                            $persona->save();
                            $response->getBody()->write('Person Saved');
                        } else {
                            $response->getBody()->write('Person could not be saved');
                        }
                    }
                } else {
                    $response->getBody()->write(json_encode(array('message' => 'Email is required')));
                }
            } else {
                $response->getBody()->write(json_encode(array('message' => 'Apellido is required')));
            }
        } else {
            $response->getBody()->write(json_encode(array('message' => 'Nombre is required')));
        }
        return $response; //->withHeader('Content-Type', 'application/json');
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $persona = Persona::find($args["id"]);
        if (isset($persona)) {
            $body = $request->getParsedBody();
            if ($body["nombre"]) $persona->nombre = $body["nombre"];
            if ($body["apellido"]) $persona->apellido = $body["apellido"];
            $persona->save();
            $response->getBody()->write('Person Updated');
        } else {
            $response->getBody()->write('Person does not exist');
        }
        return $response; //->withHeader('Content-Type', 'application/json');
    }
}
