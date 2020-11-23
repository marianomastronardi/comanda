<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use App\Models\Mesa;
use App\Models\EstadoMesa;
use App\Models\User;

class MesaController
{

    private $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $rta = Mesa::select('mesas.id', 'mesas.descripcion', 'estado_mesas.descripcion')
            ->join('estado_mesas', 'estado_mesas.id', '=', 'mesas.id_estado')
            ->get();
        $response->getBody()->write(json_encode($rta));
        return $response; //->withHeader('Content-Type', 'application/json');
    }

    public function add(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $descripcion = $body['descripcion'] ?? '';

        if (strlen($descripcion) > 0) {
            $estado = EstadoMesa::where('descripcion', 'cerrada')->get();
            $estado = $estado[0]['id'];
            $mesa = new Mesa();
            $mesa->descripcion = $descripcion;
            $mesa->id_estado = $estado;
            $mesa->save();
            $response->getBody()->write(json_encode(array('message' => 'Mesa has been saved')));
        } else {
            $response->getBody()->write(json_encode(array('message' => 'Descripcion is required')));
        }

        return $response;
    }

    public function changeState(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        //$body = $request->getParsedBody();
        $id = $args['id'] ?? '';

        if (strlen($id) > 0) {
            $mesa = Mesa::find($id);
            if ($mesa !== null) {
                $actual = EstadoMesa::find($mesa->id);
                switch ($actual->descripcion) {
                    case 'con cliente esperando pedido':
                        $estadoMesa = EstadoMesa::where('descripcion', 'con clientes comiendo')->get();
                        $estadoMesa = $estadoMesa[0]['id'];
                        break;
                    case 'con clientes comiendo':
                        $estadoMesa = EstadoMesa::where('descripcion', 'con clientes pagando')->get();
                        $estadoMesa = $estadoMesa[0]['id'];
                        break;
                        /* case 'con clientes pagando':
                        $estadoMesa = EstadoMesa::where('descripcion', 'cerrada')->get();
                        $estadoMesa = $estadoMesa[0]['id'];
                    break; */
                    default:
                        break;
                }
                $mesa->estado_id = $estadoMesa;
                $mesa->save();
                $response->getBody()->write(json_encode(array('message' => 'Table has been changed successfuly')));
            } else {
                $response->getBody()->write(json_encode(array('message' => 'Table does not exists')));
            }
        } else {
            $response->getBody()->write(json_encode(array('message' => 'ID is required')));
        }
        return $response;
    }
    public function closeTable(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $id = $body['id'] ?? '';

        if (strlen($id) > 0) {
            $mesa = Mesa::find($id);
            if ($mesa !== null) {
                $estadoMesa = EstadoMesa::where('descripcion', 'cerrada')->get();
                $estadoMesa = $estadoMesa[0]['id'];
                $mesa->estado_id = $estadoMesa;
                $mesa->save();
                $response->getBody()->write(json_encode(array('message' => 'Table has been closed successfuly')));
            } else {
                $response->getBody()->write(json_encode(array('message' => 'Table does not exists')));
            }
        } else {
            $response->getBody()->write(json_encode(array('message' => 'ID is required')));
        }
        return $response;
    }
}
