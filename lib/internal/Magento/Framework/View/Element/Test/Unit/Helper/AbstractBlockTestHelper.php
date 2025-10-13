<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\View\Element\Test\Unit\Helper;

use Magento\Framework\View\Element\AbstractBlock;

/**
 * Test helper for Magento\Framework\View\Element\AbstractBlock
 *
 * WHY THIS HELPER IS REQUIRED:
 * - Parent AbstractBlock has complex constructor requiring Context with 17+ dependencies
 * - Parent setNameInLayout() has layout dependency (lines 328-343 in AbstractBlock.php)
 * - This helper provides constructor bypass and simple name-in-layout handling
 *
 * WHY METHODS WERE REMOVED:
 * - AbstractBlock extends DataObject, which has __call magic methods
 * - Methods like setCanEditPrice(), setProductEntity(), etc. work automatically via DataObject::__call
 * - They store values in $_data array and can be retrieved with getData() or magic getters
 * - Only setNameInLayout/getNameInLayout need explicit override to avoid layout dependency
 *
 * Used By:
 * - magento2ee/app/code/Magento/PricePermissions/Test/Unit/Observer/ViewBlockAbstractToHtmlBeforeObserverTest.php
 */
class AbstractBlockTestHelper extends AbstractBlock
{
    /**
     * @var string
     */
    private $nameInLayout = '';
    
    public function __construct()
    {
        // Skip parent constructor to avoid Context dependency (17+ services)
    }
    
    /**
     * Set name in layout
     *
     * Override parent method to avoid layout dependency.
     * Parent setNameInLayout() accesses $this->_layout (lines 328-343)
     * which requires a full LayoutInterface instance.
     *
     * @param string $name
     * @return $this
     */
    public function setNameInLayout($name)
    {
        $this->nameInLayout = $name;
        return $this;
    }
    
    /**
     * Get name in layout
     *
     * @return string
     */
    public function getNameInLayout()
    {
        return $this->nameInLayout;
    }
}
