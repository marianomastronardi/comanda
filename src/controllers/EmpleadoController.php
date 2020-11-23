<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use App\Models\Persona;
use App\Models\Empleado;
use App\Models\EstadoEmpleado;
use App\Models\Puesto;
use App\Models\Sector;
use App\Models\User;

class EmpleadoController
{

    private $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $rta = Empleado::with('sector')->with('puesto')->with('estado')->get();
        //$body = $request->getParsedBody();
        $response->getBody()->write(json_encode($rta));
        //return $response->withHeader('Content-Type', 'application/json');
        return $response;
    }

    public function getOneById(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $employee = Empleado::with('sector')->with('puesto')->with('estado')->find($args["id"]);
        if ($employee) $response->getBody()->write(json_encode($employee));
        //return $response->withHeader('Content-Type', 'application/json');
        return $response;
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
        if (isset($persona)) $employee = Empleado::with('sector')->with('puesto')->with('estado')->where('persona_id', $persona->id)->first();
        $response->getBody()->write(json_encode($employee));
        //return $response->withHeader('Content-Type', 'application/json');
        return $response;
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
                    //valido el email
                    $user = User::find($email);
                    if($user !== null){
                        $persona = Persona::where('nombre', $nombre)->where('apellido', $apellido)->first();
                        if (!isset($persona)) {
                            //no existe la persona
                            $model = new Persona;
                            $model->nombre = $nombre;
                            $model->apellido = $apellido;
                            $model->email = $email;
                            $model->save();
                            $persona = Persona::where('nombre', $body["nombre"])->where('apellido', $body["apellido"])->first();
                        }
                    }else{
                        $response->getBody()->write(json_encode(array('message' => 'Email does not exist')));
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
        
        $employee = new Empleado;
        $employee->persona_id = $persona->id;
        //sector
        $sector = Sector::find($body["sector_id"]);
        if (isset($sector)) {
            $employee->sector_id = $body["sector_id"];
            //puesto
            $puesto = Puesto::find($body["puesto_id"]);
            if (isset($puesto)) {
                $employee->puesto_id = $body["puesto_id"];
                //estado empleado
                $Estado = EstadoEmpleado::where('descripcion', 'ACTIVO')->first();
                if (isset($Estado)) {
                    $employee->estado_id = $Estado->id;
                    $employee->save();
                    $response->getBody()->write('Employee Saved');
                } else {
                    $response->getBody()->write('Employee State does not exist');
                    //return $response->withHeader('Content-Type', 'application/json');
                }
            } else {
                $response->getBody()->write('Position does not exist');
                //return $response->withHeader('Content-Type', 'application/json');
            }
        } else {
            $response->getBody()->write('Section does not exist');
            //return $response->withHeader('Content-Type', 'application/json');
        }
        //return $response->withHeader('Content-Type', 'application/json');
        return $response;
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $employee = Empleado::find($args["id"]);
        if (isset($employee)) {
            //estado empleado
            $Estado = EstadoEmpleado::where('descripcion', 'SUSPENDIDO')->first();
            if (isset($Estado)) {
                $employee->estado_id = $Estado->id;
                $employee->save();
                $response->getBody()->write('Employee suspended');
            } else {
                $response->getBody()->write('Employee State does not exist');
                //return $response->withHeader('Content-Type', 'application/json');
            }
        } else {
            $response->getBody()->write('Employee does not exist');
        }
        //return $response->withHeader('Content-Type', 'application/json');
        return $response;
    }
}
