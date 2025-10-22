<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */

namespace Magento\Framework\Console;

/**
 * Class CommandList has a list of commands, which can be extended via DI configuration.
 */
class CommandList implements CommandListInterface
{
    /**
     * @var string[]
     */
    protected $commands;

    /**
     * Constructor
     *
     * @param array $commands
     */
    public function __construct(array $commands = [])
    {
        $this->commands = $commands;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommands()
    {
        return $this->commands;
    }
}
