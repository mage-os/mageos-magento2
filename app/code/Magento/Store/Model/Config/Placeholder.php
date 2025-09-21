<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */

namespace Magento\Store\Model\Config;

/**
 * Placeholder configuration values processor. Replace placeholders in configuration with config values
 */
class Placeholder
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var string[]
     */
    protected $urlPaths;

    /**
     * @var string
     */
    protected $urlPlaceholder;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param string[] $urlPaths
     * @param string $urlPlaceholder
     */
    public function __construct(\Magento\Framework\App\RequestInterface $request, $urlPaths, $urlPlaceholder)
    {
        $this->request        = $request;
        $this->urlPaths       = $urlPaths;
        $this->urlPlaceholder = $urlPlaceholder;
    }

    /**
     * Replace placeholders with config values
     *
     * @param array $data
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function process(array $data = [])
    {
        if (empty($data)) {
            return [];
        }
        array_walk_recursive(
            $data,
            function (&$value, $key, $data) {
                if (is_string($value) && str_contains($value, '{')) {  // If _getPlaceholder() would do nothing, skip
                    $value = $this->_processPlaceholders($value, $data);
                }
            },
            $data
        );
        return $data;
    }

    /**
     * Process array data recursively
     *
     * @deprecated 101.0.4 This method isn't used in process() implementation anymore
     * @see process()
     *
     * @param array &$data
     * @param string $path
     * @return void
     */
    protected function _processData(&$data, $path)
    {
        $configValue = $this->_getValue($path, $data);
        if (is_array($configValue)) {
            foreach (array_keys($configValue) as $key) {
                $this->_processData($data, $path . '/' . $key);
            }
        } else {
            $this->_setValue($data, $path, $this->_processPlaceholders($configValue, $data));
        }
    }

    /**
     * Replace placeholders with config values
     *
     * @param string $value
     * @param array $data
     * @return string
     */
    protected function _processPlaceholders($value, $data)
    {
        $placeholder = $this->_getPlaceholder($value);
        if ($placeholder) {
            $url = false;
            if ($placeholder == 'unsecure_base_url') {
                $url = $this->_getValue($this->urlPaths['unsecureBaseUrl'], $data);
            } elseif ($placeholder == 'secure_base_url') {
                $url = $this->_getValue($this->urlPaths['secureBaseUrl'], $data);
            }

            if ($url) {
                $value = str_replace('{{' . $placeholder . '}}', $url, $value);
            } elseif (strpos($value, (string)$this->urlPlaceholder) !== false) {
                $distroBaseUrl = $this->request->getDistroBaseUrl();

                $value = str_replace($this->urlPlaceholder, $distroBaseUrl, $value);
            }

            if (null !== $this->_getPlaceholder($value)) {
                $value = $this->_processPlaceholders($value, $data);
            }
        }
        return $value;
    }

    /**
     * Get placeholder from value
     *
     * @param string $value
     * @return string|null
     */
    protected function _getPlaceholder($value)
    {
        if (is_string($value) && preg_match('/{{(.*)}}.*/', $value, $matches)) {
            $placeholder = $matches[1];
            if ($placeholder == 'unsecure_base_url' ||
                $placeholder == 'secure_base_url' ||
                strpos($value, (string)$this->urlPlaceholder) !== false
            ) {
                return $placeholder;
            }
        }
        return null;
    }

    /**
     * Get array value by path
     *
     * @param string $path
     * @param array $data
     * @return array|null
     */
    protected function _getValue($path, array $data)
    {
        $keys = explode('/', (string)$path);
        foreach ($keys as $key) {
            if (is_array($data) && (isset($data[$key]) || array_key_exists($key, $data))) {
                $data = $data[$key];
            } else {
                return null;
            }
        }
        return $data;
    }

    /**
     * Set array value by path
     *
     * @deprecated 101.0.4 This method isn't used in process() implementation anymore
     * @see process()
     *
     * @param array &$container
     * @param string $path
     * @param string $value
     * @return void
     */
    protected function _setValue(array &$container, $path, $value)
    {
        $segments = explode('/', (string)$path);
        $currentPointer = &$container;
        foreach ($segments as $segment) {
            if (!isset($currentPointer[$segment])) {
                $currentPointer[$segment] = [];
            }
            $currentPointer = &$currentPointer[$segment];
        }
        $currentPointer = $value;
    }
}
