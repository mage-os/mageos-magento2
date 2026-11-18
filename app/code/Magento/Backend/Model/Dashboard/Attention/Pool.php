<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\Model\Dashboard\Attention;

/**
 * Registry of dashboard attention tiles, populated via di.xml
 */
class Pool
{
    /**
     * @var ItemInterface[]
     */
    private array $items;

    /**
     * @param ItemInterface[] $items
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $name => $item) {
            if (!$item instanceof ItemInterface) {
                throw new \InvalidArgumentException(
                    sprintf('Dashboard attention item "%s" must implement %s', $name, ItemInterface::class)
                );
            }
        }
        $this->items = $items;
    }

    /**
     * Items ordered by sort order
     *
     * @return ItemInterface[]
     */
    public function getItems(): array
    {
        $items = array_values($this->items);
        usort($items, fn (ItemInterface $a, ItemInterface $b) => $a->getSortOrder() <=> $b->getSortOrder());
        return $items;
    }
}
