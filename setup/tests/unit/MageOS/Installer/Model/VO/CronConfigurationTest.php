<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\CronConfiguration;
use MageOS\Installer\Test\TestCase\AbstractVOTest;

/**
 * Unit tests for CronConfiguration VO
 */
class CronConfigurationTest extends AbstractVOTest
{
    protected function createValidInstance(): CronConfiguration
    {
        return new CronConfiguration(configure: true);
    }

    protected function getSensitiveFields(): array
    {
        return []; // No sensitive fields
    }

    public function testItConstructsWithConfigureTrue(): void
    {
        $config = new CronConfiguration(configure: true);

        $this->assertPropertyEquals($config, 'configure', true);
    }

    public function testItConstructsWithConfigureFalse(): void
    {
        $config = new CronConfiguration(configure: false);

        $this->assertPropertyEquals($config, 'configure', false);
    }

    public function testToArrayContainsConfigureField(): void
    {
        $config = $this->createValidInstance();
        $array = $config->toArray();

        $this->assertArrayHasKey('configure', $array);
        $this->assertTrue($array['configure']);
    }

    public function testFromArrayWithConfigureTrue(): void
    {
        $data = ['configure' => true];

        $config = CronConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'configure', true);
    }

    public function testFromArrayWithConfigureFalse(): void
    {
        $data = ['configure' => false];

        $config = CronConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'configure', false);
    }

    public function testFromArrayWithMissingFieldDefaultsToFalse(): void
    {
        $data = [];

        $config = CronConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'configure', false);
    }
}
