<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseClass;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';

require_once './controllers/UsuarioController.php';
require_once __DIR__ . "/middlewares/Logger.php";
require_once __DIR__ . "/middlewares/Comprobador.php";

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// MIDDLEWARES
// ===========================================================

$datosUsuarioMW = function (Request $request, RequestHandler $handler) {
  echo "Entro al MW" . PHP_EOL;

  $params = $request->getQueryParams();

  if(!isset($params["nombre"], $params["apellido"])){

    $response = new ResponseClass();

    $response->getBody()->write('{ error: "datos incorrectos" }');
    return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus(400);
  }
    
  $response = $handler->handle($request);
  
  echo "Salgo del MW " . PHP_EOL;

  return $response; 
};


// RUTAS
// ===========================================================

$app->get("/", function (Request $request, Response $response) {    
  echo "Entro al verbo \n";
  $response->getBody()->write(json_encode(array("estado", "funciona")));
  return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/usuario', function (Request $request, Response $response) { 
  $params = $request->getQueryParams();
   
  $nombre = $params["nombre"];
  $apellido = $params["apellido"];  

  echo "Entro al verbo" . PHP_EOL . PHP_EOL;

  $response->getBody()->write(json_encode(array("nombre" => $nombre, "apellido" => $apellido)));
  return $response->withHeader('Content-Type', 'application/json');
})
->add(new Comprobador(array("nombre")))
->add(new Comprobador(array("apellido")));

$app->get('/producto', function (Request $request, Response $response) {    
  $params = $request->getQueryParams();

  $nombre = $params["nombre"];
  $id = $params["id"];  

  echo "Entro al verbo" . PHP_EOL . PHP_EOL;

  $response->getBody()->write(json_encode(array("nombre" => $nombre, "id" => $id)));
  return $response->withHeader('Content-Type', 'application/json');
})->add(new Comprobador(array("id", "nombre")));

// ===========================================================
$app->run();
