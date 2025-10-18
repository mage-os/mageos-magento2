<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */

namespace Magento\ImportExport\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem;
use Magento\ImportExport\Model\Export\ConfigInterface;
use Magento\ImportExport\Model\Export\Entity\Factory;
use Psr\Log\LoggerInterface;

/**
 * Export model
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 * @deprecated 100.3.2
 * @see \Magento\ImportExport\Api\ExportManagementInterface
 */
class Export extends \Magento\ImportExport\Model\AbstractModel
{
    public const FILTER_ELEMENT_GROUP = 'export_filter';

    public const FILTER_ELEMENT_SKIP = 'skip_attr';

    /**
     * Allow multiple values wrapping in double quotes for additional attributes.
     */
    public const FIELDS_ENCLOSURE = 'fields_enclosure';

    /**
     * Filter fields types.
     */
    public const FILTER_TYPE_SELECT = 'select';

    public const FILTER_TYPE_MULTISELECT = 'multiselect';

    public const FILTER_TYPE_INPUT = 'input';

    public const FILTER_TYPE_DATE = 'date';

    public const FILTER_TYPE_NUMBER = 'number';

    /**
     * @var \Magento\ImportExport\Model\Export\Entity\AbstractEntity
     */
    protected $_entityAdapter;

    /**
     * Writer object instance.
     *
     * @var \Magento\ImportExport\Model\Export\Adapter\AbstractAdapter
     */
    protected $_writer;

    /**
     * @var \Magento\ImportExport\Model\Export\ConfigInterface
     */
    protected $_exportConfig;

    /**
     * @var \Magento\ImportExport\Model\Export\Entity\Factory
     */
    protected $_entityFactory;

    /**
     * @var \Magento\ImportExport\Model\Export\Adapter\Factory
     */
    protected $_exportAdapterFac;

    /**
     * @var array
     */
    private static $backendTypeToFilterMapper = [
        'datetime' => self::FILTER_TYPE_DATE,
        'decimal' => self::FILTER_TYPE_NUMBER,
        'int' => self::FILTER_TYPE_NUMBER,
        'varchar' => self::FILTER_TYPE_INPUT,
        'text' => self::FILTER_TYPE_INPUT
    ];

    /**
     * @var LocaleEmulatorInterface
     */
    private $localeEmulator;

    /**
     * @param LoggerInterface $logger
     * @param Filesystem $filesystem
     * @param ConfigInterface $exportConfig
     * @param Factory $entityFactory
     * @param \Magento\ImportExport\Model\Export\Adapter\Factory $exportAdapterFac
     * @param array $data
     * @param LocaleEmulatorInterface|null $localeEmulator
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\ImportExport\Model\Export\ConfigInterface $exportConfig,
        \Magento\ImportExport\Model\Export\Entity\Factory $entityFactory,
        \Magento\ImportExport\Model\Export\Adapter\Factory $exportAdapterFac,
        array $data = [],
        ?LocaleEmulatorInterface $localeEmulator = null
    ) {
        $this->_exportConfig = $exportConfig;
        $this->_entityFactory = $entityFactory;
        $this->_exportAdapterFac = $exportAdapterFac;
        parent::__construct($logger, $filesystem, $data);
        $this->localeEmulator = $localeEmulator ?? ObjectManager::getInstance()->get(LocaleEmulatorInterface::class);
    }

    /**
     * Create instance of entity adapter and return it
     *
     * @return \Magento\ImportExport\Model\Export\Entity\AbstractEntity
     * |\Magento\ImportExport\Model\Export\AbstractEntity
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getEntityAdapter()
    {
        if (!$this->_entityAdapter) {
            $entities = $this->_exportConfig->getEntities();

            if (isset($entities[$this->getEntity()])) {
                try {
                    $this->_entityAdapter = $this->_entityFactory->create($entities[$this->getEntity()]['model']);
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Please enter a correct entity model.')
                    );
                }
                if (!$this->_entityAdapter instanceof \Magento\ImportExport\Model\Export\Entity\AbstractEntity &&
                    !$this->_entityAdapter instanceof \Magento\ImportExport\Model\Export\AbstractEntity
                ) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __(
                            'The entity adapter object must be an instance of %1 or %2.',
                            \Magento\ImportExport\Model\Export\Entity\AbstractEntity::class,
                            \Magento\ImportExport\Model\Export\AbstractEntity::class
                        )
                    );
                }

                // check for entity codes integrity
                if ($this->getEntity() != $this->_entityAdapter->getEntityTypeCode()) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('The input entity code is not equal to entity adapter code.')
                    );
                }
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a correct entity.'));
            }
            $this->_entityAdapter->setParameters($this->getData());
        }
        return $this->_entityAdapter;
    }

    /**
     * Get writer object.
     *
     * @return \Magento\ImportExport\Model\Export\Adapter\AbstractAdapter
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getWriter()
    {
        if (!$this->_writer) {
            $fileFormats = $this->_exportConfig->getFileFormats();

            if (isset($fileFormats[$this->getFileFormat()])) {
                try {
                    $this->_writer = $this->_exportAdapterFac->create($fileFormats[$this->getFileFormat()]['model']);
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Please enter a correct entity model.')
                    );
                }
                if (!$this->_writer instanceof \Magento\ImportExport\Model\Export\Adapter\AbstractAdapter) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __(
                            'The adapter object must be an instance of %1.',
                            \Magento\ImportExport\Model\Export\Adapter\AbstractAdapter::class
                        )
                    );
                }
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__('Please correct the file format.'));
            }
        }
        return $this->_writer;
    }

    /**
     * Export data.
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function export()
    {
        return $this->localeEmulator->emulate(
            $this->exportCallback(...),
            $this->getData('locale') ?: null
        );
    }

    /**
     * Export data.
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function exportCallback()
    {
        if (isset($this->_data[self::FILTER_ELEMENT_GROUP])) {
            $this->addLogComment(__('Begin export of %1', $this->getEntity()));
            $result = $this->_getEntityAdapter()->setWriter($this->_getWriter())->export();
            $countRows = substr_count($result, "\n");
            if (!$countRows) {
                throw new \Magento\Framework\Exception\LocalizedException(__('There is no data for the export.'));
            }
            if ($result) {
                $this->addLogComment([__('Exported %1 rows.', $countRows), __('The export is finished.')]);
            }
            return $result;
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please provide filter data.'));
        }
    }

    /**
     * Clean up already loaded attribute collection.
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @return \Magento\Framework\Data\Collection
     */
    public function filterAttributeCollection(\Magento\Framework\Data\Collection $collection)
    {
        return $this->_getEntityAdapter()->filterAttributeCollection($collection);
    }

    /**
     * Determine filter type for specified attribute.
     *
     * @static
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * phpcs:disable Magento2.Functions.StaticFunction
     */
    public static function getAttributeFilterType(\Magento\Eav\Model\Entity\Attribute $attribute)
    {
        if ($attribute->usesSource() || $attribute->getFilterOptions()) {
            return 'multiselect' == $attribute->getFrontendInput() ?
                self::FILTER_TYPE_MULTISELECT : self::FILTER_TYPE_SELECT;
        }

        if (isset(self::$backendTypeToFilterMapper[$attribute->getBackendType()])) {
            return self::$backendTypeToFilterMapper[$attribute->getBackendType()];
        }

        if ($attribute->isStatic()) {
            return self::getStaticAttributeFilterType($attribute);
        }

        throw new \Magento\Framework\Exception\LocalizedException(
            __('We can\'t determine the attribute filter type.')
        );
    }
    //phpcs:enable Magento2.Functions.StaticFunction

    /**
     * Determine filter type for static attribute.
     *
     * @static
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return string
     * phpcs:disable Magento2.Functions.StaticFunction
     */
    public static function getStaticAttributeFilterType(\Magento\Eav\Model\Entity\Attribute $attribute)
    {
        if (in_array($attribute->getAttributeCode(), ['category_ids', 'media_gallery'])) {
            return self::FILTER_TYPE_INPUT;
        }
        $columns = $attribute->getFlatColumns();
        if (empty($columns)) {
            return self::FILTER_TYPE_INPUT;
        }
        switch ($columns[$attribute->getAttributeCode()]['type']) {
            case \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER:
            case \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT:
                $type = self::FILTER_TYPE_NUMBER;
                break;
            case \Magento\Framework\DB\Ddl\Table::TYPE_DATE:
            case \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME:
            case \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP:
                $type = self::FILTER_TYPE_DATE;
                break;
            default:
                $type = self::FILTER_TYPE_INPUT;
        }
        return $type;
    }
    //phpcs:enable Magento2.Functions.StaticFunction

    /**
     * MIME-type for 'Content-Type' header.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->_getWriter()->getContentType();
    }

    /**
     * Override standard entity getter.
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getEntity()
    {
        if (empty($this->_data['entity'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Entity is unknown'));
        }
        return $this->_data['entity'];
    }

    /**
     * Entity attributes collection getter.
     *
     * @return \Magento\Framework\Data\Collection
     */
    public function getEntityAttributeCollection()
    {
        return $this->_getEntityAdapter()->getAttributeCollection();
    }

    /**
     * Override standard entity getter.
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFileFormat()
    {
        if (empty($this->_data['file_format'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t identify this file format.'));
        }
        return $this->_data['file_format'];
    }

    /**
     * Return file name for downloading.
     *
     * @return string
     */
    public function getFileName()
    {
        $fileName = null;
        $entityAdapter = $this->_getEntityAdapter();
        if ($entityAdapter instanceof \Magento\ImportExport\Model\Export\AbstractEntity) {
            $fileName = $entityAdapter->getFileName();
        }
        if (!$fileName) {
            $fileName = $this->getEntity();
        }
        return $fileName . '_' . date('Ymd_His') . '.' . $this->_getWriter()->getFileExtension();
    }
}
