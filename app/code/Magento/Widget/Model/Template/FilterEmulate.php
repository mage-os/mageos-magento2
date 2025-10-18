<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Widget\Model\Template;

/**
 * Class FilterEmulate
 *
 * @package Magento\Widget\Model\Template
 */
class FilterEmulate extends Filter
{
    /**
     * Generate widget with emulation frontend area
     *
     * @param string[] $construction
     *
     * @return mixed|string
     * @throws \Exception
     */
    public function widgetDirective($construction)
    {
        return $this->_appState->emulateAreaCode('frontend', [$this, 'generateWidget'], [$construction]);
    }

    /**
     * Filter the string as template with frontend area emulation
     *
     * @param string $value
     *
     * @return string
     * @throws \Exception
     */
    public function filterDirective($value) : string
    {
        return $this->_appState->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_FRONTEND,
            [$this, 'filter'],
            [$value]
        );
    }
}
