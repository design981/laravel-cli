<?php

namespace Design\LaravelCli;

use Design\LaravelCli\Console\Workerman;
use Design\LaravelCli\Listeners\EloquentTransactionListener;
use Design\LaravelCli\Listeners\KeepAliveListeners;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class LaravelCliServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfig();
        $this->registerConsoleCommands();
        $this->registerSubscribe();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * merge config
     */
    protected function mergeConfig()
    {
        $this->mergeConfigFrom(
            $this->getConfigPath(),
            'cli.workerman'
        );
    }

    /**
     * @return string
     */
    protected function getConfigPath(): string
    {
        return __DIR__ . '/../config/workerman.php';
    }

    /**
     * register console commands
     */
    protected function registerConsoleCommands()
    {
        $this->commands([
            Workerman::class
        ]);
    }

    /**
     * 订阅
     */
    protected function registerSubscribe()
    {
        // 事务回滚
        Event::listen('workerman.response', EloquentTransactionListener::class);
        // keep-alive
        Event::listen('workerman.response', KeepAliveListeners::class);
    }
}
