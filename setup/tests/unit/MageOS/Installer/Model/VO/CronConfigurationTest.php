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

    public function test_it_constructs_with_configure_true(): void
    {
        $config = new CronConfiguration(configure: true);

        $this->assertPropertyEquals($config, 'configure', true);
    }

    public function test_it_constructs_with_configure_false(): void
    {
        $config = new CronConfiguration(configure: false);

        $this->assertPropertyEquals($config, 'configure', false);
    }

    public function test_to_array_contains_configure_field(): void
    {
        $config = $this->createValidInstance();
        $array = $config->toArray();

        $this->assertArrayHasKey('configure', $array);
        $this->assertTrue($array['configure']);
    }

    public function test_from_array_with_configure_true(): void
    {
        $data = ['configure' => true];

        $config = CronConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'configure', true);
    }

    public function test_from_array_with_configure_false(): void
    {
        $data = ['configure' => false];

        $config = CronConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'configure', false);
    }

    public function test_from_array_with_missing_field_defaults_to_false(): void
    {
        $data = [];

        $config = CronConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'configure', false);
    }
}
