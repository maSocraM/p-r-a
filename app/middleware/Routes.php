<?php


class Routes
{
    private $http_method = "";
    private $str_class_name = "";
    private $arr_params = [];
    private $app = null;

    public function __construct(Config $app, String $cls_name, Array $params)
    {
        $this->http_method = strtolower($_SERVER["REQUEST_METHOD"]);
        $this->str_class_name = $cls_name;
        $this->arr_params = $params;
        $this->app = $app;
    }

    public function routes_check()
    {
        $bol_ret = $this->check_controller();

        if ($bol_ret === true) {
            $bol_ret = $this->check_method();
        }

        return $bol_ret;
    }

    public function get_method()
    {
        return $this->http_method;
    }

    protected function check_controller()
    {
        $bol_ret = false;

        if (!is_null($this->str_class_name) && $this->str_class_name != '') {
            $bol_ret = file_exists($this->app->config('system_root') . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $this->str_class_name . '.php');
        }

        return $bol_ret;
    }

    protected function check_method()
    {
        require getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $this->str_class_name . '.php';
        $rf = new ReflectionClass($this->str_class_name);
        return $rf->hasMethod($this->http_method);
    }

}