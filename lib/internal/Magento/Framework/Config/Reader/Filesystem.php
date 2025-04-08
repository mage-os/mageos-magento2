<?php
/**
 *  Copyright 2014 Adobe
 *  All Rights Reserved.
 */

namespace Magento\Framework\Config\Reader;

/**
 * Filesystem configuration loader. Loads configuration from XML files, split by scopes
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @api
 * @since 100.0.2
 */
class Filesystem implements \Magento\Framework\Config\ReaderInterface
{
    /**
     * File locator
     *
     * @var \Magento\Framework\Config\FileResolverInterface
     */
    protected $_fileResolver;

    /**
     * Config converter
     *
     * @var \Magento\Framework\Config\ConverterInterface
     */
    protected $_converter;

    /**
     * The name of file that stores configuration
     *
     * @var string
     */
    protected $_fileName;

    /**
     * Path to corresponding XSD file with validation rules for merged config
     *
     * @var string
     */
    protected $_schema;

    /**
     * Path to corresponding XSD file with validation rules for separate config files
     *
     * @var string
     */
    protected $_perFileSchema;

    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = [];

    /**
     * Class of dom configuration document used for merge
     *
     * @var string
     */
    protected $_domDocumentClass;

    /**
     * @var \Magento\Framework\Config\ValidationStateInterface
     */
    protected $validationState;

    /**
     * @var string
     * @since 100.0.3
     */
    protected $_defaultScope;

    /**
     * @var string
     * @since 100.0.3
     */
    protected $_schemaFile;

    /**
     * Name of an attribute that stands for data type of node values
     *
     * @var string|null
     */
    private $typeAttributeName;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Config\FileResolverInterface $fileResolver
     * @param \Magento\Framework\Config\ConverterInterface $converter
     * @param \Magento\Framework\Config\SchemaLocatorInterface $schemaLocator
     * @param \Magento\Framework\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     * @param string|null $typeAttributeName
     */
    public function __construct(
        \Magento\Framework\Config\FileResolverInterface $fileResolver,
        \Magento\Framework\Config\ConverterInterface $converter,
        \Magento\Framework\Config\SchemaLocatorInterface $schemaLocator,
        \Magento\Framework\Config\ValidationStateInterface $validationState,
        $fileName,
        $idAttributes = [],
        $domDocumentClass = \Magento\Framework\Config\Dom::class,
        $defaultScope = 'global',
        ?string $typeAttributeName = null,
    ) {
        $this->_fileResolver = $fileResolver;
        $this->_converter = $converter;
        $this->_fileName = $fileName;
        $this->_idAttributes = array_replace($this->_idAttributes, $idAttributes);
        $this->validationState = $validationState;
        $this->_schemaFile = $schemaLocator->getSchema();
        $this->_perFileSchema = $schemaLocator->getPerFileSchema() && $validationState->isValidationRequired()
            ? $schemaLocator->getPerFileSchema() : null;
        $this->_domDocumentClass = $domDocumentClass;
        $this->_defaultScope = $defaultScope;
        $this->typeAttributeName = $typeAttributeName;
    }

    /**
     * Load configuration scope
     *
     * @param string|null $scope
     * @return array
     */
    public function read($scope = null)
    {
        $scope = $scope ?: $this->_defaultScope;
        $fileList = $this->_fileResolver->get($this->_fileName, $scope);
        if (!count($fileList)) {
            return [];
        }
        $output = $this->_readFiles($fileList);

        return $output;
    }

    /**
     * Read configuration files
     *
     * @param array $fileList
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _readFiles($fileList)
    {
        /** @var \Magento\Framework\Config\Dom $configMerger */
        $configMerger = null;
        foreach ($fileList as $key => $content) {
            try {
                if (!$configMerger) {
                    $configMerger = $this->_createConfigMerger($this->_domDocumentClass, $content);
                } else {
                    $configMerger->merge($content);
                }
            } catch (\Magento\Framework\Config\Dom\ValidationException $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    new \Magento\Framework\Phrase(
                        'The XML in file "%1" is invalid:' . "\n%2\nVerify the XML and try again.",
                        [$key, $e->getMessage()]
                    )
                );
            }
        }

        if ($this->validationState->isValidationRequired()) {
            $errors = [];
            if ($configMerger && !$configMerger->validate($this->_schemaFile, $errors)) {
                // The merged XML is invalid, but each XML document is individually valid.
                // (If they had errors, we would have thrown an exception in the loop above.)
                // Let's work out which document is causing us a problem.
                $configMerger = null;
                foreach ($fileList as $key => $content) {
                    if (!$configMerger) {
                        $configMerger = $this->_createConfigMerger($this->_domDocumentClass, $content);
                    } else {
                        $configMerger->merge($content);
                    }

                    if (!$configMerger->validate($this->_schemaFile)) {
                        array_unshift($errors, "Error in merged XML after reading $key");
                        break;
                    }
                }

                $message = "Invalid Document \n";
                throw new \Magento\Framework\Exception\LocalizedException(
                    new \Magento\Framework\Phrase($message . implode("\n", $errors))
                );
            }
        }

        $output = [];
        if ($configMerger) {
            $output = $this->_converter->convert($configMerger->getDom());
        }
        return $output;
    }

    /**
     * Return newly created instance of a config merger
     *
     * @param string $mergerClass
     * @param string $initialContents
     * @return \Magento\Framework\Config\Dom
     * @throws \UnexpectedValueException
     */
    protected function _createConfigMerger($mergerClass, $initialContents)
    {
        $result = new $mergerClass(
            $initialContents,
            $this->validationState,
            $this->_idAttributes,
            $this->typeAttributeName,
            $this->_perFileSchema
        );
        if (!$result instanceof \Magento\Framework\Config\Dom) {
            throw new \UnexpectedValueException(
                "Instance of the DOM config merger is expected, got {$mergerClass} instead."
            );
        }
        return $result;
    }
}
