<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Weee\Plugin\ConfigurableProduct\Block\Product\View\Type;

use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as ConfigurableBlock;
use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Weee\Helper\Data as WeeeHelper;

/**
 * Plugin to add FPT data to configurable product JSON config
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 * phpcs:disable Magento2.Functions.StaticFunction
 */
class Configurable
{
    /**
     * @param WeeeHelper $weeeHelper
     * @param EncoderInterface $jsonEncoder
     * @param DecoderInterface $jsonDecoder
     */
    public function __construct(
        private readonly WeeeHelper $weeeHelper,
        private readonly EncoderInterface $jsonEncoder,
        private readonly DecoderInterface $jsonDecoder
    ) {
    }

    /**
     * Format price using the store's price format
     *
     * @param float $amount
     * @param array $priceFormat
     * @return string
     */
    private function formatPrice(float $amount, array $priceFormat): string
    {
        $pattern = $priceFormat['pattern'] ?? '%s';
        $precision = $priceFormat['precision'] ?? 2;
        $decimalSymbol = $priceFormat['decimalSymbol'] ?? '.';
        $groupSymbol = $priceFormat['groupSymbol'] ?? ',';

        $formatted = number_format($amount, $precision, $decimalSymbol, $groupSymbol);
        return str_replace('%s', $formatted, $pattern);
    }

    /**
     * Add FPT data to option prices
     *
     * @param ConfigurableBlock $subject
     * @param string $result
     * @return string
     */
    public function afterGetJsonConfig(
        ConfigurableBlock $subject,
        string $result
    ): string {
        $config = $this->jsonDecoder->decode($result);

        if (!$this->shouldProcessWeee($config)) {
            return $result;
        }

        foreach ($subject->getAllowProducts() as $product) {
            $productId = (string)$product->getId();

            if (!isset($config['optionPrices'][$productId])) {
                continue;
            }

            $this->addWeeeDataToProduct($config, $productId, $product);
        }

        return $this->jsonEncoder->encode($config);
    }

    /**
     * Check if WEEE should be processed
     *
     * @param array|null $config
     * @return bool
     */
    private function shouldProcessWeee(?array $config): bool
    {
        return $config
            && isset($config['optionPrices'])
            && $this->weeeHelper->isEnabled();
    }

    /**
     * Add WEEE data to product option price
     *
     * @param array $config
     * @param string $productId
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    private function addWeeeDataToProduct(array &$config, string $productId, $product): void
    {
        $weeeAttributes = $this->weeeHelper->getProductWeeeAttributesForDisplay($product);
        $config['optionPrices'][$productId]['weeeAttributes'] = [];

        if (empty($weeeAttributes)) {
            return;
        }

        $weeeData = $this->processWeeeAttributes($weeeAttributes);
        $this->addFormattedWeeeData($config, $productId, $weeeData);
    }

    /**
     * Process WEEE attributes and calculate total
     *
     * @param array $weeeAttributes
     * @return array
     */
    private function processWeeeAttributes(array $weeeAttributes): array
    {
        $processedAttributes = [];
        $weeeTotal = 0;

        foreach ($weeeAttributes as $attribute) {
            $name = $attribute->getData('name');
            $name = $name ? (string)$name : 'FPT';
            $amount = (float)$attribute->getAmount();

            $processedAttributes[] = [
                'name' => $name,
                'amount' => $amount,
                'amount_excl_tax' => (float)$attribute->getAmountExclTax(),
                'tax_amount' => (float)$attribute->getTaxAmount(),
            ];

            $weeeTotal += $amount;
        }

        return [
            'attributes' => $processedAttributes,
            'total' => $weeeTotal
        ];
    }

    /**
     * Add formatted WEEE data to config
     *
     * @param array $config
     * @param string $productId
     * @param array $weeeData
     * @return void
     */
    private function addFormattedWeeeData(array &$config, string $productId, array $weeeData): void
    {
        $config['optionPrices'][$productId]['weeeAttributes'] = $weeeData['attributes'];
        $basePriceAmount = $config['optionPrices'][$productId]['finalPrice']['amount'] - $weeeData['total'];

        $formattedWeeeAttributes = [];
        foreach ($weeeData['attributes'] as $weeeAttr) {
            $formattedWeeeAttributes[] = [
                'name' => $weeeAttr['name'],
                'amount' => $weeeAttr['amount'],
                'formatted' => $this->formatPrice($weeeAttr['amount'], $config['priceFormat'])
            ];
        }

        $config['optionPrices'][$productId]['finalPrice']['weeeAmount'] = $weeeData['total'];
        $config['optionPrices'][$productId]['finalPrice']['weeeAttributes'] = $formattedWeeeAttributes;
        $config['optionPrices'][$productId]['finalPrice']['amountWithoutWeee'] = $basePriceAmount;
        $config['optionPrices'][$productId]['finalPrice']['formattedWithoutWeee'] =
            $this->formatPrice($basePriceAmount, $config['priceFormat']);
        $config['optionPrices'][$productId]['finalPrice']['formattedWithWeee'] =
            $this->formatPrice(
                $config['optionPrices'][$productId]['finalPrice']['amount'],
                $config['priceFormat']
            );
    }
}
