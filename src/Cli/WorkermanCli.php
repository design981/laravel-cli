<?php

namespace Design\LaravelCli\Cli;

use Design\LaravelCli\Cli\Adapter\WorkermanAdapter;
use Illuminate\Support\Facades\DB;
use Workerman\Timer;
use Workerman\Worker;

class WorkermanCli
{
    public function onWorkerStart()
    {
        // db heartbeat
        $this->heartbeatTimerTickt();
    }

    public function onMessage($conn, $request)
    {
        static $requestCount = 0;
        if (++$requestCount >= 100000) {
            $this->tryToRestart();
        }
        new WorkermanAdapter($conn, $request);
    }

    /**
     * max request restart
     */
    private function tryToRestart()
    {
        Worker::stopAll();
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
