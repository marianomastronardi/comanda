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
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getOneById(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $employee = Empleado::with('sector')->with('puesto')->with('estado')->find($args["id"]);
        if($employee)$response->getBody()->write(json_encode($employee));
        return $response->withHeader('Content-Type', 'application/json');
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
        $employee = new Empleado;
        $employee->persona_id = $persona->id;
        //sector
        $sector = Sector::find($body["sector_id"]);
        if(isset($sector))
        {
            $employee->sector_id = $body["sector_id"];
        }else{
            $response->getBody()->write('Section does not exist');
            return $response->withHeader('Content-Type', 'application/json');
        }
        //puesto
        $puesto = Puesto::find($body["puesto_id"]);
        if(isset($puesto))
        {
            $employee->puesto_id = $body["puesto_id"];
        }else{
            $response->getBody()->write('Position does not exist');
            return $response->withHeader('Content-Type', 'application/json');
        }
        //estado empleado
        $Estado = EstadoEmpleado::where('descripcion', 'ACTIVO')->first();
        if(isset($Estado))
        {
            $employee->estado_id = $Estado->id;
        }else{
            $response->getBody()->write('Employee State does not exist');
            return $response->withHeader('Content-Type', 'application/json');
        }

        $employee->save();
        $response->getBody()->write('Partner Saved');

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $employee = Empleado::find($args["id"]);
        if(isset($employee)){
            //estado empleado
            $Estado = EstadoEmpleado::where('descripcion', 'SUSPENDIDO')->first();
            if(isset($Estado))
            {
                $employee->estado_id = $Estado->id;
            }else{
                $response->getBody()->write('Employee State does not exist');
                return $response->withHeader('Content-Type', 'application/json');
            }
            $employee->save();
            $response->getBody()->write('Employee suspended');
        }
        return $response->withHeader('Content-Type', 'application/json');
    } 

}
