<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Backend Model for Currency import options
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Cron\Model\Config\Backend;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sitemap\Model\Config\Source\GenerationMethod;

/**
 * Sitemap configuration
 */
class Sitemap extends \Magento\Framework\App\Config\Value
{
    /**
     * Cron string path for product alerts
     */
    public const CRON_STRING_PATH = 'crontab/default/jobs/sitemap_generate/schedule/cron_expr';

    /**
     * Cron mode path
     */
    public const CRON_MODEL_PATH = 'crontab/default/jobs/sitemap_generate/run/model';

    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $_configValueFactory;

    /**
     * @var string
     */
    protected $_runModelPath = '';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param string $runModelPath
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        $runModelPath = '',
        array $data = []
    ) {
        $this->_runModelPath = $runModelPath;
        $this->_configValueFactory = $configValueFactory;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * After save handler
     *
     * @return $this
     * @throws LocalizedException
     */
    public function afterSave()
    {
        $time = $this->getData('groups/generate/fields/time/value') ?:
            explode(
                ',',
                $this->_config->getValue('sitemap/generate/time', $this->getScope(), $this->getScopeId()) ?: '0,0,0'
            );
        $frequency = $this->getValue();
        $generationMethod = $this->getData('groups/generate/fields/generation_method/value') ?:
            $this->_config->getValue('sitemap/generate/generation_method', $this->getScope(), $this->getScopeId())
                ?: GenerationMethod::STANDARD;

        $cronExprArray = [
            (int)($time[1] ?? 0), //Minute
            (int)($time[0] ?? 0), //Hour
            $frequency == \Magento\Cron\Model\Config\Source\Frequency::CRON_MONTHLY ? '1' : '*', //Day of the Month
            '*', //Month of the Year
            $frequency == \Magento\Cron\Model\Config\Source\Frequency::CRON_WEEKLY ? '1' : '*', //# Day of the Week
        ];

        $cronExprString = join(' ', $cronExprArray);

        try {
            // Clear old cron configurations first to prevent conflicts
            $this->clearCronConfiguration(self::CRON_STRING_PATH);
            $this->clearCronConfiguration(self::CRON_MODEL_PATH);

            // Configure the selected generation method with correct model class
            if ($generationMethod === GenerationMethod::BATCH) {
                $this->setCronConfiguration(self::CRON_STRING_PATH, $cronExprString);
                $this->setCronConfiguration(
                    self::CRON_MODEL_PATH,
                    'Magento\Sitemap\Model\Batch\Observer::scheduledGenerateSitemaps'
                );
            } else {
                $this->setCronConfiguration(self::CRON_STRING_PATH, $cronExprString);
                $this->setCronConfiguration(
                    self::CRON_MODEL_PATH,
                    'Magento\Sitemap\Model\Observer::scheduledGenerateSitemaps'
                );
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__('We can\'t save the cron expression.'));
        }
        return parent::afterSave();
    }

    /**
     * Set cron configuration value
     *
     * @param string $path
     * @param string $value
     * @return void
     */
    private function setCronConfiguration(string $path, string $value): void
    {
        $this->_configValueFactory->create()->load(
            $path,
            'path'
        )->setValue(
            $value
        )->setPath(
            $path
        )->save();
    }

    /**
     * Clear cron configuration value
     *
     * @param string $path
     * @return void
     */
    private function clearCronConfiguration(string $path): void
    {
        $configValue = $this->_configValueFactory->create()->load($path, 'path');
        if ($configValue->getId()) {
            $configValue->delete();
        }
    }
}
