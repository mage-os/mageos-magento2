<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Model\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Verify the protected-models safeguard for product deletion is scoped to the
 * frontend area only.
 *
 * Service-contract callers (REST/SOAP/GraphQL/CLI/cron) never set the
 * isSecureArea registry flag because they do not go through
 * Backend\App\Action::dispatch(). Declaring the protected models in the
 * frontend di scope lets those callers delete products while preserving the
 * safeguard against accidental deletions originating from a storefront request.
 */
class ServiceContractDeleteTest extends TestCase
{
    /**
     * Product deletion succeeds in a service-contract context without isSecureArea.
     *
     * @magentoAppArea webapi_rest
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testDeleteByIdInServiceContractContext(): void
    {
        $registry = Bootstrap::getObjectManager()->get(Registry::class);
        $this->assertNull(
            $registry->registry('isSecureArea'),
            'Test setup invariant violated: isSecureArea must not be pre-set.'
        );

        $repository = Bootstrap::getObjectManager()->create(ProductRepositoryInterface::class);

        $this->assertTrue($repository->deleteById('simple'));
    }

    /**
     * Product deletion remains guarded in the frontend area.
     *
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testDeleteByIdIsBlockedOnFrontend(): void
    {
        $repository = Bootstrap::getObjectManager()->create(ProductRepositoryInterface::class);

        $this->expectException(StateException::class);
        $repository->deleteById('simple');
    }
}
