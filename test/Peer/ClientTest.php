<?php

namespace Thruway\Peer;

use React\EventLoop\Factory;
use Thruway\ClientSession;
use Thruway\Message\AbortMessage;
use Thruway\Message\HelloMessage;
use Thruway\Message\WelcomeMessage;
use Thruway\Test\TestLoop;
use Thruway\Transport\ClientTestTransport;
use Thruway\Transport\ClientTestTransportProvider;
use Thruway\Transport\TransportInterface;
use Thruway\Transport\ClientTransportProviderInterface;

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
        $loop = new TestLoop();

        $client = new Client('some.realm', $loop);

        $transportProvider = new ClientTestTransportProvider();

        $client->addTransportProvider($transportProvider);

        $client->start();

        $transport = $this->getMockBuilder(TransportInterface::class)
            ->getMock();

        $transport->expects($this->once())
            ->method('sendMessage')
            ->withConsecutive([
                $this->callback(function (HelloMessage $message) {
                    $this->isInstanceOf(HelloMessage::class);
                    $this->assertEquals('some.realm', $message->getRealm());
                    $this->assertObjectHasAttribute('roles', $message->getDetails());
                    $this->assertObjectHasAttribute('caller', $message->getDetails()->roles);
                    $this->assertObjectHasAttribute('callee', $message->getDetails()->roles);
                    $this->assertObjectHasAttribute('subscriber', $message->getDetails()->roles);
                    $this->assertObjectHasAttribute('publisher', $message->getDetails()->roles);
                    return true;
                })

            ]);

        $transportProvider->sendOnOpenToClient($transport);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage You can only have one transport provider for a client
     */
    public function testClientOnlyAllowsOneTransportProvider()
    {
        $client = new Client('some.realm');

        $client->addTransportProvider($this->createMock(ClientTransportProviderInterface::class));
        $client->addTransportProvider($this->createMock(ClientTransportProviderInterface::class));
    }

    public function testWelcomeMessageEmitsOpen()
    {
        $loop = new TestLoop();

        $client = new Client('some.realm', $loop);

        $transportProvider = new ClientTestTransportProvider();
        $client->addTransportProvider($transportProvider);

        $client->start();

        $gotSession = false;
        $client->on('open', function (ClientSession $session) use (&$gotSession) {
            $this->assertEquals('some.realm', $session->getRealm());
            $gotSession = true;
        });

        $transport = new ClientTestTransport();

        $transportProvider->sendOnOpenToClient($transport);

        $this->assertCount(1, $transport->getRecordedMessages());
        $this->assertInstanceOf(HelloMessage::class, $transport->getRecordedMessages()[0]);

        $client->onMessage($transport, new WelcomeMessage(1234, (object)[]));

        $this->assertTrue($gotSession);
    }

    public function testAbortAfterHello()
    {
        $loop = new TestLoop();

        $client = new Client('some.realm', $loop);

        $transportProvider = new ClientTestTransportProvider();
        $client->addTransportProvider($transportProvider);

        $client->start();

        $transport = new ClientTestTransport();

        $transportProvider->sendOnOpenToClient($transport);

        $this->assertCount(1, $transport->getRecordedMessages());
        $this->assertInstanceOf(HelloMessage::class, $transport->getRecordedMessages()[0]);

        $error = null;
        $client->on('error', function ($e) use (&$error) {
            $error = $e;
        });

        $client->onMessage($transport, new AbortMessage((object)[], 'error.uri'));

        $this->assertEquals('error.uri', $error);
    }
}