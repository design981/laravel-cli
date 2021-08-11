<?php

namespace Design\LaravelCli\Console;

use Design\LaravelCli\Cli\WorkermanCli;
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
     * Execute the console command.
     *
     * @param WorkermanCli $class
     */
    public function handle(WorkermanCli $class)
    {

        if (!class_exists(Worker::class)) {
            $this->error('please install workerman,  run: composer require workerman/workerman');
            return;
        }

        $config = config('cli.workerman');

        Worker::$pidFile = $config['path'] . str_replace(['%SERVER%', '%IP%'], ['workerman_http', Env::get('SERVER_IP')], $config['pid']);

        switch ($this->argument('status'))
        {
            case 'start':
                $this->message("Generate Pid file path in " . Worker::$pidFile);
                break;
            case 'stop':
                $this->message("Closing....");
            default:
        }

        if($this->option('d')) Worker::$daemonize = true;

        $this->server = new Worker("{$config['protocol']}://{$config['ip']}:{$config['port']}");
        $this->server->count = $this->config['count'] ?? 2;
        $this->server->name = $this->config['name'] ?? 'workerman_http';

        $this->server->onMessage = [$class, 'onMessage'];

        try {
            Worker::runAll();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
