<?php

namespace Thruway\Transport;

use React\EventLoop\LoopInterface;
use Thruway\Peer\ClientInterface;

class ClientTestTransportProvider implements ClientTransportProviderInterface
{
    /** @var ClientInterface */
    private $peer;
    private $loop;

    public function startTransportProvider(ClientInterface $peer, LoopInterface $loop)
    {
        $this->peer = $peer;
        $this->loop = $loop;
    }

    public function sendOnOpenToClient(TransportInterface $transport)
    {
        $this->peer->onOpen($transport);
    }
}