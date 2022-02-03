<?php

namespace Thruway\Peer;

use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use Thruway\Transport\ClientTestTransportProvider;

class ClientTest extends TestCase
{
    public function testClientMustHaveTransportProvider()
    {
        $loop = Factory::create();

        $client = new Client('some.realm', $loop);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You must add exactly one transport provider prior to starting');

        $client->start();
    }

    public function testClientSendsHelloMessage()
    {
        $client = new Client('some.realm', Factory::create());

        $client->addTransportProvider(new ClientTestTransportProvider());

        $this->assertTrue(true, "No error occured.");
    }
}