<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use MageOS\Installer\Model\Validator\UrlValidator;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Collects backend configuration interactively
 */
class BackendConfig
{
    public function __construct(
        private readonly UrlValidator $urlValidator
    ) {
    }

    /**
     * Collect backend configuration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @return array{frontname: string}
     */
    public function collect(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): array {
        $output->writeln('');
        $output->writeln('<info>=== Backend Configuration ===</info>');

        // Backend frontname (admin path)
        $frontnameQuestion = new Question('? Backend admin path [<comment>admin</comment>]: ', 'admin');
        $frontnameQuestion->setValidator(function ($answer) {
            $result = $this->urlValidator->validateAdminPath($answer ?? 'admin');
            if (!$result['valid']) {
                throw new \RuntimeException($result['error'] ?? 'Invalid admin path');
            }
            return $answer;
        });
        $frontname = $questionHelper->ask($input, $output, $frontnameQuestion);

        // Show security warning if using default
        $validation = $this->urlValidator->validateAdminPath($frontname ?? 'admin');
        if ($validation['warning']) {
            $output->writeln('<comment>⚠️  ' . $validation['warning'] . '</comment>');
        }

        return [
            'frontname' => $frontname ?? 'admin'
        ];
    }
}
