<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Routing\RouteCollectorProxy;
use Recursos\Persona;
use Seguridad\iToken;
use Slim\Factory\AppFactory;
use App\Controllers\PersonaController;
use Config\Database;
use Illuminate\Container\Container;

require __DIR__ . '/vendor/autoload.php';
$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->setBasePath("/ProgramacionIII-3C/Comanda");
new Database();

/** Grupo /login */
/* $app->group('/login', function (RouteCollectorProxy $group) {
    $group->get('', function (Request $request, Response $response, $args) {
        //$response->getBody()->write("Hello world from GET!");
        $query = $request->getQueryParams();
        $user = Usuario::getUser($query['email']);
        if ($user) {
            if (!password_verify($query['password'], $user["clave"])) {
                echo "Contraseña incorrecta";
            } else {
                $response->getBody()->write(Usuario::getToken($query['email'], $query['password']));
            }
        } else {
            return json_encode(array('message' => 'El email no existe'));
        }
        return $response;
    });

    $group->post('', function (Request $request, Response $response, $args) {
        //$response->getBody()->write("Hello world from GET!");
        $body = $request->getParsedBody();
        $user = Usuario::getUser($body['email']);
        //var_dump($user);
        if ($user) {
            if (!password_verify($body['password'], $user["clave"])) {
                echo "Contraseña incorrecta";
            } else {
                $response->getBody()->write(Usuario::getToken($body['email'], $body['password']));
            }
        } else {
            return json_encode(array('message' => 'El email no existe'));
        }
        return $response;
    });
});
 */
/** Grupo /usuarios */
/* $app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->post('', function (Request $request, Response $response, $args) {
        //$response->getBody()->write("Hello world from POST!");
        $body = $request->getParsedBody();
        $response->getBody()->write(Usuario::Save($body["email"], $body["password"]));
        //echo $response;
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/{email}', function (Request $request, Response $response, $args) {
        $user = Usuario::getUser($args["email"]);
        $response->getBody()->write(json_encode(new Usuario($user['email'], $user['clave'])));
        return $response->withHeader('Content-Type', 'application/json');
    });
});

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

/** Grupo /personas */
 $app->group('/personas', function (RouteCollectorProxy $group) {
    $group->post('',  PersonaController::class . ":add");
    $group->get('',  PersonaController::class . ":getAll");
    $group->get('/{id}', PersonaController::class . ":getOneById");
    $group->get('/{nombre}/[{apellido}]', PersonaController::class . ":getOneByName");
    $group->put('/{id}', PersonaController::class . ":edit");
}); 

$app->run(); 

/* use Seguridad\Usuario;
use Seguridad\iToken;

$user = new Usuario('magama', '123');

$user->name = 'Mariano';
echo $user->name; */
/* $method = $request->getMethod();
echo $method;
$group->get('/{id}', UserController::class . ":getOne");
 */ 