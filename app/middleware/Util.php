<?php

require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'middleware/Response.php';
require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'middleware/Security.php';


class Util
{
    public static function validate_fields($arr_data, $arr_fields, $arr_mandatory)
    {
        $ret = "";

        if (count($arr_mandatory) > 0 && count($arr_data) > 0) {

            foreach ($arr_fields as $value) {
                // verifica se é obrigatório
                if (in_array($value, $arr_mandatory)) {
                    //verifica se a chave existe nos dados e se está preenchido
                    if (!array_key_exists($value, $arr_data) || is_null($arr_data[$value]) || $arr_data[$value] == "") {
                        $ret = sprintf("O campo '%s' é de preenchimento obrigatório", $value);
                        break;
                    }
                }
            }    
        } elseif (count($arr_data) == 0) {
            $ret = "Nenhuma informação recebida";
        }
        return $ret;
    }

    public static function order_data($arr_fields, $arr_data)
    {
        $ret = [];

        if (is_array($arr_fields) && is_array($arr_data) && count($arr_fields) > 0 && count($arr_data) > 0) {
            foreach ($arr_fields as $key => $value) {
                $ret[$value] = $arr_data[$value];
            }
        }

        return $ret;
    }

    public static function validate_token($arr_headers)
    {
        $str_message = "";
        $int_http_code = 403;
        $str_token = "";

        if (is_array($arr_headers) && isset($arr_headers['Authorization'])) {
            $str_token = explode(' ', $arr_headers['Authorization'])[1];
        }

        if ($str_token != "" && Security::jwt_chk($str_token)) {
            return true;
        }

        $response = Response::message(403, "Acesso negado, token inválido");

        return Response::create($int_http_code, $response);
    }
}
