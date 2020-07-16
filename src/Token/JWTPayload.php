<?php

namespace EpgClient\Token;

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
     * @throws InvalidJWTPayload
     */
    public function __construct($jwt)
    {
        if (!$jwt) {
            return;
        }

        list(,$bodyB64,) = explode('.', $jwt);
        $payload = (array)JWT::jsonDecode(JWT::urlsafeB64Decode($bodyB64));
        if (!array_key_exists('account', $payload)) {
            throw new InvalidJWTPayload("Missed required parameter in payload");
        }

        $this->username = $payload['username'];
        $this->account = $payload['account'];
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
