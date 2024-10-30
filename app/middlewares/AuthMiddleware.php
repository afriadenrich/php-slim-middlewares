<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseClass;

class AuthMiddleware {

    private $perfil = "";

    public function __construct($perfil) {
        $this->perfil = $perfil;
    }

    public function __invoke(Request $request, RequestHandler $requestHandler){
        echo "Entro al authMW \n";
        $response = new ResponseClass();

        $params = $request->getQueryParams();

        if(isset($params["credenciales"])){

            $credenciales = $params["credenciales"];

            if($credenciales === $this->perfil){
                // Pasa al verbo
                $response = $requestHandler->handle($request);
                
            } else {
                // No es admin

                $response->getBody()->write(json_encode(array("error" => "No sos " . $this->perfil)));
            }
        } else {
            // No hay credenciales
            $response->getBody()->write(json_encode(array("error" => "No hay credenciales")));
        }

        echo "Salgo del authMW \n";

        return $response;

    }
}