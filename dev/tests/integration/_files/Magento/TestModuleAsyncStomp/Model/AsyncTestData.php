<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
namespace Magento\TestModuleAsyncStomp\Model;

class AsyncTestData
{
    /**
     * @var $msgValue
     */
    protected $msgValue;

    /**
     * @var $path
     */
    protected $path;

    /**
     * set path to tmp directory.
     *
     * @param string $path
     * @return void
     */
    public function setTextFilePath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getTextFilePath()
    {
        return $this->path;
    }

    /**
     * @param string $strValue
     * @return void
     */
    public function setValue($strValue)
    {
        $this->msgValue = $strValue;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->msgValue;
    }
}
