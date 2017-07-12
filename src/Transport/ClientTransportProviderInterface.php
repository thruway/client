<?php

namespace Thruway\Transport;

use React\EventLoop\LoopInterface;
use Thruway\Peer\Client;

interface ClientTransportProviderInterface extends TransportProviderInterface {
    /**
     * Start transport provider
     *
     * @param \Thruway\Peer\Client $peer
     * @param \React\EventLoop\LoopInterface $loop
     */
    public function startTransportProvider(Client $peer, LoopInterface $loop);
}
