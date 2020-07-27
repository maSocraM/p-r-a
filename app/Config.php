<?php


class Config
{
    private $arr_config;

    public function __construct()
    {

        $this->arr_config = [
            'app_name' => 'StautGroup job test',
            'app_key' => 'iKud4lsx35YXkqoBiAJjHiaZmtuphoevm+gl0wgmK4c=',
            'site_root' => $_SERVER['DOCUMENT_ROOT'],
            'system_root' => __DIR__,
            'db_host' => 'some_host',
            'db_port' => '0000',
            'db_name' => 'some_db',
            'db_user' => 'some_user',
            'db_pass' => 'some_pass',
            'debug' => true,
            'cors_allow_remote' => '*',
            'token_expires' => 5,
            'http_responses' => [
                200 => 'Ok',
                400 => 'Bad Request',
                401 => 'Unauthorized',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                418 => 'I\'m a teapot',
                500 => 'Internal Server Error',
                503 => 'Service Unavailable'
            ]
        ];

        $this->set_env();
    }

    private function set_env()
    {
        foreach ($this->arr_config as $key => $value) {
            putenv(sprintf("%s=%s", strtoupper($key), $value));
        }
    }

    public function config(String $var) : String
    {
        return getenv(strtoupper($var));
    }
}
