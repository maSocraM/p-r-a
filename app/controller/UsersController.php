<?php

require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'middleware/Response.php';
require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'model/Db.php';
require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'middleware/Util.php';
require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'middleware/Security.php';


class UsersController
{
    protected $arr_fields = [];
    protected $arr_mandatory = [];

    public function __construct()
    {
        $this->arr_fields = ["email", "name", "password", "id"];
        $this->arr_mandatory = ["email", "name", "password"];
    }

    public function get($arr_params, $arr_body, $arr_headers)
    {
        Util::validate_token($arr_headers);
        
        $arr_params = count($arr_params) == 1 && $arr_params[0] == "" ? [] : $arr_params;

        $str_message = "";
        $records = 0;
        $page = 0;
        $total = 0;
        $int_http_code = 200;
        $str_query = "";
        $search_type = count($arr_params);
        
        $ordered_data = [];

        // Todos sem paginação
        if ($search_type === 0) {
            $str_query = "SELECT count(*) OVER() AS total_rows, u.id as iduser, u.email, u.name, dc.counter
                          FROM users u
                          LEFT JOIN drink_counters dc ON dc.iduser = u.id
                          ORDER BY u.id";
        // Usuário específico
        } elseif ($search_type === 1) {
            $str_query = "SELECT u.id as iduser, u.email, u.name, dc.counter
                          FROM users u
                          LEFT JOIN drink_counters dc ON dc.iduser = u.id
                          WHERE u.id = $1";
            $ordered_data['id'] = $arr_params[0];
        // Todos usuários paginado
        } elseif ($search_type === 2) {
            $str_query = "SELECT count(*) OVER() AS total_rows, u.id as iduser, u.email, u.name, dc.counter
                          FROM users u
                          LEFT JOIN drink_counters dc ON dc.iduser = u.id
                          ORDER BY u.id OFFSET $1 LIMIT $2";
            $ordered_data[0] = $arr_params[0];
            $ordered_data[1] = $arr_params[1];
        } else {
            $int_http_code = 404;
        }
       
        if ($int_http_code == 200) {

            if ($search_type == 0 || $search_type == 2) {
                $res = Db::select($str_query, $ordered_data, true);
               
                $total = $res['total_rows'];
                $ret = $res['result'];

                if ($search_type == 0) {
                    $records = $total;
                    $page = 1;
                }

                if ($search_type == 2) {
                    $records = $arr_params[1];
                    $page = $arr_params[0];
                }

            } else {
                $ret = Db::select($str_query, $ordered_data, false);
                if (count($ret) !== 0) {
                    $page = $total = $records = 1;
                } else {
                    $int_http_code = 404;
                }
            }
        }

        $response = Response::message($int_http_code, $str_message, $page, $records, $total, $ret);

        return Response::create($int_http_code, $response);

    }

    public function post($arr_params, $arr_body, $arr_headers)
    {
        $int_http_code = 400;
        $str_message = Util::validate_fields($arr_body, $this->arr_fields, $this->arr_mandatory);
        $str_query = "INSERT INTO users (email, name, password) VALUES ($1, $2, $3)";
        $records = 0;
        $ret = [];
        
        if ($str_message == "") {
            $insert_fields = $this->arr_fields;
            unset($insert_fields[3]);
            
            $ordered_data = Util::order_data($insert_fields, $arr_body);
            $ordered_data['password'] = Security::psw_encrypt($ordered_data['password']);
            $ret = Db::query($str_query, $ordered_data);

            print_r($ret);
            
            $records = $ret ? 1 : 0;
            $int_http_code = !$ret ? 400 : 200;            
        }

        $response = Response::message($int_http_code, $str_message, $records, $records, $records, $ret);

        return Response::create($int_http_code, $response);
    }

    public function put($arr_params, $arr_body, $arr_headers)
    {

        $int_http_code = 400;
        $str_message = Util::validate_fields($arr_body, $this->arr_fields, $this->arr_mandatory);
        $str_query = "";
        $search_type = count($arr_params);
        $ret = [];
        $page = 1;
        $records = 0;
        $total = 0;

        if (count($arr_params) == 1 && $str_message == "") {

            Util::validate_token($arr_headers);
            $user_data = Security::jwt_body_data(explode(' ', $arr_headers['Authorization'])[1]);

            if (array_key_exists('id', $user_data)) {

                if ($user_data['id'] == $arr_params[0]) {

                    $arr_body['id'] = $user_data['id'];

                    $ordered_data = Util::order_data($this->arr_fields, $arr_body);
                    
                    $ordered_data['password'] = Security::psw_encrypt($ordered_data['password']);
                    
                    // array_push($ordered_data, $user_data['id']);
                    
                    $str_query = "UPDATE users SET email = $1, name = $2, password = $3 WHERE id = $4";
    
                    $res = Db::query($str_query, $ordered_data);
    
                    if ($res) {
                        $int_http_code = 200;
                        $records = 1;
                        $total = 1;
                    } else {
                        $int_http_code = 400;
                    }
    
                } else {
                    $int_http_code = 301;
                    $str_message = "Usuário somente pode alterar seu próprio registro";    
                }
    
            } else {
                $int_http_code = 301;
                $str_message = "Token inválido ou expirado";
            }

        } else {
            $int_http_code = 404;
        }

        $response = Response::message($int_http_code, $str_message, $page, $records, $total, $ret);

        return Response::create($int_http_code, $response);
    }
}
