<?php

namespace Thruway\Peer;

use React\EventLoop\Factory;
use Thruway\Authentication\ClientJwtAuthenticator;
use Thruway\Authentication\ClientWampCraAuthenticator;
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

    public function testAddAuthenticators()
    {
        $client = new Client('some.realm', Factory::create());

        $this->assertEmpty($client->getAuthMethods());

        $client->addClientAuthenticator(new ClientJwtAuthenticator('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdXRoaWQiOiJhZG1pbiIsImF1dGhyb2xlcyI6WyJhZG1pbiJdfQ.rgRR61pbyg-qimH1zdh_naLwHz3UOoYRaJA0JPuutC4',
            'someId'));

        $this->assertContains('jwt', $client->getAuthMethods());
        $this->assertNotContains('wampcra', $client->getAuthMethods());

        $client->addClientAuthenticator(new ClientWampCraAuthenticator('someId'));

        $this->assertContains('jwt', $client->getAuthMethods());
        $this->assertContains('wampcra', $client->getAuthMethods());
    }
    
}