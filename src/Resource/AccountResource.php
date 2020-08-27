<?php

namespace EpgClient\Resource;

class AccountResource extends AbstractResource
{
    /** @var string */
    protected static $baseLocation = '/api/accounts';
    /** @var string */
    protected $accountLocation;

    /**
     * @param null|string $accountLocation required only if your user have not any relation to account
     * @return $this
     */
    public function getChannels($accountLocation = null)
    {
        $this->reset();
        $accountLocation = $accountLocation ?: $this->getAccountLocation();
        $this->location = $accountLocation . '/channels';
        $this->method = 'GET';

        return $this;
    }

    /**
     * @param null|string $accountLocation required only if your user have not any relation to account
     * @return $this
     */
    public function getCategories($accountLocation = null)
    {
        $this->reset();
        $accountLocation = $accountLocation ?: $this->getAccountLocation();
        $this->location = $accountLocation . '/categories';
        $this->method = 'GET';

        return $this;
    }

    /**
     * @param null|string $accountLocation required only if your user have not any relation to account
     * @return $this
     */
    public function getGenres($accountLocation = null)
    {
        $this->reset();
        $accountLocation = $accountLocation ?: $this->getAccountLocation();
        $this->location = $accountLocation . '/genres';
        $this->method = 'GET';

        return $this;
    }

    private function getAccountLocation()
    {
        if (!$this->accountLocation) {
            $payload = $this->client->getTokenPayload();
            $this->accountLocation = $payload->getAccount();
        }
        if (!$this->accountLocation) {
            throw new \RuntimeException("Missed account location. You need to specify one or sign in with another user.");
        }

        return $this->accountLocation;
    }
}
