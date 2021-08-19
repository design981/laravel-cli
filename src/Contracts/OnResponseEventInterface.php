<?php

namespace Design\LaravelCli\Contracts;

use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;

interface OnResponseEventInterface
{

    public function handle(TcpConnection $conn, Request $request);
}
