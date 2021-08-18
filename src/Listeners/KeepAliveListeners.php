<?php

namespace Design\LaravelCli\Listeners;

use Design\LaravelCli\Contracts\OnResponseEventContracts;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;

class KeepAliveListeners implements OnResponseEventContracts
{
    public function handle(TcpConnection $conn, Request $request)
    {
        if ($request->protocolVersion() == '1.0' && $request->header('connection') != 'keep-alive') {
            $conn->close();
        }
    }
}
