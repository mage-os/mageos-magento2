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
        $groupLength = $priceFormat['groupLength'] ?? 3;

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

        if (!$config || !isset($config['optionPrices'])) {
            return $result;
        }

        if (!$this->weeeHelper->isEnabled()) {
            return $result;
        }

        foreach ($subject->getAllowProducts() as $product) {
            $productId = (string)$product->getId();

            if (!isset($config['optionPrices'][$productId])) {
                continue;
            }

            // Get FPT attributes for this product
            $weeeAttributes = $this->weeeHelper->getProductWeeeAttributesForDisplay($product);

            // Add FPT data to the option price
            $config['optionPrices'][$productId]['weeeAttributes'] = [];

            if (!empty($weeeAttributes)) {
                $weeeTotal = 0;
                foreach ($weeeAttributes as $attribute) {
                    // Use getData('name') which contains the frontend label (label_value or frontend_label)
                    // This is set by \Magento\Weee\Model\Tax::getProductWeeeAttributes() at line 374-376
                    $name = $attribute->getData('name');

                    // Cast to string to handle Magento\Framework\Phrase objects
                    $name = $name ? (string)$name : 'FPT';

                    $config['optionPrices'][$productId]['weeeAttributes'][] = [
                        'name' => $name,
                        'amount' => (float)$attribute->getAmount(),
                        'amount_excl_tax' => (float)$attribute->getAmountExclTax(),
                        'tax_amount' => (float)$attribute->getTaxAmount(),
                    ];

                    $weeeTotal += (float)$attribute->getAmount();
                }

                // Calculate base price without WEEE
                $basePriceAmount = $config['optionPrices'][$productId]['finalPrice']['amount'] - $weeeTotal;

                // Format WEEE amounts for display
                $formattedWeeeAttributes = [];
                foreach ($config['optionPrices'][$productId]['weeeAttributes'] as $weeeAttr) {
                    $formattedWeeeAttributes[] = [
                        'name' => $weeeAttr['name'],
                        'amount' => $weeeAttr['amount'],
                        'formatted' => $this->formatPrice($weeeAttr['amount'], $config['priceFormat'])
                    ];
                }

                // Store WEEE data in finalPrice object (will be used by price-box reloadPrice)
                $config['optionPrices'][$productId]['finalPrice']['weeeAmount'] = $weeeTotal;
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

        return $this->jsonEncoder->encode($config);
    }
}
