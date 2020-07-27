<?php


class Request
{
    private $raw_input;
    private $arr_input;

    public function __construct()
    {
        $this->set_raw_input();
        $this->set_arr_input();
    }

    public function get($mx_key)
    {
        $mx_ret = null;

        if (isset($this->arr_input[$mx_key])) {
            $mx_ret = $this->arr_input[$mx_key];
        }

        return $mx_ret;
    }

    public function getAll()
    {
        return $this->arr_input;
    }

    private function set_raw_input()
    {
        $this->raw_input = file_get_contents('php://input');
    }

    private function set_arr_input()
    {
        $this->arr_input = (array)json_decode($this->raw_input);
    }

}