<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Test\Util\TestDataBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Unit tests for MagentoInstallationStage
 *
 * Note: Full installation testing requires integration tests.
 * These unit tests verify the stage structure and logic.
 */
class MagentoInstallationStageTest extends TestCase
{
    public function testGetNameReturnsMagentoInstallation(): void
    {
        $app = $this->createMock(Application::class);
        $stage = new \MageOS\Installer\Model\Stage\MagentoInstallationStage($app);

        $this->assertEquals('Magento Installation', $stage->getName());
    }

    public function testCanGoBackReturnsFalse(): void
    {
        $app = $this->createMock(Application::class);
        $stage = new \MageOS\Installer\Model\Stage\MagentoInstallationStage($app);

        $this->assertFalse($stage->canGoBack(), 'Cannot go back once installation starts');
    }

    public function testGetProgressWeightReturnsHighValue(): void
    {
        $app = $this->createMock(Application::class);
        $stage = new \MageOS\Installer\Model\Stage\MagentoInstallationStage($app);

        $this->assertEquals(10, $stage->getProgressWeight(), 'Installation is heavy operation');
    }

    public function testShouldSkipReturnsFalse(): void
    {
        $app = $this->createMock(Application::class);
        $stage = new \MageOS\Installer\Model\Stage\MagentoInstallationStage($app);
        $context = TestDataBuilder::validInstallationContext();

        $this->assertFalse($stage->shouldSkip($context), 'Installation should never be skipped');
    }

    public function testWriteSecureBackupPreservesOwnerOnlyPermissions(): void
    {
        $app = $this->createMock(Application::class);
        $stage = new \MageOS\Installer\Model\Stage\MagentoInstallationStage($app);

        $dir = sys_get_temp_dir() . '/mageos-backup-' . uniqid();
        mkdir($dir);
        $source = $dir . '/env.php';
        $destination = $dir . '/env.php.backup.php';
        $secret = "<?php return ['crypt' => ['key' => 'SECRET_CRYPT_KEY'], 'db' => ['password' => 'dbpass']];\n";

        try {
            $previousUmask = umask(null);

            file_put_contents($source, $secret);
            chmod($source, 0600);

            // Widen the umask so a naive copy() would produce a 0644 file —
            // proving the helper actively tightens permissions rather than
            // inheriting them from the environment.
            umask(0022);

            $method = new \ReflectionMethod($stage, 'writeSecureBackup');
            $method->setAccessible(true);
            $result = $method->invoke($stage, $source, $destination);

            $this->assertTrue($result, 'Backup should succeed');
            $this->assertFileExists($destination);
            $this->assertSame(
                $secret,
                file_get_contents($destination),
                'Backup must contain the exact source secrets'
            );
            $this->assertSame(
                '0600',
                substr(sprintf('%o', fileperms($destination)), -4),
                'Backup of a secrets file must be readable only by its owner'
            );
        } finally {
            umask($previousUmask);

            if (is_file($source)) {
                unlink($source);
            }
            if (is_file($destination)) {
                unlink($destination);
            }
            if (is_dir($dir)) {
                rmdir($dir);
            }
        }
    }

    public function testWriteSecureBackupReturnsFalseWhenSourceMissing(): void
    {
        $app = $this->createMock(Application::class);
        $stage = new \MageOS\Installer\Model\Stage\MagentoInstallationStage($app);

        $method = new \ReflectionMethod($stage, 'writeSecureBackup');
        $method->setAccessible(true);

        // copy() of a missing source emits a warning; swallow it so the strict
        // test suite can assert on the boolean return rather than failing early.
        set_error_handler(static fn () => true);
        try {
            $result = $method->invoke(
                $stage,
                sys_get_temp_dir() . '/does-not-exist-' . uniqid() . '.php',
                sys_get_temp_dir() . '/mageos-backup-' . uniqid() . '.php'
            );
        } finally {
            restore_error_handler();
        }

        $this->assertFalse($result, 'Backup should report failure when the source cannot be copied');
    }
}
