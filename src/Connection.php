<?php

namespace Thruway;

use React\EventLoop\LoopInterface;
use React\Socket\ConnectorInterface;
use Thruway\Message\AuthenticateMessage;
use Thruway\Message\ChallengeMessage;
use Thruway\Peer\Client;
use Thruway\Transport\PawlTransportProvider;
use Thruway\Transport\TransportInterface;
use Evenement\EventEmitterInterface;
use Evenement\EventEmitterTrait;

/**
 * Class Connection
 *
 * @package Thruway
 */
class Connection implements EventEmitterInterface
{
    /**
     * Using \Evenement\EventEmitterTrait to implements \Evenement\EventEmitterInterface
     * @see \Evenement\EventEmitterTrait
     */
    use EventEmitterTrait;

    /**
     * @var \Thruway\Peer\Client
     */
    private $client;

    /**
     * @var \Thruway\Transport\TransportInterface
     */
    private $transport;

    /**
     * @var array
     */
    private $options;

    /**
     * Constructor
     *
     * @param array $options
     * @param \React\EventLoop\LoopInterface $loop
     * @throws \Exception
     */
    public function __construct(Array $options, LoopInterface $loop = null, ConnectorInterface $connector = null)
    {
        $this->options = $options;
        $this->client  = new Client($options['realm'], $loop);
        $url           = isset($options['url']) ? $options['url'] : null;
        $pawlTransport = new PawlTransportProvider($url, $connector);

        $this->client->addTransportProvider($pawlTransport);
        $this->client->setReconnectOptions($options);

        //Set Authid
        if (isset($options['authid'])) {
            $this->client->setAuthId($options['authid']);
        }
        //Set Authextra
        if (isset($options['authextra'])) {
            $this->client->setAuthextra($options['authextra']);
        }

        //Register Handlers
        $this->handleOnChallenge();
        $this->handleOnOpen();
        $this->handleOnClose();
        $this->handleOnError();
    }

    /**
     * @deprecated
     *  Process events at a set interval
     *
     * @param int $timer
     */
    public function doEvents($timer = 1)
    {
        /*$loop = $this->getClient()->getLoop();

        $looping = true;
        $loop->addTimer($timer, function () use (&$looping) {
            $looping = false;
        });

        while ($looping) {
            usleep(1000);
            $loop->tick();
        }*/
    }

    /**
     *  Starts the open sequence
     * @param bool $startLoop
     */
    public function open($startLoop = true)
    {
        $this->client->start($startLoop);
    }

    /**
     * Starts the close sequence
     */
    public function close()
    {
        $this->client->setAttemptRetry(false);
        $this->transport->close();
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Handle On Open event
     */

    private function handleOnOpen()
    {
        $this->client->on('open', function (ClientSession $session, TransportInterface $transport, $details) {
            $this->transport = $transport;
            $this->emit('open', [$session, $transport, $details]);
        });
    }

    /**
     * Handle On Close event
     */
    private function handleOnClose()
    {
        $this->client->on('close', function ($reason) {
            $this->emit('close', [$reason]);
        });

        if (isset($this->options['onClose']) && is_callable($this->options['onClose'])) {
            $this->on('close', $this->options['onClose']);
        }
    }

    /**
     * Handle On Error event
     */
    private function handleOnError()
    {
        $this->client->on('error', function () {
            $this->emit('error', func_get_args());
        });
    }

    /**
     * Setup the onChallenge callback
     */
    private function handleOnChallenge()
    {

        $options = $this->options;

        if (isset($options['onChallenge']) && is_callable($options['onChallenge'])
            && isset($options['authmethods'])
            && is_array($options['authmethods'])
        ) {
            $this->client->setAuthMethods($options['authmethods']);

            $this->client->on('challenge', function (ClientSession $session, ChallengeMessage $msg) use ($options) {
                $extra = null;
                $token = call_user_func($options['onChallenge'], $session, $msg->getAuthMethod(), $msg);
                if (is_array($token) && count($token) >= 2) {
                    [$token, $extra] = $token;
                }
                $session->sendMessage(new AuthenticateMessage($token, $extra));
            });
        }
    }
}
