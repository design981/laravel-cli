<?php

namespace Design\LaravelCli\Cli\Workerman;

use Illuminate\Support\Facades\DB;
use Workerman\Timer;

class Start
{
    /** on worker start */
    public function __invoke()
    {
        $this->heartbeatTimerTickt();
    }

    /**
     * db heartbeat
     */
    protected function heartbeatTimerTickt()
    {
        Timer::add(25, function () {
            Db::select('select 1 limit 1');
        });
    }
}
