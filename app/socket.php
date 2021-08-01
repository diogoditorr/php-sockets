<?php

namespace MyApp;

use Exception;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use ReflectionClass;

function getMethodsName(object $obj): array|bool {
    if (get_class($obj)) {
        return array_map(fn($method) => $method->name, (new ReflectionClass(get_class($obj)))->getMethods());
    } else {
        return false;
    }
}

class Socket implements MessageComponentInterface {

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $connection)
    {
        $this->clients->attach($connection);

        echo "New connection! ({$connection->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        /**
        * @var ConnectionInterface $client
        */
        foreach ($this->clients as $client) {
            if ($from->resourceId == $client->resourceId) {
                continue;
            }

            $client->send($from);

            $a = $this->getMethodsName($client);

            var_dump($a);

            $client->send("Client $from->resourceId said $msg");
        }
    }
    
    public function onClose(ConnectionInterface $conn) {}
    
    public function onError(ConnectionInterface $conn, Exception $e) {}

    public function getMethodsName(object $obj): array|bool {
        if (get_class($obj)) {
            return array_map(fn($method) => $method->name, (new ReflectionClass(get_class($obj)))->getMethods());
        } else {
            return false;
        }
    }

    public function getPropertiesName(object $obj): array|bool {
        if (get_class($obj)) {
            return array_map(fn($property) => $property->name, (new ReflectionClass(get_class($obj)))->getProperties());
        } else {
            return false;
        }
    }
}