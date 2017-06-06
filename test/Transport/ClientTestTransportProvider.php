<?php

namespace Thruway\Transport;

use React\EventLoop\LoopInterface;
use Thruway\Peer\ClientInterface;

class ClientTestTransportProvider implements ClientTransportProviderInterface
{
    public function startTransportProvider(ClientInterface $peer, LoopInterface $loop)
    {

    }
}