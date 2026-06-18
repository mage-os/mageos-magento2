<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\SearchEngineConfiguration;
use MageOS\Installer\Test\TestCase\AbstractVOTest;

/**
 * Unit tests for SearchEngineConfiguration VO
 */
class SearchEngineConfigurationTest extends AbstractVOTest
{
    protected function createValidInstance(): SearchEngineConfiguration
    {
        return new SearchEngineConfiguration(
            engine: 'opensearch',
            host: 'localhost',
            port: 9200,
            prefix: 'magento'
        );
    }

    protected function getSensitiveFields(): array
    {
        return []; // No sensitive fields
    }

    public function testItConstructsWithAllParameters(): void
    {
        $config = new SearchEngineConfiguration(
            engine: 'elasticsearch8',
            host: 'search.example.com',
            port: 9300,
            prefix: 'store'
        );

        $this->assertPropertyEquals($config, 'engine', 'elasticsearch8');
        $this->assertPropertyEquals($config, 'host', 'search.example.com');
        $this->assertPropertyEquals($config, 'port', 9300);
        $this->assertPropertyEquals($config, 'prefix', 'store');
    }

    public function testItConstructsWithDefaultPrefix(): void
    {
        $config = new SearchEngineConfiguration(
            engine: 'opensearch',
            host: 'localhost',
            port: 9200
        );

        $this->assertPropertyEquals($config, 'prefix', '');
    }

    public function testGetHostWithPort(): void
    {
        $config = $this->createValidInstance();

        $this->assertEquals('localhost:9200', $config->getHostWithPort());
    }

    public function testIsOpensearchReturnsTrueForOpensearch(): void
    {
        $config = new SearchEngineConfiguration(
            engine: 'opensearch',
            host: 'localhost',
            port: 9200
        );

        $this->assertTrue($config->isOpenSearch());
        $this->assertFalse($config->isElasticsearch());
    }

    public function testIsElasticsearchReturnsTrueForElasticsearch(): void
    {
        $config = new SearchEngineConfiguration(
            engine: 'elasticsearch8',
            host: 'localhost',
            port: 9200
        );

        $this->assertFalse($config->isOpenSearch());
        $this->assertTrue($config->isElasticsearch());
    }

    public function testIsElasticsearchMatchesVersionVariants(): void
    {
        $elasticsearchVersions = ['elasticsearch', 'elasticsearch7', 'elasticsearch8'];

        foreach ($elasticsearchVersions as $version) {
            $config = new SearchEngineConfiguration(
                engine: $version,
                host: 'localhost',
                port: 9200
            );

            $this->assertTrue(
                $config->isElasticsearch(),
                "Engine '{$version}' should be detected as Elasticsearch"
            );
        }
    }

    public function testToArrayContainsAllFields(): void
    {
        $config = $this->createValidInstance();
        $array = $config->toArray();

        $this->assertArrayHasKey('engine', $array);
        $this->assertArrayHasKey('host', $array);
        $this->assertArrayHasKey('port', $array);
        $this->assertArrayHasKey('prefix', $array);
        $this->assertSame(9200, $array['port']);
    }

    public function testFromArrayWithCompleteData(): void
    {
        $data = [
            'engine' => 'elasticsearch8',
            'host' => 'es.local',
            'port' => 9300,
            'prefix' => 'shop'
        ];

        $config = SearchEngineConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'engine', 'elasticsearch8');
        $this->assertPropertyEquals($config, 'host', 'es.local');
        $this->assertPropertyEquals($config, 'port', 9300);
        $this->assertPropertyEquals($config, 'prefix', 'shop');
    }

    public function testFromArrayWithMissingFieldsUsesDefaults(): void
    {
        $data = [];

        $config = SearchEngineConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'engine', 'opensearch');
        $this->assertPropertyEquals($config, 'host', 'localhost');
        $this->assertPropertyEquals($config, 'port', 9200);
        $this->assertPropertyEquals($config, 'prefix', '');
    }

    public function testFromArrayCoercesPortToInt(): void
    {
        $data = [
            'engine' => 'opensearch',
            'host' => 'localhost',
            'port' => '9300' // string
        ];

        $config = SearchEngineConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'port', 9300);
        $this->assertIsInt($config->port);
    }
}
