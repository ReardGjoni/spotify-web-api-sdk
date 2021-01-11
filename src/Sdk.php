<?php


namespace Gjoni\SpotifyWebApiSdk;

use Gjoni\SpotifyWebApiSdk\Interfaces\SdkInterface;

class Sdk implements SdkInterface
{
    /**
     * @var string $clientId
     */
    private string $clientId;

    /**
     * @var string $clientSecret
     */
    private string $clientSecret;

    /**
     * @var string $accessToken
     */
    private string $accessToken;

    /**
     * @var string $refreshToken Long lived refresh token; used to fetch a new access token
     */
    private string $refreshToken;

    /**
     * Sdk constructor.
     * @param string $clientId The client id of the third party app that will use the API
     * @param string $clientSecret It's corresponding secret
     */
    public function __construct(string $clientId, string $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * @inheritDoc
     */
    public function setClientId(string $id): Sdk
    {
        $this->clientId = $id;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @inheritDoc
     */
    public function setClientSecret(string $secret): Sdk
    {
        $this->clientSecret = $secret;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @inheritDoc
     */
    public function setAccessToken(string $accessToken): SdkInterface
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @inheritDoc
     */
    public function setRefreshToken(string $refreshToken): SdkInterface
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }
}
