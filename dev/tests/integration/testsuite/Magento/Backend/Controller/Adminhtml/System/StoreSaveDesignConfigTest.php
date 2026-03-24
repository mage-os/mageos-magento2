<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Controller\Adminhtml\System;

use Magento\Backend\Controller\Adminhtml\System\Store\Save as StoreSaveController;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\ResourceModel\Store as StoreResource;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\DbIsolation;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory as ThemeCollectionFactory;

/**
 * Integration coverage for store view code changes with design configuration (scoped theme).
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StoreSaveDesignConfigTest extends AbstractBackendController
{
    private const ORIGINAL_STORE_CODE = 'design_cfg_test_sv';

    private const RENAMED_STORE_CODE = 'design_cfg_test_sv_rn';

    /**
     * After renaming a store view code, scoped design/theme configuration must still resolve for the new code.
     *
     * @return void
     * @magentoDataFixture Magento/Backend/_files/store_design_config_test.php
     */
    #[DbIsolation(false)]
    #[AppArea('adminhtml')]
    public function testDesignThemeConfigRemainsAfterStoreViewCodeChange(): void
    {
        try {
            $formKey = $this->_objectManager->get(FormKey::class);
            $scopeConfig = $this->_objectManager->get(ScopeConfigInterface::class);
            $storeManager = $this->_objectManager->get(StoreManagerInterface::class);
            $themeCollectionFactory = $this->_objectManager->get(ThemeCollectionFactory::class);
            $storeResource = $this->_objectManager->get(StoreResource::class);
            $typeList = $this->_objectManager->get(TypeListInterface::class);
            $configWriter = $this->_objectManager->get(WriterInterface::class);
            $reinitableConfig = $this->_objectManager->get(ReinitableConfigInterface::class);

            $themeCollection = $themeCollectionFactory->create();
            $lumaThemeId = (int) $themeCollection->getThemeByFullPath('frontend/Magento/luma')->getId();

            /** @var Store $store */
            $store = $this->_objectManager->create(Store::class);
            $storeResource->load($store, self::ORIGINAL_STORE_CODE, 'code');
            $this->assertNotEmpty($store->getId(), 'Fixture store view should exist');

            // Persist to core_config_data (scope_id). MutableScopeConfig::setValue only mutates in-memory test
            // config keyed by store code, so after a code rename getValue would miss it and read 0 from DB.
            $configWriter->save(
                DesignInterface::XML_PATH_THEME_ID,
                (string) $lumaThemeId,
                ScopeInterface::SCOPE_STORES,
                (int) $store->getId()
            );
            $typeList->cleanType('config');
            $reinitableConfig->reinit();
            $storeManager->reinitStores();

            $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
            $this->getRequest()->setPostValue([
                'form_key' => $formKey->getFormKey(),
                'store_type' => 'store',
                'store_action' => 'edit',
                'store' => [
                    'store_id' => (string) $store->getId(),
                    'name' => $store->getName(),
                    'code' => self::RENAMED_STORE_CODE,
                    'is_active' => $store->isActive() ? '1' : '0',
                    'sort_order' => (string) $store->getSortOrder(),
                    'is_default' => $store->isDefault() ? '1' : '0',
                    'group_id' => (string) $store->getGroupId(),
                ],
            ]);
            // Run the controller without Http::launch() so frontend layout (e.g. B2B Reorder Sidebar plugin
            // using admin user id as customer id) is not rendered.
            $request = $this->getRequest();
            $request->setDispatched(true);
            $saveAction = $this->_objectManager->create(StoreSaveController::class);
            $saveAction->dispatch($request);

            $this->assertSessionMessages(
                $this->containsEqual((string) __('You saved the store view.')),
                MessageInterface::TYPE_SUCCESS,
                ManagerInterface::class
            );

            $storeManager->reinitStores();
            $reinitableConfig->reinit();

            $themeIdAfterRename = (int) $scopeConfig->getValue(
                DesignInterface::XML_PATH_THEME_ID,
                ScopeInterface::SCOPE_STORE,
                self::RENAMED_STORE_CODE
            );
            $this->assertSame(
                $lumaThemeId,
                $themeIdAfterRename,
                'Design theme for the store view must stay correct after the store code is changed'
            );
        } finally {
            $this->resetRequest();
        }
    }
}
