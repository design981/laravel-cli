<?php

namespace Design\LaravelCli\Console;

use Design\LaravelCli\Cli\Workerman\Message;
use Design\LaravelCli\Cli\Workerman\RequestFactory;
use Design\LaravelCli\Cli\Workerman\ResponseFactory;
use Design\LaravelCli\Cli\Workerman\Start;
use Exception;
use App\Http\Kernel;
use Illuminate\Support\Env;
use Workerman\Worker;

class Workerman extends Command
{
    protected Worker $server;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cli:workerman
    {status : The status to workerman status start/stop/reload/status/connections }
    {--d : run in deamon}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'run laravel for workerman';

    /**
     * @param Kernel $kernel
     */
    public function handle(Kernel $kernel)
    {

        if (!class_exists(Worker::class)) {
            $this->error('please install workerman,  run: composer require workerman/workerman');
            return;
        }

        $config = config('cli.workerman');

        Worker::$pidFile = $config['path'] . str_replace(['%SERVER%', '%IP%'], ['workerman_http', Env::get('SERVER_IP')], $config['pid']);
        Worker::$logFile = $config['path'] . 'workerman.log';

        switch ($this->argument('status')) {
            case 'start':
                $this->message("Generate Pid file path in " . Worker::$pidFile);
                break;
            case 'stop':
                $this->message("Closing....");
            default:
        }

        if ($this->option('d')) Worker::$daemonize = true;

        $this->server = new Worker("{$config['protocol']}://{$config['ip']}:{$config['port']}");
        $this->server->count = $config['count'] ?? 2;
        $this->server->name = $config['name'] ?? 'workerman_http';


        $this->server->onMessage = new Message(
            new RequestFactory(),
            new ResponseFactory(),
            $kernel
        );
        $this->server->onWorkerStart = new Start;

        try {
            Worker::runAll();
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
