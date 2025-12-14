<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\ThemeConfiguration;
use MageOS\Installer\Test\TestCase\AbstractVOTest;

/**
 * Unit tests for ThemeConfiguration VO
 */
final class ThemeConfigurationTest extends AbstractVOTest
{
    protected function createValidInstance(): ThemeConfiguration
    {
        return new ThemeConfiguration(
            install: true,
            theme: 'hyva-default'
        );
    }

    protected function getSensitiveFields(): array
    {
        return []; // No sensitive fields
    }

    public function test_it_constructs_with_all_parameters(): void
    {
        $config = new ThemeConfiguration(
            install: true,
            theme: 'hyva-custom'
        );

        $this->assertPropertyEquals($config, 'install', true);
        $this->assertPropertyEquals($config, 'theme', 'hyva-custom');
    }

    public function test_it_constructs_with_default_theme(): void
    {
        $config = new ThemeConfiguration(install: false);

        $this->assertPropertyEquals($config, 'install', false);
        $this->assertPropertyEquals($config, 'theme', '');
    }

    public function test_to_array_contains_all_fields(): void
    {
        $config = $this->createValidInstance();
        $array = $config->toArray();

        $this->assertArrayHasKey('install', $array);
        $this->assertArrayHasKey('theme', $array);
        $this->assertTrue($array['install']);
        $this->assertEquals('hyva-default', $array['theme']);
    }

    public function test_from_array_with_complete_data(): void
    {
        $data = [
            'install' => true,
            'theme' => 'luma'
        ];

        $config = ThemeConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'install', true);
        $this->assertPropertyEquals($config, 'theme', 'luma');
    }

    public function test_from_array_with_missing_fields_uses_defaults(): void
    {
        $data = [];

        $config = ThemeConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'install', false);
        $this->assertPropertyEquals($config, 'theme', '');
    }

    public function test_supports_various_themes(): void
    {
        $themes = ['hyva-default', 'hyva-custom', 'luma', 'blank'];

        foreach ($themes as $theme) {
            $config = new ThemeConfiguration(
                install: true,
                theme: $theme
            );

            $this->assertPropertyEquals($config, 'theme', $theme);
        }
    }
}
