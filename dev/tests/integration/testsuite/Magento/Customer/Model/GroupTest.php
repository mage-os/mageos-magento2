<?php
/**
 * Copyright 2011 Adobe
 * All Rights Reserved.
 */

namespace Magento\Customer\Model;

class GroupTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Customer\Model\Group
     */
    protected $groupModel;

    /**
     * @var \Magento\Customer\Api\Data\GroupInterfaceFactory
     */
    protected $groupFactory;

    protected function setUp(): void
    {
        $this->groupModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Customer\Model\Group::class
        );
        $this->groupFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Customer\Api\Data\GroupInterfaceFactory::class
        );
    }

    public function testCRUD()
    {
        $this->groupModel->setCode('test');
        $crud = new \Magento\TestFramework\Entity($this->groupModel, ['customer_group_code' => uniqid()]);
        $crud->testCrud();
    }

    /**
     * Test that customer group correctly handles multibyte characters when saving
     *
     * This verifies that the fix for multibyte character truncation works correctly.
     * Previously, substr() was used which counted bytes instead of characters,
     * causing multibyte characters to be truncated incorrectly.
     *
     * @magentoDbIsolation enabled
     * @return void
     */
    public function testMultibyteCharacterHandling(): void
    {
        // Test with multibyte characters (ö = 2 bytes in UTF-8)
        $multibyteString = str_repeat('ö', 31); // 31 characters, 62 bytes

        $group = $this->groupFactory->create();
        $group->setCode($multibyteString);
        $group->setTaxClassId(3);
        $group->save();

        // Reload from database
        $reloadedGroup = $this->groupFactory->create();
        $reloadedGroup->load($group->getId());

        // Verify all 31 multibyte characters are preserved
        $this->assertEquals(
            $multibyteString,
            $reloadedGroup->getCode(),
            'Group code with multibyte characters should be saved correctly'
        );

        $this->assertEquals(
            31,
            mb_strlen($reloadedGroup->getCode()),
            'Group code should have exactly 31 characters'
        );

        // Cleanup
        $reloadedGroup->delete();
    }
}
