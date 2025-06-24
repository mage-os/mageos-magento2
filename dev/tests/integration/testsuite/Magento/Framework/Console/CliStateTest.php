<?php
/**
 * Copyright 2025 Adobe.
 * All rights reserved.
 */

namespace Magento\Framework\Console;

use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for CLI --magento-init-params functionality
 * Tests that Magento\Framework\App\State::getMode() returns the correct mode
 */
class CliStateTest extends TestCase
{
    /**
     * @var State
     */
    private $state;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test that State::getMode() when --magento-init-params sets MAGE_MODE
     *
     * @param string $mode
     * @return void
     * @dataProvider modeDataProvider
     */
    public function testStateGetModeWithMagentoInitParams(string $mode)
    {
        // Set up test argv with --magento-init-params
        $testArgv = [
            'php',
            'bin/magento',
            'setup:upgrade',
            '--magento-init-params=MAGE_MODE=' . $mode,
        ];

        // Store original argv
        $originalArgv = $_SERVER['argv'] ?? null;
        $_SERVER['argv'] = $testArgv;

        try {
            // Create a new Cli instance which will use our fixed initObjectManager method
            $cli = new Cli('Magento CLI');
            
            // Get the ObjectManager from the Cli instance using reflection
            $reflection = new \ReflectionClass($cli);
            $objectManagerProperty = $reflection->getProperty('objectManager');
            $objectManagerProperty->setAccessible(true);
            $objectManager = $objectManagerProperty->getValue($cli);

            // Get the State object from the ObjectManager
            $state = $objectManager->get(State::class);

            // Assert that State::getMode() returns the correct mode
            $this->assertEquals($mode, $state->getMode(),
                'State::getMode() should return "' . $mode . '" when MAGE_MODE=' . $mode . ' is set via --magento-init-params');

        } finally {
            // Restore original argv
            if ($originalArgv !== null) {
                $_SERVER['argv'] = $originalArgv;
            } else {
                unset($_SERVER['argv']);
            }
        }
    }

    /**
     * Returns magento mode for cli command
     *
     * @return string[]
     */
    public static function modeDataProvider(): array
    {
        return [
            ['production'],
            ['developer'],
            ['default']
        ];
    }
}
