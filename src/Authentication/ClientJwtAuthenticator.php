<?php

namespace Thruway\Authentication;

use Thruway\Message\AuthenticateMessage;
use Thruway\Message\ChallengeMessage;

class ClientJwtAuthenticator implements ClientAuthenticationInterface
{

    private $authId;
    /**
     * @var string
     */
    private $jwt;

    public function __construct($jwt, $authid)
    {
        $this->jwt = $jwt;
        $this->authId = $authid;
    }

    /**
     * Get AuthID
     *
     * @return mixed
     */
    public function getAuthId()
    {
        return $this->authId;
    }

    /**
     * Set AuthID
     *
     * @param mixed $authid
     */
    public function setAuthId($authid)
    {
        $this->authId = $authid;
    }

    /**
     * Get list supported authentication method
     *
     * @return array
     */
    public function getAuthMethods()
    {
        return ['jwt'];
    }

    /**
     * Get authentication message from challenge message
     *
     * @param ChallengeMessage $msg
     * @return AuthenticateMessage
     */
    public function getAuthenticateFromChallenge(ChallengeMessage $msg)
    {
        return new AuthenticateMessage($this->jwt);
    }
}