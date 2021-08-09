<?php

namespace Design\LaravelCli\Console;

use Illuminate\Console\Command as IlluminateCommand;

class Command extends IlluminateCommand
{
    /**
     * Display blue message
     *
     * @param        $message
     * @param string $color
     */
    public function message($message, string $color = 'blue')
    {
        $this->getOutput()->writeln('<fg=' . $color . '>' . $message . '</fg=' . $color . '>');
    }
}
