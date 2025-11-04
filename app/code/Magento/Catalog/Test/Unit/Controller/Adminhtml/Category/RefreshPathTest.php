<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Magento\Catalog\Controller\Adminhtml\Category\RefreshPath;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Test\Unit\Helper\RefreshPathTestHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for class Magento\Catalog\Controller\Adminhtml\Category\RefreshPath.
 */
class RefreshPathTest extends TestCase
{
    /**
     * @var JsonFactory|MockObject
     */
    private $resultJsonFactoryMock;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->resultJsonFactoryMock = $this->createMock(JsonFactory::class);

        $this->contextMock = $this->createPartialMock(Context::class, ['getRequest']);
    }

    /**
     * Sets object non-public property.
     *
     * @param mixed $object
     * @param string $propertyName
     * @param mixed $value
     *
     * @return void
     */
    private function setObjectProperty($object, string $propertyName, $value) : void
    {
        $reflectionClass = new \ReflectionClass($object);
        $reflectionProperty = $reflectionClass->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }

    /**
     * @return void
     */
    public function testExecute() : void
    {
        $value = ['id' => 3, 'path' => '1/2/3', 'parentId' => 2, 'level' => 2];
        $result = '{"id":3,"path":"1/2/3","parentId":"2","level":"2"}';

        $requestMock = $this->createMock(RequestInterface::class);

        $objectManager = new ObjectManager($this);
        $objects = [
            [
                StoreManagerInterface::class,
                $this->createMock(StoreManagerInterface::class)
            ]
        ];
        $objectManager->prepareObjectManager($objects);

        $refreshPath = new RefreshPathTestHelper($this->contextMock, $this->resultJsonFactoryMock);

        $refreshPath->setRequestMock($requestMock);
        $requestMock->expects($this->any())->method('getParam')->with('id')->willReturn($value['id']);

        $categoryMock = $this->createPartialMock(Category::class, ['getPath', 'getParentId', 'getResource']);

        $categoryMock->method('getPath')->willReturn($value['path']);
        $categoryMock->method('getParentId')->willReturn($value['parentId']);

        $categoryResource = $this->createMock(\Magento\Catalog\Model\ResourceModel\Category::class);

        $objectManagerMock = $this->createMock(ObjectManagerInterface::class);
        $objectManagerMock->method('create')->willReturn($categoryMock);

        $this->setObjectProperty($refreshPath, '_objectManager', $objectManagerMock);
        $this->setObjectProperty($categoryMock, '_resource', $categoryResource);

        // Create Json result mock
        $jsonResultMock = $this->createMock(Json::class);
        $jsonResultMock->method('setData')->willReturn($result);

        // Configure factory to return the Json result
        $this->resultJsonFactoryMock->method('create')->willReturn($jsonResultMock);

        $this->assertEquals($result, $refreshPath->execute());
    }

    /**
     * @return void
     */
    public function testExecuteWithoutCategoryId() : void
    {
        $requestMock = $this->createMock(RequestInterface::class);

        $refreshPath = new RefreshPathTestHelper($this->contextMock, $this->resultJsonFactoryMock);

        $refreshPath->setRequestMock($requestMock);
        $requestMock->expects($this->any())->method('getParam')->with('id')->willReturn(null);

        $objectManagerMock = $this->createMock(ObjectManagerInterface::class);

        $this->setObjectProperty($refreshPath, '_objectManager', $objectManagerMock);

        $refreshPath->execute();
    }
}
