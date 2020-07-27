<?php


class Parameters
{

    protected $str_class = '';
    protected $arr_params = [];
    protected $arr_headers = [];

    public function __construct()
    {

        $arr_url = explode(
                    '/',
                    ltrim($_SERVER['REQUEST_URI'], '/')
        );

        if (is_array($arr_url) && count($arr_url) > 0) {

            $this->str_class = ucfirst($arr_url[0]) . "Controller";

            if (count($arr_url) > 1) {
                for ($i = 1; $i < count($arr_url); $i++) {
                    $this->arr_params[] = $arr_url[$i];
                }
            }
    
        }

        $this->arr_headers = getallheaders();
    }

    public function get_parameters() {
        return [$this->str_class, $this->arr_params, $this->arr_headers];
    }

}