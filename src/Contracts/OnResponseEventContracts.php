<?php

namespace Design\LaravelCli\Contracts;

use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;

interface OnResponseEventContracts
{

    public function handle(TcpConnection $conn, Request $request);
}
