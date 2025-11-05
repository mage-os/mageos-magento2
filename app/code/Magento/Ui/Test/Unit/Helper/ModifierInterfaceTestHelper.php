<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Ui\Test\Unit\Helper;

use Magento\Ui\DataProvider\Modifier\Dummy;

class ModifierInterfaceTestHelper extends Dummy
{
    private $data = [];
    private $meta = [];

    public function getData()
    {
        return $this->data;
    }

    public function setData($value)
    {
        $this->data = $value;
        return $this;
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function setMeta($value)
    {
        $this->meta = $value;
        return $this;
    }

    public function modifyData(array $data)
    {
        return $this->data;
    }

    public function modifyMeta(array $meta)
    {
        return $this->meta;
    }
}

