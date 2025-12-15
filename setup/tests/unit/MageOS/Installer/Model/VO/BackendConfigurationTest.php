<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\BackendConfiguration;
use MageOS\Installer\Test\TestCase\AbstractVOTest;

/**
 * Unit tests for BackendConfiguration VO
 */
class BackendConfigurationTest extends AbstractVOTest
{
    protected function createValidInstance(): BackendConfiguration
    {
        return new BackendConfiguration(
            frontname: 'admin'
        );
    }

    protected function getSensitiveFields(): array
    {
        return []; // No sensitive fields
    }

    public function test_it_constructs_with_frontname(): void
    {
        $config = new BackendConfiguration(frontname: 'backend');

        $this->assertPropertyEquals($config, 'frontname', 'backend');
    }

    public function test_to_array_contains_frontname(): void
    {
        $config = $this->createValidInstance();
        $array = $config->toArray();

        $this->assertArrayHasKey('frontname', $array);
        $this->assertEquals('admin', $array['frontname']);
    }

    public function test_from_array_with_frontname(): void
    {
        $data = ['frontname' => 'manage'];

        $config = BackendConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'frontname', 'manage');
    }

    public function test_from_array_with_missing_frontname_uses_default(): void
    {
        $data = [];

        $config = BackendConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'frontname', 'admin');
    }

    public function test_supports_custom_backend_paths(): void
    {
        $customPaths = ['admin', 'backend', 'manage', 'control', 'secure-admin-panel'];

        foreach ($customPaths as $path) {
            $config = new BackendConfiguration(frontname: $path);
            $this->assertPropertyEquals($config, 'frontname', $path);
        }
    }
}
