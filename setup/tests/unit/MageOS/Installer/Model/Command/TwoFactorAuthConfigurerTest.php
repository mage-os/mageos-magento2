<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Command;

use MageOS\Installer\Model\Command\ProcessResult;
use MageOS\Installer\Model\Command\ProcessRunner;
use MageOS\Installer\Model\Command\TwoFactorAuthConfigurer;
use MageOS\Installer\Model\VO\EnvironmentConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Unit tests for TwoFactorAuthConfigurer
 */
class TwoFactorAuthConfigurerTest extends TestCase
{
    /** @var ProcessRunner */
    private ProcessRunner $processRunnerMock;
    /** @var TwoFactorAuthConfigurer */
    private TwoFactorAuthConfigurer $configurer;
    /** @var BufferedOutput */
    private BufferedOutput $output;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processRunnerMock = $this->createMock(ProcessRunner::class);
        $this->configurer = new TwoFactorAuthConfigurer($this->processRunnerMock);
        $this->output = new BufferedOutput();
    }

    public function testConfigureKeeps2faEnabledForProduction(): void
    {
        $prodEnv = new EnvironmentConfiguration(type: 'production', mageMode: 'production');

        // Should not call any commands for production
        $this->processRunnerMock->expects($this->never())
            ->method('runMagentoCommand');

        $result = $this->configurer->configure($prodEnv, '/var/www/magento', $this->output);

        $this->assertTrue($result);
        $outputContent = $this->output->fetch();
        $this->assertStringContainsString('2FA enabled', $outputContent);
    }

    public function testConfigureReturnsTrueForProduction(): void
    {
        $prodEnv = new EnvironmentConfiguration(type: 'production', mageMode: 'production');

        $result = $this->configurer->configure($prodEnv, '/var/www/magento', $this->output);

        $this->assertTrue($result);
    }
}
