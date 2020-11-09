<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use App\Models\Persona;
use App\Models\Empleado;
use App\Models\EstadoPedido;
use App\Models\EstadoEmpleado;
use App\Models\Sector;
use App\Models\Pedido;
use DateInterval;
use DateTime;

class PedidoController
{

    private $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $rta = Pedido::with('sector')->with('employee')->with('estado')->get();
        $response->getBody()->write(json_encode($rta));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getOneById(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $order = Pedido::with('sector')->with('employee')->with('estado')->find($args["id"]);
        if($order)$response->getBody()->write(json_encode($order));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getBySection(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $pendiente = EstadoPedido::where('descripcion', 'PENDIENTE')->first();
        //var_dump($pendiente);
        //die();
        $order = Pedido::with('sector')->with('employee')->with('estado')->where('estado_id', $pendiente->id)->where('sector_id', $args['sector_id'])->get();
        if($order)$response->getBody()->write(json_encode($order));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function add(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $pedido = new Pedido;
        //sector
        $sector = Sector::find($body["sector_id"]);
        if(isset($sector))
        {
            $pedido->sector_id = $body["sector_id"];
        }else{
            $response->getBody()->write('Section does not exist');
            return $response->withHeader('Content-Type', 'application/json');
        }
        //estado empleado
        $Estado = EstadoPedido::find($body["estado_id"]);
        if(isset($Estado))
        {
            $pedido->estado_id = $Estado->id;
        }else{
            $response->getBody()->write('Order State does not exist');
            return $response->withHeader('Content-Type', 'application/json');
        }
        $pedido->descripcion = $body["descripcion"];
        $pedido->monto = $body["monto"];
        //codigo
        $pedido->codigo = substr(md5(time()), 0, 5);
        $pedido->save();
        $response->getBody()->write('Order Saved');

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function setOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $pedido = Pedido::find($args['id']);

        //empleado
        $employee = Empleado::find($body["empleado_id"]);
        if(isset($employee)){
            if($pedido->sector_id == $employee->sector_id){
                $activo = EstadoEmpleado::where('descripcion', 'ACTIVO')->first();
                if($employee->estado_id == $activo->id)
                {
                    $pedido->empleado_id = $employee->id;
                }else{
                    $response->getBody()->write('Employee state does not allows to get the order');
                    return $response->withHeader('Content-Type', 'application/json');        
                }
            }else{
                $response->getBody()->write('Employee belongs to some wrong section');
                return $response->withHeader('Content-Type', 'application/json');    
            }            
        }else{
            $response->getBody()->write('Employee does not exist');
            return $response->withHeader('Content-Type', 'application/json');
        }

        //estado
        $estado = EstadoPedido::where('descripcion', 'EN PREPARACION')->first();
        if(isset($estado))
        {
            $pedido->estado_id = $estado->id;
        }else{
            $response->getBody()->write('Order State does not exist');
            return $response->withHeader('Content-Type', 'application/json');
        }

        //Tiempo Estimado
        $minutes_to_add = $body['delivery_time'];
        $deliveryTime = new DateTime();
        $deliveryTime->add(new DateInterval('PT' . $minutes_to_add . 'M'));
        
        $pedido->delivery_time = $deliveryTime;
        $pedido->save();
        $response->getBody()->write('Order on process');
        return $response->withHeader('Content-Type', 'application/json');
    } 

}
