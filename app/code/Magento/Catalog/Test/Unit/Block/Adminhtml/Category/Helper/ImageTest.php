<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Adminhtml\Category\Helper;

use Magento\Catalog\Block\Adminhtml\Category\Helper\Image;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for Image helper
 */
class ImageTest extends TestCase
{
    /**
     * @var Image|MockObject
     */
    private $model;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var Store|MockObject
     */
    private $storeMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Create a partial mock to avoid parent constructor issues
        $this->model = $this->getMockBuilder(Image::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        // Use reflection to inject the storeManager dependency
        $reflection = new \ReflectionClass($this->model);
        $property = $reflection->getProperty('_storeManager');
        $property->setAccessible(true);
        $property->setValue($this->model, $this->storeManagerMock);
    }

    /**
     * Test _getUrl method returns false when no value is set
     *
     * @return void
     */
    public function testGetUrlWithoutValue(): void
    {
        $this->model->setValue(null);

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->model);
        $method = $reflection->getMethod('_getUrl');
        $method->setAccessible(true);

        $result = $method->invoke($this->model);

        $this->assertFalse($result);
    }

    /**
     * Test _getUrl method returns correct URL when value is set
     *
     * @return void
     */
    public function testGetUrlWithValue(): void
    {
        $imageName = 'test_image.jpg';
        $baseUrl = 'http://example.com/media/';
        $expectedUrl = $baseUrl . 'catalog/category/' . $imageName;

        $this->model->setValue($imageName);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $this->storeMock->expects($this->once())
            ->method('getBaseUrl')
            ->with(UrlInterface::URL_TYPE_MEDIA)
            ->willReturn($baseUrl);

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->model);
        $method = $reflection->getMethod('_getUrl');
        $method->setAccessible(true);

        $result = $method->invoke($this->model);

        $this->assertEquals($expectedUrl, $result);
    }

    /**
     * Test _getUrl method with different image names
     *
     * @dataProvider imageNameDataProvider
     * @param string $imageName
     * @return void
     */
    public function testGetUrlWithDifferentImageNames(string $imageName): void
    {
        $baseUrl = 'http://example.com/media/';
        $expectedUrl = $baseUrl . 'catalog/category/' . $imageName;

        $this->model->setValue($imageName);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $this->storeMock->expects($this->once())
            ->method('getBaseUrl')
            ->with(UrlInterface::URL_TYPE_MEDIA)
            ->willReturn($baseUrl);

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->model);
        $method = $reflection->getMethod('_getUrl');
        $method->setAccessible(true);

        $result = $method->invoke($this->model);

        $this->assertEquals($expectedUrl, $result);
    }

    /**
     * Test _getUrl method with empty string value
     *
     * @return void
     */
    public function testGetUrlWithEmptyString(): void
    {
        $this->model->setValue('');

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->model);
        $method = $reflection->getMethod('_getUrl');
        $method->setAccessible(true);

        $result = $method->invoke($this->model);

        $this->assertFalse($result);
    }

    /**
     * Test that storeManager is properly used
     *
     * @return void
     */
    public function testStoreManagerUsage(): void
    {
        $this->model->setValue('test.jpg');
        
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($this->storeMock);
        
        $this->storeMock->expects($this->once())
            ->method('getBaseUrl')
            ->willReturn('http://example.com/media/');

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->model);
        $method = $reflection->getMethod('_getUrl');
        $method->setAccessible(true);

        $result = $method->invoke($this->model);
        $this->assertStringContainsString('catalog/category/', $result);
    }

    /**
     * Data provider for image names
     *
     * @return array
     */
    public static function imageNameDataProvider(): array
    {
        return [
            'jpg image' => ['category_image.jpg'],
            'png image' => ['category_image.png'],
            'gif image' => ['category_image.gif'],
            'image with path' => ['subfolder/category_image.jpg'],
            'image with special chars' => ['category-image_01.jpg']
        ];
    }
}
