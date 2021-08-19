<?php

namespace Design\LaravelCli\Contracts;

use Symfony\Component\HttpFoundation\Response;
use Workerman\Connection\TcpConnection as WorkermanTcpConnection;

interface WorkermanResponseFactoryInterface
{
    /**
     * @param Response $response
     * @param WorkermanTcpConnection $conn
     */
    public function send(Response $response, WorkermanTcpConnection $conn): void;
}
