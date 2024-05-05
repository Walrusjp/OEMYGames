<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Slim\Factory\AppFactory;
    use Slim\Middleware\BodyParsingMiddleware;

    require __DIR__ . '/vendor/autoload.php';

    $app = AppFactory::create();
    $app->addBodyParsingMiddleware();
    $app->setBasePath("/OEMYGames");
    
    function Authentication($user, $pass) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oemygames-default-rtdb.firebaseio.com/usuarios.json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $usuarios = json_decode(curl_exec($ch), true);
        curl_close($ch);
    
        $isUser = false;
        $isPass = false;
    
        if (array_key_exists($user, $usuarios)) {
            $isUser = true;
            if ($usuarios[$user] === md5($pass)) {
                $isPass = true;
            } else {
                return json_encode(['code'=>'401', 'message'=>'Password no reconocido', 'status'=>'error']);
            }
        } else {
            return json_encode(['code'=>'401', 'message'=>'Usuario no reconocido', 'status'=>'error']);
        }
    
        return json_encode(['code'=>'200', 'message'=>'Atenticacion exitosa', 'status'=>'success']);
    };
        
    $app->get('/productos[/{categoria}]', function (Request $request, Response $response, $args) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oemygames-default-rtdb.firebaseio.com/productos.json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $productos = json_decode(curl_exec($ch), true);
        curl_close($ch);
    
        $user = $request->getHeader('user')[0];
        $pass = $request->getHeader('pass')[0];
    
        $resAuth = Authentication($user, $pass);
        $resAuthObj = json_decode($resAuth);
    
        if ($resAuthObj->status !== 'error') {
            $categoria = strtolower($args['categoria']);
            $productos = array_change_key_case($productos, CASE_LOWER);
            if (isset($productos[$categoria])) {
                $response->getBody()->write(json_encode([
                    "code" => "200",
                    "message" => "Productos obtenidos exitosamente",
                    "data" => $productos[$categoria],
                    "status" => "success"
                ]));
            } else {
                $response->getBody()->write(json_encode([
                    "code" => "404",
                    "message" => "Categoria no encontrada",
                    "status" => "error"
                ]));
            }
        } else {
            $response->getBody()->write($resAuth);
        }
    
        return $response;
    });
    
    $app->get('/detalles[/{clave}]', function (Request $request, Response $response, $args) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oemygames-default-rtdb.firebaseio.com/detalles.json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $detalles = json_decode(curl_exec($ch), true);
        curl_close($ch);
    
        $user = $request->getHeader('user')[0];
        $pass = $request->getHeader('pass')[0];
    
        $resAuth = Authentication($user, $pass);
        $resAuthObj = json_decode($resAuth);
    
        if ($resAuthObj->status !== 'error') {
            $clave = strtolower($args['clave']);
            $detalles = array_change_key_case($detalles, CASE_LOWER);
            if (isset($detalles[$clave])) {
                $oferta = $detalles[$clave]['Descuento'] === '0' ? true : false;
                $response->getBody()->write(json_encode([
                    "code" => "200",
                    "message" => "Detalles obtenidos exitosamente",
                    "data" => $detalles[$clave],
                    "oferta" => $oferta,
                    "status" => "success"
                ]));
            } else {
                $response->getBody()->write(json_encode([
                    "code" => "404",
                    "message" => "Clave no encontrada",
                    "status" => "error"
                ]));
            }
        } else {
            $response->getBody()->write($resAuth);
        }
    
        return $response;
    });

    $app->post('/producto/{categoria}', function ($request, $response, $args) {

        $user = $request->getHeader('user')[0];
        $pass = $request->getHeader('pass')[0];
    
        $resAuth = Authentication($user, $pass);
        $resAuthObj = json_decode($resAuth);
    
        if ($resAuthObj->status !== 'error') {
            $categoria = $args['categoria'];
            $data = $request->getParsedBody();
    
            $camposRequeridos = ['Consola', 'Desarrollador', 'Descuento', 'Fecha', 'ID', 'Nombre', 'Precio'];
            foreach ($camposRequeridos as $campo) {
                if (!isset($data[$campo])) {
                    $response->getBody()->write(json_encode([
                        "code" => "400",
                        "message" => "Bad Request, los datos enviados estan mal formados",
                        "status" => "error"
                    ]));
                    return $response;
                }
            }
    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://oemygames-default-rtdb.firebaseio.com/productos.json');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $categorias = json_decode(curl_exec($ch), true);
            curl_close($ch);
    
            $categoriaEncontrada = '';
            foreach ($categorias as $cat => $productos) {
                if (strcasecmp($cat, $categoria) == 0) {
                    $categoriaEncontrada = $cat;
                    break;
                }
            }
    
            if ($categoriaEncontrada !== '') {
                $idExiste = false;
                foreach ($categorias as $productos) {
                    if (array_key_exists($data['ID'], $productos)) {
                        $idExiste = true;
                        break;
                    }
                }
    
                if (!$idExiste) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://oemygames-default-rtdb.firebaseio.com/productos/' . $categoriaEncontrada . '.json');
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([$data['ID'] => $data['Nombre']]));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $resProd = curl_exec($ch);
    
                    curl_setopt($ch, CURLOPT_URL, 'https://oemygames-default-rtdb.firebaseio.com/detalles.json');
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([$data['ID'] => $data]));
                    $resDet = curl_exec($ch);
                    curl_close($ch);
    
                    if ($resProd !== false && $resDet !== false) {
                        $response->getBody()->write(json_encode([
                            "code" => "201",
                            "message" => "Producto insertado correctamente",
                            "data" => date('Y-m-d H:i:s'),
                            "status" => "success"
                        ]));
                    } else {
                        $response->getBody()->write(json_encode([
                            "code" => "400",
                            "message" => "Bad Request",
                            "status" => "error"
                        ]));
                    }
                } else {
                    $response->getBody()->write(json_encode([
                        "code" => "409",
                        "message" => "Conflict, el ID del producto ya existe",
                        "status" => "error"
                    ]));
                }
            } else {
                $response->getBody()->write(json_encode([
                    "code" => "404",
                    "message" => "Categoria no encontrada",
                    "status" => "error"
                ]));
            }
        } else {
            $response->getBody()->write($resAuth);
        }
    
        return $response;
    });
   
    $app->put('/producto/detalles/{clave}', function ($request, $response, $args) {

        $user = $request->getHeader('user')[0];
        $pass = $request->getHeader('pass')[0];
    
        $resAuth = Authentication($user, $pass);
        $resAuthObj = json_decode($resAuth);
    
        if ($resAuthObj->status !== 'error') {
            $clave = $args['clave'];
            $data = $request->getParsedBody();
    
            $camposRequeridos = ['Consola', 'Desarrollador', 'Descuento', 'Fecha', 'ID', 'Nombre', 'Precio'];
            $campoEncontrado = false;
            foreach ($camposRequeridos as $campo) {
                if (isset($data[$campo])) {
                    $campoEncontrado = true;
                    break;
                }
            }
    
            if ($campoEncontrado) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://oemygames-default-rtdb.firebaseio.com/detalles.json');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $detalles = json_decode(curl_exec($ch), true);
                curl_close($ch);
    
                if (array_key_exists($clave, $detalles)) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://oemygames-default-rtdb.firebaseio.com/detalles/' . $clave . '.json');
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $resDet = curl_exec($ch);
                    curl_close($ch);
    
                    if ($resDet !== false) {
                        $response->getBody()->write(json_encode([
                            "code" => "200",
                            "message" => "Producto actualizado correctamente",
                            "data" => date('Y-m-d H:i:s'),
                            "status" => "success"
                        ]));
                    } else {
                        $response->getBody()->write(json_encode([
                            "code" => "400",
                            "message" => "Bad Request",
                            "status" => "error"
                        ]));
                    }
                } else {
                    $response->getBody()->write(json_encode([
                        "code" => "404",
                        "message" => "Producto no encontrado",
                        "status" => "error"
                    ]));
                }
            } else {
                $response->getBody()->write(json_encode([
                    "code" => "400",
                    "message" => "Bad Request, los datos enviados estan mal formados",
                    "status" => "error"
                ]));
            }
        } else {
            $response->getBody()->write($resAuth);
        }
    
        return $response;
    });
    
    $app->delete('/producto/{clave}', function ($request, $response, $args) {

        $user = $request->getHeader('user')[0];
        $pass = $request->getHeader('pass')[0];
    
        $resAuth = Authentication($user, $pass);
        $resAuthObj = json_decode($resAuth);
    
        if ($resAuthObj->status !== 'error') {
            $clave = $args['clave'];
    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://oemygames-default-rtdb.firebaseio.com/productos.json');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $categorias = json_decode(curl_exec($ch), true);
            curl_close($ch);
    
            $categoriaEncontrada = '';
            foreach ($categorias as $categoria => $productos) {
                if (array_key_exists($clave, $productos)) {
                    $categoriaEncontrada = $categoria;
                    break;
                }
            }
            if ($categoriaEncontrada !== '') {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://oemygames-default-rtdb.firebaseio.com/productos/' . $categoriaEncontrada . '/' . $clave . '.json');
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $resProd = curl_exec($ch);
    
                curl_setopt($ch, CURLOPT_URL, 'https://oemygames-default-rtdb.firebaseio.com/detalles/' . $clave . '.json');
                $resDet = curl_exec($ch);
                curl_close($ch);
    
                if ($resProd !== false && $resDet !== false) {
                    $response->getBody()->write(json_encode([
                        "code" => "200",
                        "message" => "Producto eliminado correctamente",
                        "data" => date('Y-m-d H:i:s'),
                        "status" => "success"
                    ]));
                } else {
                    $response->getBody()->write(json_encode([
                        "code" => "400",
                        "message" => "Bad request",
                        "status" => "error"
                    ]));
                }
            } else {
                $response->getBody()->write(json_encode([
                    "code" => "404",
                    "message" => "El producto especificado no existe",
                    "status" => "error"
                ]));
            }
        } else {
            $response->getBody()->write($resAuth);
        }

        return $response;
    });    
   
      $app->run();
?>