<?php

require_once 'Config.php';
require_once '../app/middleware/Parameters.php';
require_once '../app/middleware/Routes.php';
require_once '../app/middleware/Request.php';
require_once '../app/middleware/Response.php';
require_once '../app/middleware/Dispatcher.php';


class Bootstrap
{
    protected $str_controller = "";
    protected $str_method = "";
    protected $arr_params = [];
    protected $arr_headers = [];
    protected $bol_routes = false;
    protected $arr_request = [];
    protected $obj_request = null;
    public $app;

    public function __construct()
    {
        $this->debug_mode();

        $this->app = new Config();
        $parameters = new Parameters();
       
        list($this->str_controller, $this->arr_params, $this->arr_headers) = $parameters->get_parameters();

        $routes = new Routes($this->app, $this->str_controller, $this->arr_params);
        $this->bol_routes = $routes->routes_check();
        $this->str_method = $routes->get_method();
        $this->obj_request = new Request();
    }

    public function run()
    {
        if ($this->bol_routes) {
            $dispatcher = new Dispatcher($this->str_controller, $this->str_method, $this->arr_params, $this->obj_request->getAll(), $this->arr_headers);
            $dispatcher->load();
        } else {
            $message = Response::message(404, "Recurso não encontrado");
            return Response::create(404, $message);
        }
    }

    private function debug_mode()
    {
        if (isset($app['debug']) && $app['debug'] == true) {
            error_reporting(E_ALL);
            ini_set('display_errors', 'on');
            ini_set('display_startup_errors', true);
        }
    }

}
