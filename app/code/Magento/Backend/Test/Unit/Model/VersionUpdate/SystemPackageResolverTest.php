<?php
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Model\VersionUpdate;

use Magento\Backend\Model\VersionUpdate\SystemPackageResolver;
use Magento\Framework\Composer\ComposerInformation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SystemPackageResolverTest extends TestCase
{
    private ComposerInformation|MockObject $composerInfo;
    private SystemPackageResolver $resolver;

    protected function setUp(): void
    {
        $this->composerInfo = $this->createMock(ComposerInformation::class);
        $this->resolver = new SystemPackageResolver($this->composerInfo);
    }

    public function testReturnsCommunityEditionPackageName(): void
    {
        $this->composerInfo->method('getSystemPackages')->willReturn([
            'mage-os/product-community-edition' => [
                'name' => 'mage-os/product-community-edition',
                'type' => 'metapackage',
                'version' => '2.1.0',
            ],
        ]);

        $this->assertSame('mage-os/product-community-edition', $this->resolver->getPackageName());
    }

    public function testReturnsEnterpriseEditionPackageName(): void
    {
        $this->composerInfo->method('getSystemPackages')->willReturn([
            'mage-os/product-enterprise-edition' => [
                'name' => 'mage-os/product-enterprise-edition',
                'type' => 'metapackage',
                'version' => '2.1.0',
            ],
        ]);

        $this->assertSame('mage-os/product-enterprise-edition', $this->resolver->getPackageName());
    }

    public function testReturnsNullWhenNoSystemPackage(): void
    {
        $this->composerInfo->method('getSystemPackages')->willReturn([]);

        $this->assertNull($this->resolver->getPackageName());
    }

    public function testReturnsInstalledVersion(): void
    {
        $this->composerInfo->method('getSystemPackages')->willReturn([
            'mage-os/product-community-edition' => [
                'name' => 'mage-os/product-community-edition',
                'type' => 'metapackage',
                'version' => '2.1.0',
            ],
        ]);

        $this->assertSame('2.1.0', $this->resolver->getInstalledVersion());
    }

    public function testReturnsNullVersionWhenNoSystemPackage(): void
    {
        $this->composerInfo->method('getSystemPackages')->willReturn([]);

        $this->assertNull($this->resolver->getInstalledVersion());
    }

    public function testCachesResultAcrossCalls(): void
    {
        $this->composerInfo->expects($this->once())->method('getSystemPackages')->willReturn([
            'mage-os/product-community-edition' => [
                'name' => 'mage-os/product-community-edition',
                'type' => 'metapackage',
                'version' => '2.1.0',
            ],
        ]);

        $this->resolver->getPackageName();
        $this->resolver->getPackageName();
    }
}
