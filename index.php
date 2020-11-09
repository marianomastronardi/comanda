<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Routing\RouteCollectorProxy;
use Seguridad\iToken;
use Slim\Factory\AppFactory;
use App\Controllers\PersonaController;
use App\Controllers\SocioController;
use App\Controllers\EmpleadoController;
use App\Controllers\PedidoController;
use Config\Database;
use Illuminate\Container\Container;

require __DIR__ . '/vendor/autoload.php';
$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->setBasePath("/ProgramacionIII-3C/Comanda");
new Database();

/** Grupo /pedidos */
$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('', PedidoController::class . ':getAll');
    $group->post('', PedidoController::class . ':add');
    //$group->get('/{id}', PedidoController::class . ":getOneById");
    $group->get('/{sector_id}', PedidoController::class . ":getBySection");
    $group->put('/{id}', PedidoController::class . ":setOrder");
    $group->delete('/{id}', PedidoController::class . ":remove");
});

/** Grupo /empleados */
 $app->group('/empleados', function (RouteCollectorProxy $group) {
    $group->get('', EmpleadoController::class . ':getAll');
    $group->post('', EmpleadoController::class . ':add');
    $group->get('/{id}', EmpleadoController::class . ":getOneById");
    $group->get('/{nombre}/[{apellido}]', EmpleadoController::class . ":getOneByName");
    $group->put('/{id}', EmpleadoController::class . ":edit");
    $group->delete('/{id}', EmpleadoController::class . ":remove");
});
 
/** Grupo /socios */
 $app->group('/socios', function (RouteCollectorProxy $group) {
    $group->post('', SocioController::class . ':add');
    $group->get('',  SocioController::class . ":getAll");
    $group->get('/{id}', SocioController::class . ":getOneById");
    $group->get('/{nombre}/[{apellido}]', SocioController::class . ":getOneByName");
    $group->put('/{id}', SocioController::class . ":edit");
});

/** Grupo /personas */
 $app->group('/personas', function (RouteCollectorProxy $group) {
    $group->post('',  PersonaController::class . ":add");
    $group->get('',  PersonaController::class . ":getAll");
    $group->get('/{id}', PersonaController::class . ":getOneById");
    $group->get('/{nombre}/[{apellido}]', PersonaController::class . ":getOneByName");
    $group->put('/{id}', PersonaController::class . ":edit");
}); 

$app->run(); 
/*
$app->post('/productos', function (Request $request, Response $response, $args) {
    $params = $request->getServerParams();
    $token = $params['HTTP_TOKEN'] ?? null;
    if ($token) {
        if (iToken::decodeUserToken($token)) {
            echo 'Signature valid';
        }
    }
    return $response;
}); */

?>