<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Collects sample data configuration interactively
 */
class SampleDataConfig
{
    /**
     * Collect sample data configuration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @return array{install: bool}
     */
    public function collect(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): array {
        $output->writeln('');
        $output->writeln('<info>=== Optional Features ===</info>');

        $sampleDataQuestion = new ConfirmationQuestion(
            '? Install sample data? [<comment>y/N</comment>]: ',
            false
        );
        $installSampleData = $questionHelper->ask($input, $output, $sampleDataQuestion);

        return [
            'install' => (bool)$installSampleData
        ];
    }
}
