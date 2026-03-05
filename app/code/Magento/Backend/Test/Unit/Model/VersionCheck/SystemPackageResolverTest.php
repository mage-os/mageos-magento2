<?php
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Model\VersionCheck;

use Magento\Backend\Model\VersionCheck\SystemPackageResolver;
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

    public function testResolvesPackageNameAndVersion(): void
    {
        $this->composerInfo->method('getSystemPackages')->willReturn([
            'mage-os/product-community-edition' => [
                'name' => 'mage-os/product-community-edition',
                'type' => 'metapackage',
                'version' => '2.1.0',
            ],
        ]);

        $this->assertSame('mage-os/product-community-edition', $this->resolver->getPackageName());
        $this->assertSame('2.1.0', $this->resolver->getInstalledVersion());
    }

    public function testReturnsNullWhenNoSystemPackage(): void
    {
        $this->composerInfo->method('getSystemPackages')->willReturn([]);

        $this->assertNull($this->resolver->getPackageName());
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
