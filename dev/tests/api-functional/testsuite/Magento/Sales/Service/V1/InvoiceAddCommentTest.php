<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Service\V1;

use Magento\Sales\Api\Data\InvoiceCommentInterface;
use Magento\TestFramework\TestCase\WebapiAbstract;

class InvoiceAddCommentTest extends WebapiAbstract
{
    /**
     * Service read name
     */
    public const SERVICE_READ_NAME = 'salesInvoiceCommentRepositoryV1';

    /**
     * Service version
     */
    public const SERVICE_VERSION = 'V1';

    /**
     * Test invoice add comment service
     *
     * @magentoApiDataFixture Magento/Sales/_files/invoice.php
     */
    public function testInvoiceAddComment()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoiceCollection = $objectManager->get(\Magento\Sales\Model\ResourceModel\Order\Invoice\Collection::class);
        $invoice = $invoiceCollection->getFirstItem();

        $commentData = [
            InvoiceCommentInterface::COMMENT => 'Hello world!',
            InvoiceCommentInterface::ENTITY_ID => null,
            InvoiceCommentInterface::CREATED_AT => null,
            InvoiceCommentInterface::PARENT_ID => $invoice->getId(),
            InvoiceCommentInterface::IS_VISIBLE_ON_FRONT => 1,
            InvoiceCommentInterface::IS_CUSTOMER_NOTIFIED => 1
        ];

        $requestData = ['entity' => $commentData];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/invoices/comments',
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_READ_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_READ_NAME . 'save',
            ],
        ];

        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotEmpty($result);
    }
}
