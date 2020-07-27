<?php


class Security
{

    public static function psw_encrypt($str_psw)
    {
        return substr(hash('sha256', $str_psw), 0, 128) ;
    }

    public static function psw_compare($str_input, $str_stored)
    {
        return ($str_stored == substr(hash('sha256', $str_input), 0, 128));
    }

    // https://blog.codeexpertslearning.com.br/criando-primeiro-token-jwt-4eab1b811400
    public static function jwt_enc($id)
    {

        // jwt expira em 3 minutos
        $dttm_expires = (new DateTime())->add(new DateInterval('PT3M'))->getTimeStamp();

        $arr_header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $arr_body = [
            'id' => $id,
            // 'issuer' => $_SERVER['HTTP_REFERER'],
            'expires' => $dttm_expires
        ];

        $obj_header = json_encode($arr_header);
        $obj_body = json_encode($arr_body);

        $obj_header = base64_encode($obj_header);
        $obj_body = base64_encode($obj_body);

        $str_sig = hash_hmac('sha256', sprintf("%s.%s", $obj_header, $obj_body), getenv('APP_KEY'), true);
        $str_sig = base64_encode($str_sig);

        $str_token = sprintf("%s.%s.%s", $obj_header, $obj_body, $str_sig);

        return $str_token;
    }

    public static function jwt_chk($str_token)
    {
        list($header, $body, $key) = explode('.', $str_token);
        $hash = hash_hmac('sha256', sprintf("%s.%s", $header, $body), getenv('APP_KEY'), true);
        $str_sig = base64_encode($hash);

        // TODO: verificar a data de validade do certificado

        return ($key == $str_sig);
    }

    public static function jwt_body_data($str_token)
    {
        list($header, $body, $key) = explode('.', $str_token);
        $body = base64_decode($body);

        return (array)json_decode($body);
    }

}
