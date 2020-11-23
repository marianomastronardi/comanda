<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Factory\AppFactory;
//Controllers
use App\Controllers\PersonaController;
use App\Controllers\SocioController;
use App\Controllers\EmpleadoController;
use App\Controllers\PedidoController;
use App\Controllers\UserController;
use App\Controllers\SectorController;
use App\Controllers\ProductoController;
use App\Controllers\ClienteController;
use App\Controllers\MesaController;
use App\Controllers\ReportesController;
//Middlewares
use App\Middlewares\MozoMiddleware;
use App\Middlewares\JsonMiddleware;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\SocioMiddleware;
use App\Middlewares\ClienteMiddleware;
use App\Middlewares\AdminMiddleware;
//DB
use Config\Database;
use Illuminate\Container\Container;

require __DIR__ . '/vendor/autoload.php';
$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->setBasePath("/ProgramacionIII-3C/Comanda");
new Database();

/**users */
$app->post('/users', UserController::class . ':signup')->add(new JsonMiddleware);

/**login */
$app->post('/login', UserController::class . ':LogIn')->add(new JsonMiddleware);

/**sectores */
$app->get('/sectores', SectorController::class . ':getAll')->add(new JsonMiddleware);

/**Mesas */
$app->group('/mesas', function (RouteCollectorProxy $group) {
$group->post('', MesaController::class . ':add');
$group->post('/{id}', MesaController::class . ':changeState')->add(new MozoMiddleware());
$group->put('/{id}', MesaController::class . ':closeTable')->add(new SocioMiddleware());
})->add(new JsonMiddleware)->add(new AuthMiddleware());

/**productos */
$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('', ProductoController::class . ':getAll');
    $group->post('', ProductoController::class . ':add');
})->add(new JsonMiddleware);

/** Grupo /pedidos */
$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('', PedidoController::class . ':getAll')->add(new SocioMiddleware());
    $group->get('/{id}', PedidoController::class . ':getOneById')->add(new MozoMiddleware());
    $group->post('', PedidoController::class . ':add')->add(new MozoMiddleware());
    //$group->get('/{sector_id}', PedidoController::class . ":getBySection");
    //$group->put('/{id}', PedidoController::class . ":setOrder");
    //$group->delete('/{id}', PedidoController::class . ":remove");
})->add(new JsonMiddleware)->add(new AuthMiddleware());

$app->get('/order/{code}', PedidoController::class . ':getOrder')
    ->add(new JsonMiddleware())
    ->add(new AuthMiddleware())
    ->add(new ClienteMiddleware());

    /**items */
$app->group('/item', function (RouteCollectorProxy $group) {
    $group->get('', PedidoController::class . ':getItemBySector');
    $group->post('', PedidoController::class . ':addItem')->add(new MozoMiddleware());
    $group->put('/{item_id}', PedidoController::class . ":setOrder");
    $group->delete('/{item_id}', PedidoController::class . ":remove");
})->add(new JsonMiddleware)->add(new AuthMiddleware());

/** Grupo /empleados */
 $app->group('/empleados', function (RouteCollectorProxy $group) {
    $group->get('', EmpleadoController::class . ':getAll');
    $group->post('', EmpleadoController::class . ':add');
    $group->get('/{id}', EmpleadoController::class . ":getOneById");
    $group->get('/{nombre}/[{apellido}]', EmpleadoController::class . ":getOneByName");
    $group->put('/{id}', EmpleadoController::class . ":edit");
    $group->delete('/{id}', EmpleadoController::class . ":remove");
})->add(new JsonMiddleware);
 
/** Grupo /socios */
 $app->group('/socios', function (RouteCollectorProxy $group) {
    $group->post('', SocioController::class . ':add');
    $group->get('',  SocioController::class . ":getAll");
    $group->get('/{id}', SocioController::class . ":getOneById");
    $group->get('/{nombre}/[{apellido}]', SocioController::class . ":getOneByName");
    $group->put('/{id}', SocioController::class . ":edit");
})->add(new JsonMiddleware);

/**Cliente */
$app->group('/cliente', function (RouteCollectorProxy $group) {
    $group->post('', ClienteController::class . ':add');
    $group->get('',  ClienteController::class . ":getAll");
    $group->get('/{id}', ClienteController::class . ":getOneById");
    $group->get('/{nombre}/[{apellido}]', ClienteController::class . ":getOneByName");
    $group->put('/{id}', ClienteController::class . ":edit");
})->add(new JsonMiddleware);

/** Grupo /personas */
 $app->group('/personas', function (RouteCollectorProxy $group) {
    $group->post('',  PersonaController::class . ":add");
    $group->get('',  PersonaController::class . ":getAll");
    $group->get('/{id}', PersonaController::class . ":getOneById");
    $group->get('/{nombre}/[{apellido}]', PersonaController::class . ":getOneByName");
    $group->put('/{id}', PersonaController::class . ":edit");
})->add(new JsonMiddleware); 

/**Reportes */
$app->group('/reportes', function (RouteCollectorProxy $group) {
    $group->get('/orders/sector',  PedidoController::class . ":getOrderBySector");
    $group->get('/orders/sector/employee',  PedidoController::class . ":getOrderBySectorEmployee");
    $group->get('/orders/employee', PedidoController::class . ":getOrderByEmployee");
})->add(new JsonMiddleware)->add(new AuthMiddleware())->add(new AdminMiddleware()); 

$app->run(); 

?>