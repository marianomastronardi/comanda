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
use App\Models\EstadoMesa;
use App\Models\Sector;
use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\Item;
use App\Models\Producto;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Puesto;
use App\Models\Socio;
//Clases
use Seguridad\iToken;
use Psr\Http\Message\UploadedFileInterface;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Capsule\Manager as DBM;

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
        $rta = Pedido::select('pedidos.id', 'pedidos.estado_id', 'estado_pedidos.descripcion as estado', 'pedidos.mesa_id', 'mesas.descripcion as mesa', 'productos.descripcion', 'pedidos.codigo', 'pedidos.delivery_time', 'items.monto', 'pedidos.photo', 'pedidos.created_at', 'pedidos.updated_at', 'pedidos.deleted_at')
            ->join('estado_pedidos', 'estado_pedidos.id', '=', 'pedidos.estado_id')
            ->join('mesas', 'mesas.id', '=', 'pedidos.mesa_id')
            ->join('estado_mesas', 'estado_mesas.id', '=', 'mesas.id_estado')
            ->join('items', 'items.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'productos.id', '=', 'items.producto_id')
            ->get();
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function getOneById(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $order = Pedido::select('pedidos.id', 'pedidos.estado_id', 'estado_pedidos.descripcion as estado', 'pedidos.mesa_id', 'mesas.descripcion as mesa', 'productos.descripcion', 'pedidos.codigo', 'pedidos.delivery_time', 'items.monto', 'pedidos.photo', 'pedidos.created_at', 'pedidos.updated_at', 'pedidos.deleted_at')
            ->join('estado_pedidos', 'estado_pedidos.id', '=', 'pedidos.estado_id')
            ->join('mesas', 'mesas.id', '=', 'pedidos.mesa_id')
            ->join('estado_mesas', 'estado_mesas.id', '=', 'mesas.id_estado')
            ->join('items', 'items.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'productos.id', '=', 'items.producto_id')
            ->where('pedidos.id', '=', $id)
            ->get();
        if ($order) $response->getBody()->write(json_encode($order));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function add(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $pedido = new Pedido;
        $photo = null;
        $mesa = $body["mesa_id"] ?? '';
        $cliente_id = $body["cliente_id"] ?? '';
        $uploadedFiles = $request->getUploadedFiles();
        if (isset($uploadedFiles['foto'])) {
            $uploadedFile = $uploadedFiles['foto'];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $photo = serialize($uploadedFile);
            }
        }
        //mesa
        $mesa = Mesa::find($mesa);
        if (isset($mesa)) {
            $cliente = Cliente::find($cliente_id);
            if ($cliente !== null) {
                $pedido->mesa_id = $mesa->id;
                $pedido->cliente_id = $cliente->id;
                if (isset($photo)) $pedido->photo = $photo;
                //codigo
                $pedido->codigo = substr(md5(time()), 0, 5);
                //Estado
                $estado = EstadoPedido::where('descripcion', 'PENDIENTE')->get();
                $pedido->estado_id = $estado[0]['id'];
                $pedido->save();

                //Mesa Estado
                //le cambio es estado a la mesa
                $estadoMesa = EstadoMesa::where('descripcion', 'con cliente esperando pedido')->get();
                $estadoMesa = $estadoMesa[0]['id'];
                $mesa->id_estado = $estadoMesa;
                $mesa->save();

                $order = Pedido::orderBy('id', 'desc')->first();
                $response->getBody()->write(json_encode(array('message' => 'Order ID: ' . $order->id . ' Saved')));
            } else {
                $response->getBody()->write('Cliente does not exist');
            }
        } else {
            $response->getBody()->write('Mesa does not exist');
        }
        return $response;
    }

    public function getOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $code = $args['code'];

        $pedidos = Pedido::select(
            'pedidos.id',
            'mesas.descripcion',
            'personas.nombre as NombreCliente',
            'personas.apellido as ApellidoCliente',
            'estado_pedidos.descripcion as EstadoItem',
            'productos.descripcion',
            'items.monto',
            'items.delivery_time'
        )
            //->join('estado_pedidos', 'estado_pedidos.id', '=', 'pedidos.estado_id')
            ->join('mesas', 'mesas.id', '=', 'pedidos.mesa_id')
            ->join('clientes', 'clientes.id', '=', 'pedidos.cliente_id')
            ->join('personas', 'personas.id', '=', 'clientes.persona_id')
            ->join('items', 'items.pedido_id', '=', 'pedidos.id')
            ->join('estado_pedidos', 'estado_pedidos.id', '=', 'items.estado_id')
            ->join('productos', 'productos.id', '=', 'items.producto_id')
            ->join('sectors', 'sectors.id', '=', 'items.sector_id')
            //->join('empleados', 'empleados.id', '=', 'items.empleado_id')
            //->join('personas p', DB::raw('p.id as pid'), '=', 'empleados.persona_id')
            ->where('pedidos.codigo', $code)
            ->get();

        $response->getBody()->write(json_encode($pedidos));
        return $response;
    }

    //items
    public function addItem(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();

        $producto_id = $body['producto_id'] ?? '';
        $pedido_id = $body['pedido_id'] ?? '';
        $cantidad = $body['cantidad'] ?? '';

        if (strlen($pedido_id) > 0) {
            if (strlen($producto_id) > 0) {
                if (strlen($cantidad) > 0) {
                    $producto = Producto::find($producto_id);
                    if ($producto !== null) {
                        $monto = $producto->precio * $cantidad;
                        $sector = $producto->sector_id;
                        $estado = EstadoPedido::where('descripcion', 'PENDIENTE')->get();
                        $estado_id = $estado[0]['id'];
                        $item = new Item();
                        $item->pedido_id = $pedido_id;
                        $item->producto_id = $producto_id;
                        $item->sector_id = $sector;
                        $item->estado_id = $estado_id;
                        $item->cantidad = $cantidad;
                        $item->monto = $monto;
                        $item->save();
                        $response->getBody()->write('Item has been saved successfuly');
                    } else {
                        $response->getBody()->write('Producto does not exist');
                    }
                } else {
                    $response->getBody()->write('Cantidad is required');
                }
            } else {
                $response->getBody()->write('Producto is required');
            }
        } else {
            $response->getBody()->write('Pedido is required');
        }
        return $response;
    }

    public function getItemBySector(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $arr = $request->getHeader('token');
        if (count($arr) > 0) $token = $arr[0];
        $jwt = isset($token) ? iToken::decodeUserToken($token) : false; // VALIDAR EL TOKEN

        $user = User::find($jwt["email"]);
        $persona = Persona::where('email', $user->email)->get();
        if (isset($persona[0]['id'])) {
            $id = $persona[0]['id'];
            $empleado = Empleado::where('persona_id', $id)->get();
            if (isset($empleado[0]['sector_id'])) {
                //ITEMS X SECTOR
                $sector = $empleado[0]['sector_id'];
                $sector_id = Sector::find($sector);
                if (isset($sector_id)) {
                    //Estado
                    $estado = EstadoPedido::where('descripcion', 'PENDIENTE')->get();
                    $estado_id = $estado[0]['id'];
                    $items = Item::where('sector_id', $sector_id->id)->where('estado_id', $estado_id)->get();
                    $response->getBody()->write(json_encode($items));
                } else {
                    $response->getBody()->write(json_encode(array('message' => 'Sector does not exist')));
                }
            } else {
                //ITEMS TODOS PORQUE ES SOCIO
                $socio = Socio::where('persona_id', $id)->get();
                if (isset($socio[0]['id'])) {
                    $items = Item::get();
                    $response->getBody()->write(json_encode($items));
                } else {
                    $response->getBody()->write(json_encode(array('message' => 'Person is not an Employee nor a Partner')));
                }
            }
        } else {
            $response->getBody()->write(json_encode(array('message' => 'Person does not exists')));
        }
        return $response;
    }

    public function setOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $item_id = $args['item_id'] ?? '';
        $delivery_time = $body['delivery_time'] ?? '';
        $enPreparacion = EstadoPedido::where('descripcion', 'EN PREPARACION')->get();
        $enPreparacion = $enPreparacion[0]['id'] ?? '';
        $pendiente = EstadoPedido::where('descripcion', 'PENDIENTE')->get();
        $pendiente = $pendiente[0]['id'] ?? '';
        //var_dump($enPreparacion[0]['id']);
        //die();
        if (strlen($item_id) > 0) {
            if (strlen($delivery_time) > 0) {
                if (strlen($enPreparacion) > 0) {
                    $arr = $request->getHeader('token');
                    if (count($arr) > 0) $token = $arr[0];
                    $jwt = isset($token) ? iToken::decodeUserToken($token) : false; // VALIDAR EL TOKEN

                    $user = User::find($jwt["email"]);
                    $persona = Persona::where('email', $user->email)->get();
                    $id = $persona[0]['id'];
                    $empleado = Empleado::where('persona_id', $id)->get();
                    if (isset($empleado[0]['sector_id'])) {
                        $sector = $empleado[0]['sector_id'];
                        $item = Item::find($item_id);
                        if ($item !== null) {
                            if ($item->estado_id == $pendiente) {
                                if ($item->sector_id == $sector) {
                                    $deliveryTime = new DateTime();
                                    $deliveryTime->add(new DateInterval('PT' . $delivery_time . 'M'));
                                    $empleado_id = $empleado[0]['id'];

                                    $item->delivery_time = $deliveryTime;
                                    $item->empleado_id = $empleado_id;
                                    $item->estado_id = $enPreparacion;
                                    $item->save();

                                     //busco los items de mi pedido para saber si soy el ultimo listo para servir
                                     $count = Item::where('pedido_id', $item->pedido_id)->where('estado_id', $pendiente)->count();
                                     if ($count == 0) {
                                         //cierro el pedido, le cambio el estado
                                         $pedido = Pedido::find($item->pedido_id);
                                         $pedido->estado_id = $enPreparacion;
                                         $max = Item::where('pedido_id', $item->pedido_id)->max('delivery_time');
                                         $pedido->delivery_time = $max;
                                         $pedido->save();
                                     }
                                    $response->getBody()->write(json_encode(array('message' => 'Item has been changed successfuly')));
                                } else {
                                    $response->getBody()->write(json_encode(array('message' => 'You are not allowed to set this sector item')));
                                }
                            } else {
                                $response->getBody()->write(json_encode(array('message' => 'Wrong Status')));
                            }
                        } else {
                            $response->getBody()->write(json_encode(array('message' => 'Item does not exist')));
                        }
                    } else {
                        $response->getBody()->write(json_encode(array('message' => 'Sector does not exist')));
                    }
                } else {
                    $response->getBody()->write(json_encode(array('message' => 'State does not exist')));
                }
            } else {
                $response->getBody()->write(json_encode(array('message' => 'Time is required')));
            }
        } else {
            $response->getBody()->write(json_encode(array('message' => 'Item is required')));
        }
        return $response;
    }

    public function remove(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        //$body = $request->getParsedBody();
        $item_id = $args['item_id'] ?? '';
        $enPreparacion = EstadoPedido::where('descripcion', 'EN PREPARACION')->get();
        $enPreparacion = $enPreparacion[0]['id'] ?? '';
        $pendiente = EstadoPedido::where('descripcion', 'PENDIENTE')->get();
        $pendiente = $pendiente[0]['id'] ?? '';
        $ready = EstadoPedido::where('descripcion', 'LISTO PARA SERVIR')->get();
        $ready = $ready[0]['id'] ?? '';

        if (strlen($item_id) > 0) {
            if (strlen($ready) > 0) {
                $arr = $request->getHeader('token');
                if (count($arr) > 0) $token = $arr[0];
                $jwt = isset($token) ? iToken::decodeUserToken($token) : false; // VALIDAR EL TOKEN

                $user = User::find($jwt["email"]);
                $persona = Persona::where('email', $user->email)->get();
                $id = $persona[0]['id'];
                //var_dump($id);
                $empleado = Empleado::where('persona_id', $id)->get();
                //var_dump($empleado[0]['persona_id']);
                if (isset($empleado[0]['sector_id'])) {
                    $sector = $empleado[0]['sector_id'];
                    $item = Item::find($item_id);
                    if ($item !== null) {
                        if ($item->estado_id == $enPreparacion) {
                            if ($item->sector_id == $sector) {
                                //var_dump($item->empleado_id);
                                //var_dump($empleado[0]['persona_id']);
                                if ($item->empleado_id == $empleado[0]['id']) {
                                    $item->estado_id = $ready;
                                    $item->save();
                                    //busco los items de mi pedido para saber si soy el ultimo listo para servir
                                    $countenp = Item::where('pedido_id', $item->pedido_id)->where('estado_id', $enPreparacion)->count();
                                    $countp = $count = Item::where('pedido_id', $item->pedido_id)->where('estado_id', $pendiente)->count();
                                    $count = $countenp  + $countp;
                                    if ($count == 0) {
                                        //cierro el pedido, le cambio el estado
                                        $pedido = Pedido::find($item->pedido_id);
                                        $pedido->estado_id = $ready;
                                        $pedido->deleted_at = new DateTime();
                                        $pedido->save();
                                    }
                                    $response->getBody()->write(json_encode(array('message' => 'Item has been closed successfuly')));
                                } else {
                                    $response->getBody()->write(json_encode(array('message' => 'Employee is not the same who are making the order')));
                                }
                            } else {
                                $response->getBody()->write(json_encode(array('message' => 'You are not allowed to set this sector item')));
                            }
                        } else {
                            $response->getBody()->write(json_encode(array('message' => 'Wrong Status')));
                        }
                    } else {
                        $response->getBody()->write(json_encode(array('message' => 'Item does not exist')));
                    }
                } else {
                    $response->getBody()->write(json_encode(array('message' => 'Sector does not exist')));
                }
            } else {
                $response->getBody()->write(json_encode(array('message' => 'State does not exist')));
            }
        } else {
            $response->getBody()->write(json_encode(array('message' => 'Item is required')));
        }

        return $response;
    }

    //Reportes
    public function getOrderBySector(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $results = DBM::table('pedidos')
                     ->select(DBM::raw('count(*) as qty, items.sector_id'))
                     ->join('items', 'items.pedido_id', '=', 'pedidos.id')
                     //->where('status', '<>', 1)
                     ->groupBy('items.sector_id')
                     ->get();
        $response->getBody()->write(json_encode($results));
        return $response;
    }
    
    public function getOrderBySectorEmployee(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $results = DBM::table('pedidos')
                     ->select(DBM::raw('count(*) as qty, items.sector_id, items.empleado_id'))
                     ->join('items', 'items.pedido_id', '=', 'pedidos.id')
                     //->where('status', '<>', 1)
                     ->groupBy('items.sector_id','items.empleado_id')
                     ->get();
        $response->getBody()->write(json_encode($results));
        return $response;
    }
    
    public function getOrderByEmployee(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $results = DBM::table('pedidos')
                     ->select(DBM::raw('count(*) as qty, items.empleado_id'))
                     ->join('items', 'items.pedido_id', '=', 'pedidos.id')
                     //->where('status', '<>', 1)
                     ->groupBy('items.empleado_id')
                     ->get();
        $response->getBody()->write(json_encode($results));
        return $response;
    }
}
