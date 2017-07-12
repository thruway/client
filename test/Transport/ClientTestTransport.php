<?php

namespace Thruway\Transport;

use Thruway\Message\Message;
use Thruway\Serializer\SerializerInterface;

class ClientTestTransport implements TransportInterface
{
    private $trusted = false;
    /** @var Message[] */
    private $recordedMessages = [];

    public function getTransportDetails()
    {
    }

    public function sendMessage(Message $msg)
    {
        $this->recordedMessages[] = $msg;
    }

    public function getRecordedMessages()
    {
        return $this->recordedMessages;
    }

    public function clearRecordedMessages()
    {
        $this->recordedMessages = [];
    }

    public function close()
    {
    }

    public function ping()
    {
    }

    public function setSerializer(SerializerInterface $serializer)
    {
    }

    public function getSerializer()
    {
    }

    public function isTrusted()
    {
        return $this->trusted;
    }

    public function setTrusted($trusted)
    {
        $this->trusted = $trusted;
    }
}