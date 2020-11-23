<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use App\Models\Sector;
use App\Models\User;

class SectorController
{

    private $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getAll(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $rta = Sector::get();
        //$body = $request->getParsedBody();
        $response->getBody()->write(json_encode($rta));
        return $response; //->withHeader('Content-Type', 'application/json');
    }
}