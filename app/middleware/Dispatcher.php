<?php


class Dispatcher
{

    protected $str_class_name = "";
    protected $str_method = "";
    protected $arr_params = [];
    protected $arr_body = [];
    protected $arr_headers = [];

    public function __construct($str_class_name, $str_method, $arr_params, $arr_body, $arr_headers)
    {
        $this->str_class_name = $str_class_name;
        $this->str_method = $str_method;
        $this->arr_params = $arr_params;
        $this->arr_body = $arr_body;
        $this->arr_headers = $arr_headers;
    }

    public function load()
    {
        require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $this->str_class_name . '.php';
        $obj = new $this->str_class_name();
        $obj->{$this->str_method}($this->arr_params, $this->arr_body, $this->arr_headers);
    }
}