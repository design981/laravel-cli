<?php

namespace Design\LaravelCli\Cli\Workerman;

use Design\LaravelCli\Contracts\WorkermanResponseFactoryContracts;
use Symfony\Component\HttpFoundation\Response;
use Workerman\Connection\TcpConnection as WorkermanTcpConnection;
use Workerman\Protocols\Http\Response as WorkermanResponse;

class ResponseFactory implements WorkermanResponseFactoryContracts
{

    /**
     * @param Response $response
     * @param WorkermanTcpConnection $conn
     */
    public function send(Response $response, WorkermanTcpConnection $conn): void
    {
        $conn->send((new WorkermanResponse())
            ->withStatus(200)
            ->withHeaders($response->headers->allPreserveCaseWithoutCookies())
            ->withBody($response->getContent())
        );
    }
}
