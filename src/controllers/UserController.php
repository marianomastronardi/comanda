<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use DI\ContainerBuilder;
use Seguridad\iToken;
use App\Models\User;


class UserController
{

    public function signup(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        try {
            $body = $request->getParsedBody();
            $email = $body["email"] ?? '';
            $password = $body["password"] ?? '';
            if (strlen($email)>0) {
                if (strlen($password)>0) {
                    $count = User::where('email',$email)->count();
                    if ($count > 0) {
                        $response->getBody()->write(json_encode(array("rta" => "User already exists")));
                    } else {
                        $user = new User;
                        $user->email = $email;
                        //$user->tipo_usuario = $body["tipo_usuario"];
                        $user->password = password_hash($password, PASSWORD_BCRYPT);
                        $user->save();
                        $response->getBody()->write(json_encode(array("rta" => "User has been saved successfuly")));
                    }
                } else {
                    $response->getBody()->write(json_encode(array("rta" => "Password is required")));
                }
            } else {
                $response->getBody()->write(json_encode(array("rta" => "Email is required")));
            }
            return $response;
        } catch (\Illuminate\Database\QueryException $e) {
            $error_code = $e->errorInfo[1];
            $response->getBody()->write((string)$error_code);
            return $response;
        } catch (\Throwable $th) {
            $response->getBody()->write($th->getMessage());
            return $response;
        }
    }

    public function LogIn(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $body = $request->getParsedBody();
            $email = $body["email"] ?? '';
            $password = $body["password"] ?? '';
            if (strlen($email) > 0) {
                $user = User::find($email);
                if ($user == null) {
                    $response->getBody()->write(json_encode(array('message' => "Wrong email")));
                } else {
                    if (strlen($password) > 0) {
                        $token = iToken::encodeUserToken($email, $password);
                        if (isset($token)) {
                            $response->getBody()->write(json_encode($token));
                        } else {
                            $response->getBody()->write(json_encode(array("error" => "Something goes wrong. Please, check your credentials")));
                        }
                    } else {
                        $response->getBody()->write(json_encode(array("error" => "You must set a password")));
                    }
                }
            } else {
                $response->getBody()->write(json_encode(array("error" => "You must set an email")));
            }

            return $response;
        } catch (\Throwable $th) {
            $response->getBody()->write($th->getMessage());
            return $response;
        }
    }
}
