<?php

namespace Design\LaravelCli;

use Design\LaravelCli\Console\Workerman;
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
}
