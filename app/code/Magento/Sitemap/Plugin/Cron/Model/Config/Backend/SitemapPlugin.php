<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sitemap\Plugin\Cron\Model\Config\Backend;

use Magento\Cron\Model\Config\Backend\Sitemap;
use Magento\Cron\Model\Config\Source\Frequency;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sitemap\Model\Config\Source\GenerationMethod;

/**
 * Plugin for Cron Sitemap backend model to ensure extended cron job configuration
 */
class SitemapPlugin
{
    /**
     * Cron string path for sitemap schedule
     */
    private const CRON_STRING_PATH = 'crontab/default/jobs/sitemap_generate/schedule/cron_expr';

    /**
     * Cron model path
     */
    private const CRON_MODEL_PATH = 'crontab/default/jobs/sitemap_generate/run/model';

    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    private $configValueFactory;

    /**
     * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory
     */
    public function __construct(
        \Magento\Framework\App\Config\ValueFactory $configValueFactory
    ) {
        $this->configValueFactory = $configValueFactory;
    }

    /**
     * Plugin to override the afterSave behavior to use unified cron job
     *
     * @param Sitemap $subject
     * @param mixed $result
     * @return mixed
     * @throws LocalizedException
     */
    public function afterAfterSave(
        Sitemap $subject,
        mixed $result
    ) {
        $time = $subject->getData('groups/generate/fields/time/value') ?:
            explode(
                ',',
                $subject->getConfig()->getValue(
                    'sitemap/generate/time',
                    $subject->getScope(),
                    $subject->getScopeId()
                ) ?: '0,0,0'
            );
        $frequency = $subject->getValue();
        $generationMethod = $subject->getData('groups/generate/fields/generation_method/value') ?:
            $subject->getConfig()->getValue(
                'sitemap/generate/generation_method',
                $subject->getScope(),
                $subject->getScopeId()
            ) ?: GenerationMethod::STANDARD;

        $cronExprArray = [
            (int)($time[1] ?? 0), //Minute
            (int)($time[0] ?? 0), //Hour
            $frequency == Frequency::CRON_MONTHLY ? '1' : '*', //Day of the Month
            '*', //Month of the Year
            $frequency == Frequency::CRON_WEEKLY ? '1' : '*', //# Day of the Week
        ];

        $cronExprString = join(' ', $cronExprArray);

        try {
            $this->clearCronConfiguration(self::CRON_STRING_PATH);
            $this->clearCronConfiguration(self::CRON_MODEL_PATH);

            $observerModel = $generationMethod === GenerationMethod::BATCH
                ? 'Magento\Sitemap\Model\Batch\Observer::scheduledGenerateSitemaps'
                : 'Magento\Sitemap\Model\Observer::scheduledGenerateSitemaps';

            $this->setCronConfiguration(self::CRON_STRING_PATH, $cronExprString);
            $this->setCronConfiguration(self::CRON_MODEL_PATH, $observerModel);
        } catch (\Exception $e) {
            throw new LocalizedException(__('We can\'t save the cron expression.'));
        }

        // Return the original result from the afterSave method
        return $result;
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
        $this->configValueFactory->create()->load(
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
        $configValue = $this->configValueFactory->create()->load($path, 'path');
        if ($configValue->getId()) {
            $configValue->delete();
        }
    }
}
