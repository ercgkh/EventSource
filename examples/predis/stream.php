<?php

use Igorw\EventSource\Stream;

require __DIR__.'/vendor/.composer/autoload.php';

set_time_limit(0);

$redis = new Predis\Client();
$pubsub = $redis->pubSub();
$pubsub->subscribe('notifications');

foreach (Stream::getHeaders() as $name => $value) {
    header("$name: $value");
}

$stream = new Stream();

foreach ($pubsub as $message) {
    if ('message' === $message->kind) {
        $stream
            ->event()
                ->setData($message->payload)
            ->end()
            ->flush();
    }
}
