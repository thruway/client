<?php

namespace Thruway\Peer;

use React\EventLoop\Factory;
use Thruway\Transport\ClientTestTransportProvider;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage You must add exactly one transport provider prior to starting
     */
    public function testClientMustHaveTransportProvider()
    {
        $loop = Factory::create();

        $client = new Client('some.realm', $loop);

        $client->start();
    }

    public function testClientSendsHelloMessage()
    {
        $client = new Client('some.realm', Factory::create());

        $client->addTransportProvider(new ClientTestTransportProvider());
    }
}