<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Wishlist\BackwardCompatibility;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The classes that moved from Magento_Customer keep working under their old names
 *
 * @magentoAppArea adminhtml
 */
class CustomerClassNamesTest extends TestCase
{
    /**
     * Old class name => new class name
     */
    private const MOVED_CLASSES = [
        'Magento\Customer\Block\Adminhtml\Edit\Tab\View\Wishlist' =>
            'Magento\Wishlist\Block\Adminhtml\Customer\Edit\Tab\View\Wishlist',
        'Magento\Customer\Controller\Adminhtml\Index\Wishlist' =>
            'Magento\Wishlist\Controller\Adminhtml\Index\Wishlist',
        'Magento\Customer\Controller\Adminhtml\Index\ViewWishlist' =>
            'Magento\Wishlist\Controller\Adminhtml\Index\ViewWishlist',
        'Magento\Customer\Controller\Adminhtml\Wishlist\Product\Composite\Wishlist' =>
            'Magento\Wishlist\Controller\Adminhtml\Wishlist\Product\Composite\Wishlist',
        'Magento\Customer\Controller\Adminhtml\Wishlist\Product\Composite\Wishlist\Configure' =>
            'Magento\Wishlist\Controller\Adminhtml\Wishlist\Product\Composite\Wishlist\Configure',
        'Magento\Customer\Controller\Adminhtml\Wishlist\Product\Composite\Wishlist\Update' =>
            'Magento\Wishlist\Controller\Adminhtml\Wishlist\Product\Composite\Wishlist\Update',
    ];

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * Each old name still exists and extends its new class
     *
     * @param string $oldClass
     * @param string $newClass
     * @return void
     */
    #[DataProvider('movedClassesProvider')]
    public function testOldClassExtendsNewClass(string $oldClass, string $newClass): void
    {
        $this->assertTrue(class_exists($oldClass), $oldClass . ' does not exist');
        $this->assertTrue(is_subclass_of($oldClass, $newClass), $oldClass . ' does not extend ' . $newClass);
    }

    /**
     * Creating a routed class gives its old name, so configuration for the old names keeps applying
     *
     * @param string $oldClass
     * @param string $newClass
     * @return void
     */
    #[DataProvider('instantiableClassesProvider')]
    public function testNewClassIsCreatedUnderOldName(string $oldClass, string $newClass): void
    {
        $this->assertInstanceOf($oldClass, $this->objectManager->create($newClass));
    }

    /**
     * @return array
     */
    public static function movedClassesProvider(): array
    {
        $cases = [];
        foreach (self::MOVED_CLASSES as $oldClass => $newClass) {
            $cases[$oldClass] = [$oldClass, $newClass];
        }
        return $cases;
    }

    /**
     * @return array
     */
    public static function instantiableClassesProvider(): array
    {
        return array_filter(
            self::movedClassesProvider(),
            static fn (array $case): bool => !(new \ReflectionClass($case[1]))->isAbstract()
        );
    }
}
