<?php

namespace Design\LaravelCli\Contracts;

use Illuminate\Http\Request;
use Workerman\Connection\TcpConnection as WorkermanTcpConnection;
use Workerman\Protocols\Http\Request as WorkermanRequest;

interface WorkermanRequestFactoryInterface
{
    /**
     * @param WorkermanTcpConnection $conn
     * @param WorkermanRequest $workermanRequest
     * @return Request
     */
    public function create(WorkermanTcpConnection $conn, WorkermanRequest $workermanRequest): Request;
}
