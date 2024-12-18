<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

/** @var \Magento\Translation\Model\ResourceModel\StringUtils $translateString */
$translateString = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Translation\Model\ResourceModel\StringUtils::class
);
$translateString->saveTranslate(
    'Unable to place order: %message',
    'Kan geen bestelling plaatsen: %message',
    "nl_NL",
    0
);
$translateString->saveTranslate(
    'Some addresses can\'t be used due to the configurations for specific countries.',
    'Sommige adressen kunnen niet worden gebruikt vanwege de configuraties van specifieke landen.',
    "nl_NL",
    0
);
