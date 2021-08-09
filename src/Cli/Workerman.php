<?php
namespace Design\LaravelCli\Cli;

use Design\LaravelCli\Cli\Adapter\WorkermanAdapter;

class Workerman extends WorkermanAdapter
{

    public function onMessage($conn, $request)
    {
        $this->conn = $conn;

        $this->request = $request;

        $response = $this->request();

        $this->response($response);
    }
}