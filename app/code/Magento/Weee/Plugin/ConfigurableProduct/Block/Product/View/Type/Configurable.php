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
use Magento\Catalog\Model\Product;

/**
 * Plugin to add FPT data to configurable product JSON config
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 * phpcs:disable Magento2.Functions.StaticFunction
 */
class Configurable
{
    public function __construct(
        private readonly WeeeHelper $weeeHelper,
        private readonly EncoderInterface $jsonEncoder,
        private readonly DecoderInterface $jsonDecoder
    ) {}

    /**
     * Add FPT/WEEE data to option prices
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

            $this->injectWeeeData($config, $productId, $product);
        }

        return $this->jsonEncoder->encode($config);
    }

    /**
     * Check if WEEE should be processed
     */
    private function shouldProcessWeee(?array $config): bool
    {
        return !empty($config['optionPrices']) && $this->weeeHelper->isEnabled();
    }

    /**
     * Inject processed WEEE data into config
     */
    private function injectWeeeData(array &$config, string $productId, Product $product): void
    {
        $attributes = $this->weeeHelper->getProductWeeeAttributesForDisplay($product);

        if (empty($attributes)) {
            return;
        }

        $weeeData = $this->processWeeeAttributes($attributes);

        $this->appendFormattedWeee(
            $config['optionPrices'][$productId]['finalPrice'],
            $config['priceFormat'],
            $weeeData
        );
    }

    /**
     * Convert raw attribute objects into array data
     */
    private function processWeeeAttributes(array $weeeAttributes): array
    {
        $processed = [];
        $total = 0.0;

        foreach ($weeeAttributes as $attribute) {
            $amount = (float)$attribute->getAmount();
            $name = (string)($attribute->getData('name') ?: 'FPT');

            $processed[] = [
                'name' => $name,
                'amount' => $amount,
                'amount_excl_tax' => (float)$attribute->getAmountExclTax(),
                'tax_amount' => (float)$attribute->getTaxAmount(),
            ];

            $total += $amount;
        }

        return ['attributes' => $processed, 'total' => $total];
    }

    /**
     * Add formatted WEEE data to price array
     */
    private function appendFormattedWeee(
        array &$finalPrice,
        array $priceFormat,
        array $weeeData
    ): void {
        $finalAmount = (float)$finalPrice['amount'];
        $baseAmount = $finalAmount - $weeeData['total'];

        // Format each attribute
        $formattedAttrs = array_map(
            fn($attr) => [
                'name' => $attr['name'],
                'amount' => $attr['amount'],
                'formatted' => $this->formatPrice($attr['amount'], $priceFormat)
            ],
            $weeeData['attributes']
        );

        $finalPrice = array_merge(
            $finalPrice,
            [
                'weeeAmount'           => $weeeData['total'],
                'weeeAttributes'       => $formattedAttrs,
                'amountWithoutWeee'    => $baseAmount,
                'formattedWithoutWeee' => $this->formatPrice($baseAmount, $priceFormat),
                'formattedWithWeee'    => $this->formatPrice($finalAmount, $priceFormat),
            ]
        );
    }

    /**
     * Format price using the store's price format
     */
    private function formatPrice(float $amount, array $priceFormat): string
    {
        $pattern        = $priceFormat['pattern']        ?? '%s';
        $precision      = $priceFormat['precision']      ?? 2;
        $decimalSymbol  = $priceFormat['decimalSymbol']  ?? '.';
        $groupSymbol    = $priceFormat['groupSymbol']    ?? ',';

        return str_replace(
            '%s',
            number_format($amount, $precision, $decimalSymbol, $groupSymbol),
            $pattern
        );
    }
}
