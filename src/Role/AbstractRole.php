<?php

namespace Thruway\Role;

use Thruway\ClientSession;
use Thruway\Message\Message;

/**
 * Class AbstractRole
 *
 * @package Thruway\Role
 */
abstract class AbstractRole
{

    /**
     * Handle process reveiced message
     *
     * @param \Thruway\ClientSession $session
     * @param \Thruway\Message\Message $msg
     * @return mixed
     */
    abstract public function onMessage(ClientSession $session, Message $msg);

    /**
     * Handle process message
     *
     * @param \Thruway\Message\Message $msg
     * @return mixed
     */
    abstract public function handlesMessage(Message $msg);

    /**
     * @return \stdClass
     */
    public function getFeatures()
    {
        return new \stdClass();
    }
}
