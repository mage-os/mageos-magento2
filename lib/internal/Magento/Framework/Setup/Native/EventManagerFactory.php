<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup\Native;

/**
 * Native EventManagerFactory that replicates Laminas\Mvc\Service\EventManagerFactory
 */
class EventManagerFactory
{
    /**
     * Create an EventManager instance (same behavior as Laminas)
     *
     * @param mixed $container Laminas ServiceManager
     * @param string $name
     * @param array|null $options
     * @return EventManager
     */
    public function __invoke($container, $name, ?array $options = null)
    {
        $shared = $container->has('SharedEventManager') ? $container->get('SharedEventManager') : null;
        return new EventManager($shared);
    }
}
