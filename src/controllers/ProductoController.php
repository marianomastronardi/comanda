<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use App\Models\Producto;
use App\Models\Sector;
use App\Models\User;

class ProductoController
{

    private $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $rta = Producto::get();
        //$body = $request->getParsedBody();
        $response->getBody()->write(json_encode($rta));
        return $response; //->withHeader('Content-Type', 'application/json');
    }

    public function add(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();

        $descripcion = $body['descripcion'] ?? '';
        $sector_id = $body['sector_id'] ?? '';
        $precio = $body['precio'] ?? '';

        if (strlen($descripcion) > 0) {
            if (strlen($sector_id) > 0) {
                if (strlen($precio) > 0) {
                    $sector = Sector::find($sector_id);
                    if ($sector == null) {
                        $response->getBody()->write(json_encode(array('message' => 'Sector does not exist')));
                    } else {
                        $producto = new Producto();
                        $producto->descripcion = $descripcion;
                        $producto->sector_id = $sector_id;
                        $producto->precio = $precio;
                        $producto->save();
                        $response->getBody()->write(json_encode(array('message' => 'Product has been saved')));
                    }
                } else {
                    $response->getBody()->write(json_encode(array('message' => 'Precio is required')));
                }
            } else {
                $response->getBody()->write(json_encode(array('message' => 'Sector is required')));
            }
        } else {
            $response->getBody()->write(json_encode(array('message' => 'Descripcion is required')));
        }
        return $response; //->withHeader('Content-Type', 'application/json');
    }
}
