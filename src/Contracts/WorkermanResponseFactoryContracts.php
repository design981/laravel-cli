<?php

namespace Design\LaravelCli\Contracts;

use Symfony\Component\HttpFoundation\Response;
use Workerman\Connection\TcpConnection as WorkermanTcpConnection;

interface WorkermanResponseFactoryContracts
{
    /**
     * @param Response $response
     * @param WorkermanTcpConnection $conn
     */
    public function send(Response $response, WorkermanTcpConnection $conn): void;
}
