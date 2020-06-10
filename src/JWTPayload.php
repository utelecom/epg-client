<?php

namespace EpgClient;

use Firebase\JWT\JWT;

class JWTPayload
{
    /** @var string */
    private $username;
    /** @var null|string */
    private $account;

    /**
     * TokenPayload constructor.
     * @param string $jwt
     */
    public function __construct($jwt)
    {
        if (!$jwt) {
            return;
        }
        list(,$bodyB64,) = explode('.', $jwt);
        $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyB64));
        $this->username = $payload->username;
        $this->account = $payload->account;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getAccount()
    {
        return $this->account;
    }
}
