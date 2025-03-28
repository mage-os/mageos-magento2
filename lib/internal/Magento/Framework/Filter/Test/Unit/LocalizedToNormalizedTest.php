<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Filter\Test\Unit;

use Magento\Framework\Filter\LocalizedToNormalized;
use PHPUnit\Framework\TestCase;
use NumberFormatter;

class LocalizedToNormalizedTest extends TestCase
{

    /**
     * @param string $value
     * @param array $options
     * @param string|array $expectedValues
     *
     * @dataProvider localizedToNormalizedDataProvider
     */
    public function testLocalizedToNormalized($value, $options, $expectedValue)
    {
        $filter = new LocalizedToNormalized($options);
        $this->assertEquals($expectedValue, $filter->filter($value));
    }

    /**
     * @return array
     */
    public static function localizedToNormalizedDataProvider()
    {
        
        return [
            '1' => [
                "0.5",
                [
                    'locale' => 'nl',
                    'date_format' => null,
                    'precision' => null,
                    'type' => NumberFormatter::TYPE_DOUBLE
                ],
                "0.5"
            ],
            '2' => [
                "0.5",
                [
                    'locale' => 'en',
                    'date_format' => null,
                    'precision' => null,
                    'type' => ' '
                ],
                "0.5"
            ],
            '3' => [
                "2",
                [
                    'locale' => 'en',
                    'date_format' => null,
                    'precision' => null,
                    'type' => ''
                ],
                "2"
            ],
            '4' => [
                '2014-03-30',
                [
                    'locale' => 'en',
                    'date_format' => 'Y-M-d',
                    'precision' => null,
                    'type' => ''
                ],
                [
                    "date_format" => "Y-M-d",
                    "locale" => "en",
                    "year" => "2014",
                    "month" => "03",
                    "day" => "30",
                ]
            ]
        ];
    }
}
