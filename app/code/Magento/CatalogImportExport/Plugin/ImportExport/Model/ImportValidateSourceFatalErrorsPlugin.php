<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Plugin\ImportExport\Model;

use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;

/**
 * When validation strategy is "Stop on Error", treat fatal (critical) validation errors as a failed check
 */
class ImportValidateSourceFatalErrorsPlugin
{

    /**
     * Fail validation when strategy is stop-on-errors
     *
     * @param Import $subject
     * @param bool $result
     * @param mixed $source
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterValidateSource(Import $subject, bool $result, $source): bool
    {
        if ($result === false) {
            return false;
        }
        if ($subject->getData(Import::FIELD_NAME_VALIDATION_STRATEGY)
            !== ProcessingErrorAggregatorInterface::VALIDATION_STRATEGY_STOP_ON_ERROR
        ) {
            return $result;
        }
        if ($subject->getErrorAggregator()->hasFatalExceptions()) {
            return false;
        }

        return $result;
    }
}
