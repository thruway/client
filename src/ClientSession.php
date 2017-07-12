<?php

namespace Thruway;


use React\Promise\Promise;
use Thruway\Message\Message;
use Thruway\Peer\Client;
use Thruway\Transport\TransportInterface;

/**
 * Class ClientSession
 *
 * @package Thruway
 */
class ClientSession
{
    /**
     * Session state
     * @const int
     */
    const STATE_UNKNOWN = 0;
    const STATE_PRE_HELLO = 1;
    const STATE_CHALLENGE_SENT = 2;
    const STATE_UP = 3;
    const STATE_DOWN = 4;

    /**
     * @var string
     */
    protected $realm;

    /**
     * @var boolean
     */
    protected $authenticated;

    /**
     * @var int
     */
    protected $state;

    /**
     * @var \Thruway\Transport\TransportInterface
     */
    protected $transport;

    /**
     * @var int
     */
    protected $sessionId;

    /**
     * @var boolean
     */
    private $goodbyeSent = false;

    /**
     *
     * @var array
     */
    protected $pingRequests = [];

    /**
     * @var \React\EventLoop\LoopInterface
     */
    protected $loop;

    /**
     * Set client state
     *
     * @param int $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Get client state
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }


    /**
     * Set athentication state (authenticated or not)
     *
     * @param boolean $authenticated
     */
    public function setAuthenticated($authenticated)
    {
        $this->authenticated = $authenticated;
    }

    /**
     * Get authentication state (authenticated or not)
     *
     * @return boolean
     */
    public function getAuthenticated()
    {
        return $this->authenticated;
    }

    /**
     * check is authenticated
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        return $this->getAuthenticated();
    }

    /**
     * Set realm
     *
     * @param \Thruway\Realm $realm
     */
    public function setRealm($realm)
    {
        $this->realm = $realm;
    }

    /**
     * Get realm
     *
     * @return \Thruway\Realm
     */
    public function getRealm()
    {
        return $this->realm;
    }


    /**
     * Get session ID
     *
     * @return int
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Get transport
     *
     * @return \Thruway\Transport\TransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * Check sent Goodbye message
     *
     * @return boolean
     */
    public function isGoodbyeSent()
    {
        return $this->goodbyeSent;
    }

    /**
     * Set state sent goodbye message ?
     *
     * @param boolean $goodbyeSent
     */
    public function setGoodbyeSent($goodbyeSent)
    {
        $this->goodbyeSent = $goodbyeSent;
    }

    /**
     * Ping
     *
     * @param int $timeout
     * @return \React\Promise\Promise
     */
    public function ping($timeout = 5)
    {
        return $this->getTransport()->ping($timeout);
    }

    /**
     * process abort request
     *
     * @param mixed $details
     * @param mixed $responseURI
     * @throws \Exception
     */
    public function abort($details = null, $responseURI = null)
    {
        if ($this->isAuthenticated()) {
            throw new \Exception("Session::abort called after we are authenticated");
        }

        $abortMsg = new AbortMessage($details, $responseURI);

        $this->sendMessage($abortMsg);

        $this->shutdown();
    }

    /**
     * Process Shutdown session
     */
    public function shutdown()
    {
        // we want to immediately remove
        // all references

        $this->onClose();

        $this->transport->close();
    }

    /**
     * Set loop
     *
     * @param \React\EventLoop\LoopInterface $loop
     */
    public function setLoop($loop)
    {
        $this->loop = $loop;
    }

    /**
     * Get loop
     *
     * @return \React\EventLoop\LoopInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }


    /**
     * @var \Thruway\Peer\Client
     */
    private $peer;

    /**
     * Constructor
     *
     * @param \Thruway\Transport\TransportInterface $transport
     * @param Client $peer
     */
    public function __construct(TransportInterface $transport, Client $peer)
    {
        $this->transport = $transport;
        $this->peer      = $peer;
    }

    /**
     * Subscribe
     *
     * @param string $topicName
     * @param callable $callback
     * @param $options array
     * @return Promise
     */
    public function subscribe($topicName, callable $callback, $options = null)
    {
        return $this->peer->getSubscriber()->subscribe($this, $topicName, $callback, $options);
    }

    /**
     * Publish
     *
     * @param string $topicName
     * @param array|mixed $arguments
     * @param array|mixed $argumentsKw
     * @param array|mixed $options
     * @return \React\Promise\Promise
     */
    public function publish($topicName, $arguments = null, $argumentsKw = null, $options = null)
    {
        return $this->peer->getPublisher()->publish($this, $topicName, $arguments, $argumentsKw, $options);
    }

    /**
     * Register
     *
     * @param string $procedureName
     * @param callable $callback
     * @param array|mixed $options
     * @return \React\Promise\Promise
     */
    public function register($procedureName, callable $callback, $options = null)
    {
        return $this->peer->getCallee()->register($this, $procedureName, $callback, $options);
    }

    /**
     * Unregister
     *
     * @param string $procedureName
     * @return \React\Promise\Promise|FALSE
     */
    public function unregister($procedureName)
    {
        return $this->peer->getCallee()->unregister($this, $procedureName);
    }

    /**
     * Call
     *
     * @param string $procedureName
     * @param array|mixed $arguments
     * @param array|mixed $argumentsKw
     * @param array|mixed $options
     * @return \React\Promise\Promise
     */
    public function call($procedureName, $arguments = null, $argumentsKw = null, $options = null)
    {
        return $this->peer->getCaller()->call($this, $procedureName, $arguments, $argumentsKw, $options);
    }

    /**
     * Send message
     *
     * @param \Thruway\Message\Message $msg
     * @return mixed
     */
    public function sendMessage(Message $msg)
    {
        $this->transport->sendMessage($msg);
    }

    /**
     * @param int $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * Close client session
     * TODO: Need to send goodbye message
     */
    public function close()
    {
        $this->transport->close();
    }

    /**
     * Handle on close client session
     */
    public function onClose()
    {
        $this->state = static::STATE_DOWN;
    }
}
