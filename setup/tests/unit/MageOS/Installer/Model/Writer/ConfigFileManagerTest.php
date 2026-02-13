<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Writer;

use MageOS\Installer\Model\Writer\ConfigFileManager;
use MageOS\Installer\Test\TestCase\FileSystemTestCase;
use MageOS\Installer\Test\Util\TestDataBuilder;

/**
 * Unit tests for ConfigFileManager
 *
 * Tests file persistence for installation resume capability
 */
class ConfigFileManagerTest extends FileSystemTestCase
{
    /** @var ConfigFileManager */
    private ConfigFileManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new ConfigFileManager();
    }

    public function testSaveContextCreatesFile(): void
    {
        $baseDir = $this->getVirtualFilePath('');
        $context = TestDataBuilder::validInstallationContext();

        $result = $this->manager->saveContext($baseDir, $context);

        $this->assertTrue($result);
        $this->assertVirtualFileExists('var/.mageos-install-config.json');
    }

    public function testSaveContextCreatesValidJson(): void
    {
        $baseDir = $this->getVirtualFilePath('');
        $context = TestDataBuilder::validInstallationContext();

        $this->manager->saveContext($baseDir, $context);

        $content = $this->getVirtualFileContent('var/.mageos-install-config.json');
        $data = json_decode($content, true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('_metadata', $data);
        $this->assertArrayHasKey('config', $data);
    }

    public function testSaveContextIncludesMetadata(): void
    {
        $baseDir = $this->getVirtualFilePath('');
        $context = TestDataBuilder::validInstallationContext();

        $this->manager->saveContext($baseDir, $context);

        $content = $this->getVirtualFileContent('var/.mageos-install-config.json');
        $data = json_decode($content, true);

        $this->assertArrayHasKey('created_at', $data['_metadata']);
        $this->assertArrayHasKey('version', $data['_metadata']);
        $this->assertArrayHasKey('note', $data['_metadata']);
        $this->assertArrayHasKey('sensitive_fields_excluded', $data['_metadata']);
        $this->assertEquals('1.0.0', $data['_metadata']['version']);
    }

    public function testSaveContextExcludesSensitiveData(): void
    {
        $baseDir = $this->getVirtualFilePath('');
        $context = TestDataBuilder::validInstallationContext();

        $this->manager->saveContext($baseDir, $context);

        $content = $this->getVirtualFileContent('var/.mageos-install-config.json');
        $data = json_decode($content, true);

        // Database password should not be in saved file
        $this->assertArrayNotHasKey('password', $data['config']['database']);

        // Admin password should not be in saved file
        $this->assertArrayNotHasKey('password', $data['config']['admin']);
    }

    public function testSaveContextSetsRestrictivePermissions(): void
    {
        $baseDir = $this->getVirtualFilePath('');
        $context = TestDataBuilder::validInstallationContext();

        $this->manager->saveContext($baseDir, $context);

        $filePath = $this->getVirtualFilePath('var/.mageos-install-config.json');
        $perms = fileperms($filePath) & 0777;

        $this->assertEquals(0600, $perms, 'File should have 0600 permissions (owner read/write only)');
    }

    public function testLoadContextReturnsNullWhenFileNotExists(): void
    {
        $baseDir = $this->getVirtualFilePath('');

        $result = $this->manager->loadContext($baseDir);

        $this->assertNull($result);
    }

    public function testLoadContextReconstructsInstallationContext(): void
    {
        $baseDir = $this->getVirtualFilePath('');
        $original = TestDataBuilder::validInstallationContext();

        $this->manager->saveContext($baseDir, $original);
        $loaded = $this->manager->loadContext($baseDir);

        $this->assertNotNull($loaded);
        $this->assertInstanceOf(\MageOS\Installer\Model\InstallationContext::class, $loaded);
    }

    public function testRoundTripPreservesNonSensitiveData(): void
    {
        $baseDir = $this->getVirtualFilePath('');
        $original = TestDataBuilder::validInstallationContext();

        $this->manager->saveContext($baseDir, $original);
        $loaded = $this->manager->loadContext($baseDir);

        // Check non-sensitive data preserved
        $this->assertEquals(
            $original->getDatabase()->host,
            $loaded->getDatabase()->host
        );
        $this->assertEquals(
            $original->getAdmin()->email,
            $loaded->getAdmin()->email
        );
        $this->assertEquals(
            $original->getStore()->baseUrl,
            $loaded->getStore()->baseUrl
        );
    }

    public function testRoundTripLosesSensitiveData(): void
    {
        $baseDir = $this->getVirtualFilePath('');
        $original = TestDataBuilder::validInstallationContext();

        $this->manager->saveContext($baseDir, $original);
        $loaded = $this->manager->loadContext($baseDir);

        // Passwords should be empty
        $this->assertEmpty($loaded->getDatabase()->password);
        $this->assertEmpty($loaded->getAdmin()->password);
    }

    public function testLoadContextHandlesCorruptedJson(): void
    {
        $baseDir = $this->getVirtualFilePath('');
        $this->createVirtualFile('var/.mageos-install-config.json', '{invalid json}');

        $result = $this->manager->loadContext($baseDir);

        $this->assertNull($result);
    }

    public function testLoadContextHandlesMissingConfigKey(): void
    {
        $baseDir = $this->getVirtualFilePath('');
        $this->createVirtualFile('var/.mageos-install-config.json', json_encode(['wrong_key' => []]));

        $result = $this->manager->loadContext($baseDir);

        $this->assertNull($result);
    }

    public function testExistsReturnsTrueWhenFileExists(): void
    {
        $baseDir = $this->getVirtualFilePath('');
        $this->createVirtualFile('var/.mageos-install-config.json', '{}');

        $result = $this->manager->exists($baseDir);

        $this->assertTrue($result);
    }

    public function testExistsReturnsFalseWhenFileNotExists(): void
    {
        $baseDir = $this->getVirtualFilePath('');

        $result = $this->manager->exists($baseDir);

        $this->assertFalse($result);
    }

    public function testDeleteRemovesFile(): void
    {
        $baseDir = $this->getVirtualFilePath('');
        $this->createVirtualFile('var/.mageos-install-config.json', '{}');

        $this->manager->delete($baseDir);

        $this->assertVirtualFileDoesNotExist('var/.mageos-install-config.json');
    }

    public function testDeleteReturnsTrueWhenFileNotExists(): void
    {
        $baseDir = $this->getVirtualFilePath('');

        $result = $this->manager->delete($baseDir);

        $this->assertTrue($result, 'Deleting non-existent file should return true');
    }

    public function testDeleteReturnsTrueOnSuccessfulDeletion(): void
    {
        $baseDir = $this->getVirtualFilePath('');
        $this->createVirtualFile('var/.mageos-install-config.json', '{}');

        $result = $this->manager->delete($baseDir);

        $this->assertTrue($result);
    }

    public function testGetConfigFilePathReturnsCorrectPath(): void
    {
        $baseDir = '/var/www/magento';

        $path = $this->manager->getConfigFilePath($baseDir);

        $this->assertEquals('/var/www/magento/var/.mageos-install-config.json', $path);
    }

    public function testSaveOverwritesExistingFile(): void
    {
        $baseDir = $this->getVirtualFilePath('');
        $context1 = TestDataBuilder::minimalInstallationContext();
        $context2 = TestDataBuilder::validInstallationContext();

        // Save first context
        $this->manager->saveContext($baseDir, $context1);

        // Save second context (should overwrite)
        $this->manager->saveContext($baseDir, $context2);

        $loaded = $this->manager->loadContext($baseDir);

        // Should have data from second context
        $this->assertNotNull($loaded->getEmail());
    }
}
