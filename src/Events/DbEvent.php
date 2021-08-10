<?php

namespace Design\LaravelCli\Events;

use Design\LaravelCli\Contracts\OnResponseEventContracts;
use Exception;
use Illuminate\Support\Facades\DB;

class DbEvent implements OnResponseEventContracts
{

    /**
     * begin transaction
     *
     * @throws Exception
     */
    public static function beginTransaction()
    {
        DB::beginTransaction();
        OnResponseEvent::register(new self(), 'transaction');
    }

    /**
     * commit commit close event
     */
    public static function commit()
    {
        DB::commit();
        OnResponseEvent::closeEvent('transaction');
    }

    /**
     * handle
     *
     * @throws Exception
     */
    public function handle()
    {
        DB::rollBack();
    }
}
