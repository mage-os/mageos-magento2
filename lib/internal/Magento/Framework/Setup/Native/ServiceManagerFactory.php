<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup\Native;

/**
 * Factory for creating ServiceManager instance for Magento's ObjectManager
 */
class ServiceManagerFactory
{
    /**
     * Create ServiceManager instance
     *
     * @return ServiceManager
     */
    public function create()
    {
        return ServiceManagerProvider::getServiceManager();
    }
}
