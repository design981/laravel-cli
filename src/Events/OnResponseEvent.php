<?php

namespace Design\LaravelCli\Events;

use Design\LaravelCli\Contracts\OnResponseEventContracts;

class OnResponseEvent
{
    /**
     * @var OnResponseEventContracts[] $events
     */
    protected static array $events = [];

    /**
     * register response event
     *
     * @param OnResponseEventContracts $event
     * @param null $key
     */
    public static function register(OnResponseEventContracts $event, $key = null): void
    {
        if ($key === null) {
            self::$events[] = $event;
        } else {
            self::$events[$key] = $event;
        }
    }

    /**
     * close response event
     *
     * @param $key
     */
    public static function closeEvent($key): void
    {
        unset(self::$events[$key]);
    }

    /**
     * handle response event
     */
    public static function handle(): void
    {
        foreach (self::$events as $key => $event) {
            $event->handle();
            self::closeEvent($key);
        }
    }
}
