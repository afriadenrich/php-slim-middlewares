<?php

use Slim\Psr7\Response as ResponseClass;

class Logger 
{
    public static function LogOperacion($request, $next)
    {
        echo "Entro al MW" . PHP_EOL;

        $params = $request->getQueryParams();

        if(!isset($params["nombre"], $params["apellido"])){

            $response = new ResponseClass();

            $response->getBody()->write('{ error: "datos incorrectos" }');
            return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
        }
            
        $response = $next->handle($request);
        
        echo "Salgo del MW " . PHP_EOL;

        return $response; 
    }
}