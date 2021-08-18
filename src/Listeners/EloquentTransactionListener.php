<?php

namespace Design\LaravelCli\Listeners;

use Design\LaravelCli\Contracts\OnResponseEventContracts;
use Illuminate\Support\Facades\DB;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request;

class EloquentTransactionListener implements OnResponseEventContracts
{

    public function handle(TcpConnection $conn, Request $request)
    {
        while (DB::transactionLevel()) {
            DB::rollBack();
        }
    }
}
