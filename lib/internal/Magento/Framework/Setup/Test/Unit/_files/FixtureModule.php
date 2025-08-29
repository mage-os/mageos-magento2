<?php
declare(strict_types=1);

namespace Magento\Framework\Setup\Test\Unit\Mvc\Fixture;

final class Module
{
    public function getConfig(): array
    {
        return [
            'service_manager' => [
                'services' => [
                    'foo' => 'bar',
                ],
            ],
        ];
    }
}


