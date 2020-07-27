<?php

require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'middleware/Response.php';
require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'model/Db.php';
require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'middleware/Util.php';
require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'middleware/Security.php';


class LoginController
{
    protected $arr_fields = [];
    protected $arr_mandatory = [];

    public function __construct()
    {
        $this->arr_fields = ["email", "password"];
        $this->arr_mandatory = ["email", "password"];
    }

    public function post($arr_params, $arr_body, $arr_headers)
    {
        $int_http_code = 400;
        $str_message = Util::validate_fields($arr_body, $this->arr_fields, $this->arr_mandatory);
        $str_query = "
                    SELECT '' as token, u.id as iduser, u.email, u.name, dc.counter
                    FROM users u
                    LEFT JOIN drink_counters dc ON dc.iduser = u.id
                    WHERE email=$1 AND password=$2;
                    ";
        $records = 0;
        $total = 0;
        $page = 0;
        $ret = [];
        
        if ($str_message == "") {
            $odered_data = Util::order_data($this->arr_fields, $arr_body);
            $odered_data['password'] = Security::psw_encrypt($odered_data['password']);
            $res = Db::query($str_query, $odered_data);

            if ($res) {

                $row = pg_fetch_assoc($res);
                $row['token'] = Security::jwt_enc($row['iduser']);

                $page = $records = $total = 1;

                $int_http_code = 200;

                $ret = $row;
                
            } else {
                $int_http_code = 400;
                $str_message = "Usuário não existe ou senha inválida";
            }
            
        }

        $response = Response::message($int_http_code, $str_message, $page, $records, $total, $ret);

        return Response::create($int_http_code, $response);
    }

}