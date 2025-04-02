<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Webapi\Authentication;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Authentication\Rest\OauthService;

/**
 * @magentoApiDataFixture consumerFixture
 */
class RestTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    /** @var \Magento\Integration\Model\Oauth\Consumer */
    protected static $_consumer;

    /** @var \Magento\Integration\Model\Oauth\Token */
    protected static $_token;

    /** @var string */
    protected static $_consumerKey;

    /** @var string */
    protected static $_consumerSecret;

    /** @var string */
    protected static $_verifier;

    /** @var \Magento\TestFramework\Authentication\Rest\OauthService */
    private $_oauthService;

    protected function setUp(): void
    {
        $this->_markTestAsRestOnly();
        $objectManager = Bootstrap::getObjectManager();
        $this->_oauthService = $objectManager->create(OauthService::class);
        parent::setUp();
    }

    /**
     * Create a consumer
     */
    public static function consumerFixture($date = null)
    {
        /** Clear the credentials because during the fixture generation, any previous credentials are invalidated */
        \Magento\TestFramework\Authentication\OauthHelper::clearApiAccessCredentials();

        $consumerCredentials = \Magento\TestFramework\Authentication\OauthHelper::getConsumerCredentials($date);
        self::$_consumerKey = $consumerCredentials['key'];
        self::$_consumerSecret = $consumerCredentials['secret'];
        self::$_verifier = $consumerCredentials['verifier'];
        self::$_consumer = $consumerCredentials['consumer'];
        self::$_token = $consumerCredentials['token'];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->_oauthService = null;
        if (isset(self::$_consumer)) {
            self::$_consumer->delete();
            self::$_token->delete();
        }
    }

    public function testGetRequestToken()
    {
        $oauthService = $this->_oauthService->create(self::$_consumerKey, self::$_consumerSecret);
        $requestToken = $oauthService->getRequestToken();

        $this->assertNotEmpty($requestToken["oauth_token"], "Request token value is not set");
        $this->assertNotEmpty($requestToken["oauth_token_secret"], "Request token secret is not set");

        $this->assertEquals(
            \Magento\Framework\Oauth\Helper\Oauth::LENGTH_TOKEN,
            strlen($requestToken["oauth_token"]),
            "Request token value length should be " . \Magento\Framework\Oauth\Helper\Oauth::LENGTH_TOKEN
        );
        $this->assertEquals(
            \Magento\Framework\Oauth\Helper\Oauth::LENGTH_TOKEN_SECRET,
            strlen($requestToken["oauth_token_secret"]),
            "Request token secret length should be " . \Magento\Framework\Oauth\Helper\Oauth::LENGTH_TOKEN_SECRET
        );
    }

    /**
     */
    public function testGetRequestTokenExpiredConsumer()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('401 Unauthorized');

        $this::consumerFixture('2012-01-01 00:00:00');
        $this::$_consumer->setUpdatedAt('2012-01-01 00:00:00');
        $this::$_consumer->save();

        $oauthService = $this->_oauthService->create(self::$_consumerKey, self::$_consumerSecret);
        $oauthService->getRequestToken();
    }

    /**
     */
    public function testGetRequestTokenInvalidConsumerKey()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('401 Unauthorized');

        $oauthService = $this->_oauthService->create('invalid_key', self::$_consumerSecret);
        $oauthService->getRequestToken();
    }

    /**
     */
    public function testGetRequestTokenInvalidConsumerSecret()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('401 Unauthorized');

        $oauthService = $this->_oauthService->create(self::$_consumerKey, 'invalid_secret');
        $oauthService->getRequestToken();
    }

    public function testGetAccessToken()
    {
        $oauthService = $this->_oauthService->create(self::$_consumerKey, self::$_consumerSecret);
        $requestToken = $oauthService->getRequestToken();
        $accessToken = $oauthService->getAccessToken($requestToken, self::$_verifier);

        $this->assertNotEmpty($accessToken["oauth_token"], "Access token value is not set.");
        $this->assertNotEmpty($accessToken["oauth_token_secret"], "Access token secret is not set.");

        $this->assertEquals(
            \Magento\Framework\Oauth\Helper\Oauth::LENGTH_TOKEN,
            strlen($accessToken["oauth_token"]),
            "Access token value length should be " . \Magento\Framework\Oauth\Helper\Oauth::LENGTH_TOKEN
        );
        $this->assertEquals(
            \Magento\Framework\Oauth\Helper\Oauth::LENGTH_TOKEN_SECRET,
            strlen($accessToken["oauth_token_secret"]),
            "Access token secret length should be " . \Magento\Framework\Oauth\Helper\Oauth::LENGTH_TOKEN_SECRET
        );
    }

    /**
     */
    public function testGetAccessTokenInvalidVerifier()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('401 Unauthorized');

        $oauthService = $this->_oauthService->create(self::$_consumerKey, self::$_consumerSecret);
        $requestToken = $oauthService->getRequestToken();
        $oauthService->getAccessToken($requestToken, 'invalid verifier');
    }

    /**
     */
    public function testGetAccessTokenConsumerMismatch()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('401 Unauthorized');

        $oauthServiceA = $this->_oauthService->create(self::$_consumerKey, self::$_consumerSecret);
        $requestTokenA = $oauthServiceA->getRequestToken();
        $oauthVerifierA = self::$_verifier;

        self::consumerFixture();
        $oauthServiceB = $this->_oauthService->create(self::$_consumerKey, self::$_consumerSecret);
        $oauthServiceB->getRequestToken();
        $oauthServiceB->getAccessToken($requestTokenA, $oauthVerifierA);
    }

    /**
     */
    public function testAccessApiInvalidAccessToken()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('400 Bad Request');

        $oauthService = $this->_oauthService->create(self::$_consumerKey, self::$_consumerSecret);
        $requestToken = $oauthService->getRequestToken();
        $accessToken = $oauthService->getAccessToken(
            $requestToken,
            self::$_verifier
        );

        $accessToken['oauth_token'] = 'invalid';
        $oauthService->validateAccessToken($accessToken);
    }
}
