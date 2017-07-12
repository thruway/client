<?php

namespace Thruway\Transport;

use React\EventLoop\LoopInterface;
use Thruway\Peer\Client;

class ClientTestTransportProvider implements ClientTransportProviderInterface
{
    /** @var Client */
    private $peer;
    private $loop;

    public function startTransportProvider(Client $peer, LoopInterface $loop)
    {
        $this->peer = $peer;
        $this->loop = $loop;
    }

    public function sendOnOpenToClient(TransportInterface $transport)
    {
        $this->peer->onOpen($transport);
    }
}