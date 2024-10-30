<?php
use Slim\Psr7\Response as ResponseClass;

class Comprobador {
    private $camposAValidar;

    public function __construct($camposAValidar) {
        $this->camposAValidar = $camposAValidar;
    }

    public function __invoke($request, $handler){
        echo "Entro al MW" . PHP_EOL;

        $params = $request->getQueryParams();

        foreach ($this->camposAValidar as $key => $value) {
            if(!isset($params[$value])){

                $response = new ResponseClass();
    
                $response->getBody()->write('{ error: "datos incorrectos, falta '. $value . '" }');
                return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
            }
        }
            
        $response = $handler->handle($request);
        
        echo "Salgo del MW " . PHP_EOL;

        return $response; 
    }

    public function LogOperacion()
    {
       
    }
}