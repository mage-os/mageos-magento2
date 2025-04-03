<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */

namespace Magento\TestFramework\Authentication\Rest;

use Magento\Framework\Oauth\NonceGeneratorInterface;
use Magento\Framework\Url;
use Magento\Framework\Oauth\Helper\Utility;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Inspection\Exception;
use Magento\Framework\HTTP\ClientFactory;

class OauthService
{
    /**
     * @var \Magento\Framework\Url
     */
    protected Url $urlProvider;

    /**
     * @var \Magento\Framework\HTTP\ClientFactory
     */
    protected ClientFactory $clientFactory;

    /**
     * @var \Magento\Framework\Oauth\NonceGeneratorInterface
     */
    protected NonceGeneratorInterface $_nonceGenerator;

    /**
     * @var \Magento\Framework\Oauth\Helper\Utility
     */
    private Utility $_httpUtility;

    /**
     * @var string
     */
    protected string $consumerKey;

    /**
     * @var string
     */
    protected string $consumerSecret;

    /**
     * @param Url $urlProvider
     * @param ClientFactory $clientFactory
     * @param NonceGeneratorInterface $nonceGenerator
     * @param Utility $utility
     */
    public function __construct(
        Url                     $urlProvider,
        ClientFactory           $clientFactory,
        NonceGeneratorInterface $nonceGenerator,
        Utility                 $utility
    ) {
        $this->urlProvider = $urlProvider;
        $this->clientFactory = $clientFactory;
        $this->_nonceGenerator = $nonceGenerator;
        $this->_httpUtility = $utility;
    }

    /**
     * Return current OauthService object after setting required key values
     *
     * @param string $consumerKey
     * @param string $consumerSecret
     * @return OauthService
     */
    public function create(string $consumerKey, string $consumerSecret)
    {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        return $this;
    }

    /**
     * Builds the authorization header array.
     *
     * @param array $params
     * @return array
     */
    public function getBasicAuthorizationParams(array $params): array
    {
        $headerParams = [
            'oauth_nonce' => $this->_nonceGenerator->generateNonce(),
            'oauth_timestamp' => (string)$this->_nonceGenerator->generateTimestamp(),
            'oauth_version' => '1.0',
            "oauth_signature_method" => \Magento\Framework\Oauth\Oauth::SIGNATURE_SHA256,
            "oauth_callback" => TESTS_BASE_URL
        ];
        return array_merge($headerParams, $params);
    }

    /**
     * Get request token.
     *
     * @return array
     * @throws \Exception
     */
    public function getRequestToken(): array
    {
        $authParameters = ['oauth_consumer_key' => $this->consumerKey];
        $authParameters = $this->getBasicAuthorizationParams($authParameters);
        $requestUrl = $this->getRequestTokenEndpoint();
        $headers = [
            'Authorization' => $this->buildAuthorizationHeaderToRequestToken(
                $authParameters,
                $this->consumerSecret,
                $requestUrl
            )
        ];

        $responseBody = $this->fetchResponse($requestUrl, [], $headers);
        return $this->parseResponseBody($responseBody);
    }

    /**
     * Build header for request token
     *
     * @param array $params
     * @param string $consumerSecret
     * @param string $requestUrl
     * @param string $signatureMethod
     * @param string $httpMethod
     * @return string
     */
    public function buildAuthorizationHeaderToRequestToken(
        array  $params,
        string $consumerSecret,
        string $requestUrl,
        string $signatureMethod = \Magento\Framework\Oauth\Oauth::SIGNATURE_SHA256,
        string $httpMethod = 'POST'
    ): string {
        $params['oauth_signature'] = $this->_httpUtility->sign(
            $params,
            $signatureMethod,
            $consumerSecret,
            null,
            $httpMethod,
            $requestUrl
        );

        return $this->_httpUtility->toAuthorizationHeader($params);
    }

    /**
     * Get access token
     *
     * @param array $token
     * @param string $verifier
     * @return array
     * @throws \Exception
     */
    public function getAccessToken(array $token, string $verifier): array
    {
        $authParameters = ['oauth_consumer_key' => $this->consumerKey];
        $authParameters = $this->getBasicAuthorizationParams($authParameters);

        $bodyParams = [
            'oauth_verifier' => $verifier,
        ];

        $authorizationHeader = [
            'Authorization' => $this->buildAuthorizationHeaderForAPIRequest(
                $authParameters,
                $this->consumerSecret,
                $this->getAccessTokenEndpoint(),
                $token,
                $bodyParams
            ),
        ];
        $responseBody = $this->fetchResponse($this->getAccessTokenEndpoint(), $bodyParams, $authorizationHeader);
        return $this->parseResponseBody($responseBody);
    }

    /**
     * Validate access token
     *
     * @param array $token
     * @param string $method
     * @return array
     */
    public function validateAccessToken(array $token, string $method = 'GET'): array
    {
        $authParameters = ['oauth_consumer_key' => $this->consumerKey];
        $authParameters = $this->getBasicAuthorizationParams($authParameters);

        //Need to add Accept header else Magento errors out with 503
        $extraAuthenticationHeaders = ['Accept' => 'application/json'];

        $authorizationHeader = [
            'Authorization' => $this->buildAuthorizationHeaderForAPIRequest(
                $authParameters,
                $this->consumerSecret,
                $this->getTestApiEndpoint(),
                $token,
                [],
                \Magento\Framework\Oauth\Oauth::SIGNATURE_SHA256,
                $method
            ),
        ];

        $headers = array_merge($authorizationHeader, $extraAuthenticationHeaders);

        $responseBody = $this->fetchResponse($this->getTestApiEndpoint(), [], $headers, $method);

        return json_decode($responseBody);
    }

    /**
     * Build header for api request
     *
     * @param array $params
     * @param string $consumerSecret
     * @param string $requestUrl
     * @param array $token
     * @param array|null $bodyParams
     * @param string $signatureMethod
     * @param string $httpMethod
     * @return string
     */
    public function buildAuthorizationHeaderForAPIRequest(
        array  $params,
        string $consumerSecret,
        string $requestUrl,
        array  $token,
        ?array $bodyParams = null,
        string $signatureMethod = \Magento\Framework\Oauth\Oauth::SIGNATURE_SHA256,
        string $httpMethod = 'POST'
    ): string {

        if (isset($params['oauth_callback'])) {
            unset($params['oauth_callback']);
        }

        $params = array_merge($params, ['oauth_token' => $token['oauth_token']]);
        $params = array_merge($params, $bodyParams);

        $params['oauth_signature'] = $this->_httpUtility->sign(
            $params,
            $signatureMethod,
            $consumerSecret,
            $token['oauth_token_secret'],
            $httpMethod,
            $requestUrl
        );

        return $this->_httpUtility->toAuthorizationHeader($params);
    }

    /**
     * Request token endpoint.
     *
     * @return string
     * @throws \Exception
     */
    public function getRequestTokenEndpoint(): string
    {
        return $this->urlProvider->getRebuiltUrl(TESTS_BASE_URL . '/oauth/token/request');
    }

    /**
     * Access token endpoint
     *
     * @return string
     */
    public function getAccessTokenEndpoint(): string
    {
        return $this->urlProvider->getRebuiltUrl(TESTS_BASE_URL . '/oauth/token/access');
    }

    /**
     * Returns the TestModule1 Rest API endpoint.
     *
     * @return string
     */
    public function getTestApiEndpoint(): string
    {
        /** @phpstan-ignore-next-line */
        $defaultStoreCode = Bootstrap::getObjectManager()->get(\Magento\Store\Model\StoreManagerInterface::class)
            ->getStore()->getCode();
        return $this->urlProvider->getRebuiltUrl(TESTS_BASE_URL . '/rest/' . $defaultStoreCode . '/V1/testmodule1');
    }

    /**
     * Fetch api response using curl client factory
     *
     * @param string $url
     * @param array $requestBody
     * @param array $headers
     * @param string $method
     * @return string
     */
    public function fetchResponse(string $url, array $requestBody, array $headers, string $method = 'POST'): string
    {
        $httpClient = $this->clientFactory->create();
        $httpClient->setHeaders($headers);
        $httpClient->setOption(CURLOPT_FAILONERROR, true);
        if ($method === 'GET') {
            $httpClient->get($url);
        } else {
            $httpClient->post($url, $requestBody);
        }

        return $httpClient->getBody();
    }

    /**
     * Parse response body and return data in array.
     *
     * @param string $responseBody
     * @return array
     * @throws \Exception
     */
    protected function parseResponseBody(string $responseBody): array
    {
        parse_str($responseBody, $data);
        if (!is_array($data)) {
            throw new Exception('Unable to parse response.');
        } elseif (isset($data['error'])) {
            throw new Exception("Error occurred: '{$data['error']}'");
        }
        return $data;
    }
}
