<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Vendor\LazyTypesFixture;

class TrivialAssigns
{
    private const DEFAULT_MODE = 'default';

    /**
     * @var mixed
     */
    private mixed $config;

    /**
     * @var array
     */
    private array $data;

    /**
     * @var string
     */
    private string $mode;

    /**
     * @var int
     */
    private int $limit;

    /**
     * @var string|null
     */
    private ?string $label;

    /**
     * @var string
     */
    private string $flagName;

    public function __construct(mixed $config, ?array $data = null, int|string $limit = -1, bool $flag = false)
    {
        $this->config = $config;
        $this->data = $data ?? [];
        $this->mode ??= self::DEFAULT_MODE;
        $this->limit = (int)$limit;
        $this->label = $data['label'] ?? null;
        $this->flagName = $flag ? 'on' : 'off';
    }
}
