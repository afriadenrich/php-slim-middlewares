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
require_once './middlewares/AuthMiddleware.php';

require_once './controllers/UsuarioController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// MIDLEWWARES
$usuarioMW = function (Request $request, RequestHandler $requestHandler) {
  $params = $request->getQueryParams();

  echo "Entro al Middleware \n";


  if(isset($params["nombre"], $params["apellido"])){ 
    $response = $requestHandler->handle($request);
    echo "Salgo del verbo al middleware \n";

    return $response;
  } else { 
    echo "NO entro al verbo \n";

    $response = new ResponseClass();
    $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
    return $response->withHeader('Content-Type', 'application/json');
  }
};

// Routes

$app->get("/", function (Request $request, Response $response) {    
  var_dump($_ENV);

  echo "Entro al verbo \n";
  $response->getBody()->write(json_encode(array("estado", "funciona")));
  return $response->withHeader('Content-Type', 'application/json');
})->add(new AuthMiddleware("Administrador"));

$app->get('/usuario', function (Request $request, Response $response) {    
  $params = $request->getQueryParams();
  $nombre = $params["nombre"];
  $apellido = $params["apellido"];  

  echo "Entro al verbo \n";

  $response->getBody()->write(json_encode(array($nombre, $apellido)));
  return $response->withHeader('Content-Type', 'application/json');
})->add(new AuthMiddleware("Cocinero"));

$app->run();
