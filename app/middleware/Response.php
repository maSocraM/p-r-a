<?php


class Response
{
    public static function create($http_code, $message = null, $cors_remote = null)
    {

        if (is_null($cors_remote)) {
            $cors_remote = getenv('CORS_ALLOW_REMOTE');
        }

        header("Access-Control-Origin: " . $cors_remote);
        header("Content-Type: application/json; charset=UTF-8");
        header("Accept: application/json");
        http_response_code($http_code);

        if (is_null($cors_remote)) {
            $cors_remote = getenv('cors_allow_remote');
        }

        if (is_null($message) && $message == "") {
            $message = self::message($http_code);
        }

        echo json_encode($message);
        exit;
    }

    public static function message($code = 0, $message = "", $page = 0, $records = 0, $total = 0, $data = [])
    {
        return [
            'code' => $code,
            'message' => $message,
            'page' => $page,
            'records' => $records,
            'total' => $total,
            'data' => $data
        ];
    }

}
