<?php

require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'middleware/Response.php';
require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'model/Db.php';
require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'middleware/Util.php';
require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'middleware/Security.php';


class CarsController
{
    protected $arr_fields = [];
    protected $arr_mandatory = [];
    protected $str_system_root = "";
    protected $arr_http_responses = [];

    public function __construct()
    {
        $this->arr_fields = ["title", "brand", "price", "age"];
        $this->arr_mandatory = ["title", "brand", "price", "age"];
        $this->arr_http_responses = [
                200 => 'Ok',
                400 => 'Bad Request',
                401 => 'Unauthorized',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                418 => 'I\'m a teapot',
                500 => 'Internal Server Error',
                503 => 'Service Unavailable'
        ];        
        $this->str_system_root = sprintf("%s%s%s%s%s", getenv('SITE_ROOT'), DIRECTORY_SEPARATOR, "..", DIRECTORY_SEPARATOR, "response.json");
    }

    public function get($arr_params, $arr_body, $arr_headers)
    {
        $int_http_code = array_rand($this->arr_http_responses);

        $str_message = $int_http_code != 200 ? "Dummy error" : "";
        $records = 0;
        $total = 0;
        $page = 0;
        $ret = [];
        
        $int_http_code = 200;

        if ($int_http_code == 200) {
            $ret = json_decode(file_get_contents($this->str_system_root), true);
            $records = count($ret);
            $total = $records;
            $page = 1;
        }

        $response = Response::message($int_http_code, $str_message, $page, $records, $total, $ret);

        return Response::create($int_http_code, $response);

    }

    public function post($arr_params, $arr_body, $arr_headers)
    {
        $int_http_code = getenv('HTTP_RESPONSES')[rand(0, (count(getenv('HTTP_RESPONSES') - 1)))];
        $int_http_code = 200;
        $str_message = $int_http_code != 200 ? "Dummy error" : "";
        $records = 0;
        $total = 0;
        $page = 0;
        $ret = [];

        if ($int_http_code == 200) {

            $str_message = Util::validate_fields($arr_body, $this->arr_fields, $this->arr_mandatory);

            if ($str_message == "") {
                $ret = Util::order_data($this->arr_fields, $arr_body);
                $records = 1;
                $total = 1;
                $page = 1;
            }

        }

        $response = Response::message($int_http_code, $str_message, $page, $records, $total, $ret);

        return Response::create($int_http_code, $response);
    }

}
