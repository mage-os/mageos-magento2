<?php
declare(strict_types=1);

namespace Magento\Framework\Setup\Mvc;

final class TestModule
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


