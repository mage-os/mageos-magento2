<?php
declare(strict_types=1);

namespace Magento\Catalog\Test\Integration\Model\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Verify ProductRepository::delete() works when called from a service-contract
 * context (REST/GraphQL/CLI/programmatic) where Backend\App\Action::dispatch()
 * has not set the isSecureArea flag.
 */
class ServiceContractDeleteTest extends TestCase
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testDeleteByIdWithoutPreSetSecureArea(): void
    {
        $registry = Bootstrap::getObjectManager()->get(Registry::class);
        $this->assertNull(
            $registry->registry('isSecureArea'),
            'Test setup invariant violated: isSecureArea must not be pre-set.'
        );

        $repository = Bootstrap::getObjectManager()
            ->create(ProductRepositoryInterface::class);

        $this->assertTrue($repository->deleteById('simple'));
        $this->assertNull(
            $registry->registry('isSecureArea'),
            'isSecureArea must be cleaned up after delete returns.'
        );
    }
}
