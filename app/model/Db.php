<?php

require_once getenv('SYSTEM_ROOT') . DIRECTORY_SEPARATOR . 'middleware/Response.php';


class Db
{
    public static function connect()
    {
        $str_conn = sprintf("host=%s port=%s dbname=%s user=%s password=%s connect_timeout=5", 
                            getenv('DB_HOST'), 
                            getenv('DB_PORT'),
                            getenv('DB_NAME'),
                            getenv('DB_USER'),
                            getenv('DB_PASS')
        );
        return pg_connect($str_conn);        
    }

    public static function disconnect($db)
    {
        pg_close($db);
    }

    public static function get_current_pagination($items_per_page, $page)
    {
        return ($items_per_page * ($page - 1)) + 1;
    }

    public static function select($str_query, $arr_params, $bol_count = false)
    {
        $arr_ret = [];
        $conn = self::connect();
       
        $res = pg_query_params($conn, $str_query, $arr_params);
        
        if ($res !== false) {

            $result = pg_fetch_all($res);

            $total_rows = 0;

            if ($bol_count === true && $result !== false) {

                array_walk($result, function (&$value) use (&$total_rows) {
                    if ($total_rows != $value["total_rows"]) {
                        $total_rows = $value["total_rows"];
                    }
                    unset($value["total_rows"]);
                });

                $arr_ret = ['total_rows' => $total_rows, 'result' => $result];
            }
            
            if ($result !== false && count($result) > 0) {
                $arr_ret = $result;
            }

        }

        self::disconnect($conn);

        return $arr_ret;

    }

    public static function query($str_query, $arr_params)
    {
        $ret = "";
        $conn = self::connect();
        $result = @pg_query_params($conn, $str_query, $arr_params);

        if ($result !== false) {
            $ret = $result;
        } else {
            self::get_redir_db_errors($conn);
        }

        self::disconnect($conn);
        return $ret;
    }

    public static function get_redir_db_errors($conn)
    {
        $str_error = @pg_last_error($conn);
        $str_message = "";
        $int_http_code = 500;

        if (strpos($str_error, 'already exists') !== false) {
            $str_message = "Registro já existe";
            $int_http_code = 409;
        } else {
            $str_message = $str_error;
        }

        $response = Response::message($int_http_code, $str_message, 0, 0, 0, [$str_error]);

        return Response::create($int_http_code, $response);

    }


}
