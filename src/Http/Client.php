<?php


namespace SpotifyAPI\Http;

use Config\SecretsCollection;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Message;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client
 * Serves as a boilerplate with client and request parameters for our specific needs
 */
class Client extends GuzzleClient
{
    /**
     * @var string $baseUri
     */
    private string $baseUri;

    /**
     * @var string $timeout
     */
    private $timeout;

    /**
     * @var array $configs
     */
    public array $configs;

    /**
     * @var array $allowRedirects
     */
    private array $allowRedirects;

    /**
     * @var array $headers
     */
    public array $headers;

    /**
     * @var Response $response
     */
    private Response $response;

    /**
     * Client constructor.
     *
     * @param string $baseUri
     * @param integer $timeout
     * @param array $allowRedirects
     */
    public function __construct (string $baseUri, int $timeout, array $allowRedirects = [])
    {
        $this->baseUri = $baseUri;
        $this->timeout = $timeout;
        $this->allowRedirects = $allowRedirects;

        $this->setConfigs();
        $this->setHeaders(getallheaders());
        $this->setResponse(new Response());

        parent::__construct([
            "base_uri" => $this->baseUri,
            "timeout" => $this->timeout,
            "allow_redirects" => $this->allowRedirects
        ]);
    }

    /**
     * Initializes a config blueprint to be used in most of the requests
     *
     * @return $this
     */
    private function setConfigs() {
        $this->configs = [
            "client_id" => SecretsCollection::$id,
            "response_type" => "code",
            "redirect_uri" => "http://frontend.spotify-auth.com:1024",
            "scope" => "user-read-private user-read-email playlist-read-private", // add other scopes
            "grant_type" => "authorization_code",
            "headers" => [
                "accept" => "application/json",
                "content_type" => "application/json",
                "authorization_access" => sprintf("Basic %s", base64_encode(SecretsCollection::$id . ":" . SecretsCollection::$secret)),
            ],
            "query" => [
                "limit" => 25,
            ],
        ];

        return $this;
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * @param array $headers
     * @return $this
     */
    private function setHeaders(array $headers) {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param Response $response
     * @return $this
     */
    private function setResponse(Response $response) {
        $this->response = $response;

        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param $method
     * @param $uri
     * @param array $options
     * @return ResponseInterface|string
     * @throws GuzzleException
     */
    public function fetch($method,
                          $uri,
                          array $options = []) {
        $response = $this->getResponse();

        try {
            # Set default headers for a typical user request. Includes the access token
            if (empty($options["headers"])) {
                $options["headers"] = [
                    "Accept" => $this->configs["headers"]["accept"],
                    "Content-Type" => $this->configs["headers"]["content_type"],
                    "Authorization" => sprintf("Bearer %s",  $this->headers["Access-Token"])
                ];
            }

            $request = parent::request($method, $uri, $options);

            if ($body = $request->getBody()) {
                $body = json_decode($body);

                return $response->json([
                    "body" => $body
                ]);
            }
        } catch (RequestException $exception) {
            if ($exception->hasResponse()) {
                return $this->response->json([
                    "error" => $exception->getMessage(),
                    "request" => Message::toString($exception->getRequest())
                ], $exception->getCode());
            }
        }
    }
}
