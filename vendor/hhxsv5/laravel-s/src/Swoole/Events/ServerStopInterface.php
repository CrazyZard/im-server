<?php

namespace Hhxsv5\LaravelS\Swoole\Events;

use Swoole\Http\Server;

interface ServerStopInterface
{
    public function __construct();

    public function handle(Server $server);
}