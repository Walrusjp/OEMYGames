<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Slim\Factory\AppFactory;

    require __DIR__ . '/vendor/autoload.php';

    $app = AppFactory::create();
    $app->setBasePath("/PRUEBA");

    $usuarios = array(
        'ventas' => '530b350d414da3378a15b3149b322908',
        'almacen' => '4e210009a1cfbf891ee1a8f75f441e2f'
    );

    $productos = array(
        'Deportes' => array(
            'SPRT-001'=> 'FIFA 23',
            'SPRT-002'=> 'NBA 2K24',
            'SPRT-003'=> 'Madden NFL 24'
    ),
        'Shooter' => array(
            'SHTR-004'=> 'Call of Duty: Warzone',
            'SHTR-005'=> 'Halo Infinite',
            'SHTR-006'=> 'Destiny 2'
    ),
        'RPG' => array(
            'RPG-007'=> 'Elden Ring',
            'RPG-008'=> 'The Witcher 3: Wild Hunt',
            'RPG-009'=> 'Final Fantasy XVI'
    ),
        'OpenWorld' => array(
            'OPNW-010'=> 'Red Dead Redemption 2',
            'OPNW-011'=> 'The Legend of Zelda: Breath of the Wild',
            'OPNW-012'=> 'Grand Theft Auto V'
    ));

    $detalles = array(
        'SPRT-001' => array(
            'ID' => 'SPRT-001',
            'Nombre' => 'FIFA 23',
            'Desarrollador' => 'EA Sports',
            'Consola' => 'PlayStation 5, Xbox Series X/S, PC, Stadia',
            'Fecha' => '30-09-2022',
            'Precio' => '1399 MXN',
            'Descuento' => '0'
        ),
        'SPRT-002' => array(
            'ID' => 'SPRT-002',
            'Nombre' => 'NBA 2K24',
            'Desarrollador' => 'Visual Concepts',
            'Consola' => 'PlayStation 4, PlayStation 5, Xbox One, Xbox Series X/S, Nintendo Switch, PC',
            'Fecha' => '08-09-2024',
            'Precio' => '1499 MXN',
            'Descuento' => '15%'
        ),
        'SPRT-003' => array(
            'ID' => 'SPRT-003',
            'Nombre' => 'Madden NFL 24',
            'Desarrollador' => 'EA Tiburon',
            'Consola' => 'PlayStation 5, Xbox Series X/S, PC',
            'Fecha' => '18-08-2023',
            'Precio' => '1399 MXN',
            'Descuento' => '5%'
        ),
        'SHTR-004' => array(
            'ID' => 'SHTR-004',
            'Nombre' => 'Call of Duty: Warzone',
            'Desarrollador' => 'Infinity Ward, Raven Software',
            'Consola' => 'PlayStation 4, PlayStation 5, Xbox One, Xbox Series X/S, PC',
            'Fecha' => '10-03-2020',
            'Precio' => 'Gratis',
            'Descuento' => '0'
        ),
        'SHTR-005' => array(
            'ID' => 'SHTR-005',
            'Nombre' => 'Halo Infinite',
            'Desarrollador' => '343 Industries',
            'Consola' => 'Xbox One, Xbox Series X/S, PC',
            'Fecha' => '08-12-2021',
            'Precio' => '1299 MXN',
            'Descuento' => '20%'
        ),
        'SHTR-006' => array(
            'ID' => 'SHTR-006',
            'Nombre' => 'Destiny 2',
            'Desarrollador' => 'Bungie',
            'Consola' => 'PlayStation 4, Xbox One, PC',
            'Fecha' => '06-09-2017',
            'Precio' => 'Gratis',
            'Descuento' => '0'
        ),
        'RPG-007' => array(
            'ID' => 'RPG-007',
            'Nombre' => 'Elden Ring',
            'Desarrollador' => 'FromSoftware',
            'Consola' => 'PlayStation 4, PlayStation 5, Xbox One, Xbox Series X/S, PC',
            'Fecha' => '25-02-2022',
            'Precio' => '1599 MXN',
            'Descuento' => '0'
        ),
        'RPG-008' => array(
            'ID' => 'RPG-008',
            'Nombre' => 'The Witcher 3: Wild Hunt',
            'Desarrollador' => 'CD Projekt',
            'Consola' => 'PlayStation 4, Xbox One, PC',
            'Fecha' => '19-05-2015',
            'Precio' => '899 MXN',
            'Descuento' => '30%'
        ),
        'RPG-009' => array(
            'ID' => 'RPG-009',
            'Nombre' => 'Final Fantasy XVI',
            'Desarrollador' => 'Square Enix',
            'Consola' => 'PlayStation 5',
            'Fecha' => '16-06-2023',
            'Precio' => '1799 MXN',
            'Descuento' => '0'
        ),
        'OPNW-010' => array(
            'ID' => 'OPNW-010',
            'Nombre' => 'Red Dead Redemption 2',
            'Desarrollador' => 'Rockstar Games',
            'Consola' => 'PlayStation 4, Xbox One, PC',
            'Fecha' => '26-10-2018',
            'Precio' => '1599 MXN',
            'Descuento' => '20%'
        ),
        'OPNW-011' => array(
            'ID' => 'OPNW-011',
            'Nombre' => 'The Legend of Zelda: Breath of the Wild',
            'Desarrollador' => 'Nintendo',
            'Consola' => 'Nintendo Switch',
            'Fecha' => '03-03-2017',
            'Precio' => '1399 MXN',
            'Descuento' => '10%'
        ),
        'OPNW-012' => array(
            'ID' => 'OPNW-012',
            'Nombre' => 'Grand Theft Auto V',
            'Desarrollador' => 'Rockstar North',
            'Consola' => 'PlayStation 3, PlayStation 4, Xbox 360, Xbox One, PC',
            'Fecha' => '17-09-2013',
            'Precio' => '999 MXN',
            'Descuento' => '0'
        )
    );
    
    function Authentication($user, $pass) {
        global $usuarios;
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
        
    $app->post('/autenticacion', function ($request, $response, $args) {

        $user = $request->getHeader('user')[0];
        $pass = $request->getHeader('pass')[0];
    
        $resAuth = Authentication($user, $pass);
        $resAuthObj = json_decode($resAuth);
        $response->getBody()->write($resAuth);
    
        return $response;
    });

    $app->get('/productos[/{categoria}]', function (Request $request, Response $response, $args) {

        global $productos;
    
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

        global $detalles;
    
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
            $categoria = strtolower($args['categoria']);
    
            $response->getBody()->write(json_encode([
                "code" => "200",
                "message" => "Operacion exitosa",
                "status" => "success"
            ]));
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
           
            $clave = strtoupper($args['clave']);
    
           
            $body = $request->getBody()->getContents();
            $detalles = json_decode($body, true);
    
            if ($detalles) {
                $response->getBody()->write(json_encode([
                    "code" => "200",
                    "message" => "Detalles del producto actualizados exitosamente",
                    "data" => $detalles,
                    "status" => "success"
                ]));
            } else {
             
                $response->getBody()->write(json_encode([
                    "code" => "400",
                    "message" => "Formato JSON invalido en el cuerpo de la solicitud",
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
          
            $clave = strtoupper($args['clave']);
    
            $response->getBody()->write(json_encode([
                "code" => "200",
                "message" => "Producto eliminado exitosamente",
                "status" => "success"
            ]));
        } else {
            
            $response->getBody()->write($resAuth);
        }
    
        return $response;
    });
    
    

    $app->run();
?>