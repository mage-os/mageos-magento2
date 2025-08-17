<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup\Native;

use Magento\Setup\Model\ObjectManagerProvider;
use Magento\Setup\Model\Bootstrap;

/**
 * Factory for ObjectManagerProvider in native setup
 */
class ObjectManagerProviderFactory
{
    /**
     * Create ObjectManagerProvider instance
     *
     * @param ServiceLocatorInterface $serviceManager
     * @param string $requestedName
     * @return ObjectManagerProvider
     */
    public function __invoke(ServiceLocatorInterface $serviceManager, $requestedName)
    {
        // Create Bootstrap instance
        $bootstrap = new Bootstrap();
        
        // Create ObjectManagerProvider with ServiceManager and Bootstrap
        return new ObjectManagerProvider($serviceManager, $bootstrap);
    }
}
