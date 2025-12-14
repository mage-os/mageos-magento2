<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use MageOS\Installer\Model\Validator\EmailValidator;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Collects admin account configuration interactively
 */
class AdminConfig
{
    public function __construct(
        private readonly EmailValidator $emailValidator
    ) {
    }

    /**
     * Collect admin account configuration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @return array{firstName: string, lastName: string, email: string, username: string, password: string}
     */
    public function collect(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): array {
        $output->writeln('');
        $output->writeln('<info>=== Admin Account ===</info>');

        // First name
        $firstNameQuestion = new Question('? Admin first name: ');
        $firstNameQuestion->setValidator(function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException('First name cannot be empty');
            }
            return $answer;
        });
        $firstName = $questionHelper->ask($input, $output, $firstNameQuestion);

        // Last name
        $lastNameQuestion = new Question('? Admin last name: ');
        $lastNameQuestion->setValidator(function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException('Last name cannot be empty');
            }
            return $answer;
        });
        $lastName = $questionHelper->ask($input, $output, $lastNameQuestion);

        // Email
        $emailQuestion = new Question('? Admin email: ');
        $emailQuestion->setValidator(function ($answer) {
            $result = $this->emailValidator->validate($answer ?? '');
            if (!$result['valid']) {
                throw new \RuntimeException($result['error'] ?? 'Invalid email');
            }
            return $answer;
        });
        $email = $questionHelper->ask($input, $output, $emailQuestion);

        // Username
        $usernameQuestion = new Question('? Admin username [<comment>admin</comment>]: ', 'admin');
        $username = $questionHelper->ask($input, $output, $usernameQuestion);

        // Password
        $passwordQuestion = new Question('? Admin password: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);
        $passwordQuestion->setValidator(function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException('Password cannot be empty');
            }
            if (strlen($answer) < 7) {
                throw new \RuntimeException('Password must be at least 7 characters long');
            }
            // Check password strength
            $hasLower = preg_match('/[a-z]/', $answer);
            $hasUpper = preg_match('/[A-Z]/', $answer);
            $hasNumber = preg_match('/[0-9]/', $answer);

            if (!$hasLower || !$hasUpper || !$hasNumber) {
                throw new \RuntimeException(
                    'Password must contain at least one lowercase letter, one uppercase letter, and one number'
                );
            }

            return $answer;
        });
        $password = $questionHelper->ask($input, $output, $passwordQuestion);

        $output->writeln('<info>✓ Strong password detected!</info>');

        return [
            'firstName' => $firstName ?? '',
            'lastName' => $lastName ?? '',
            'email' => $email ?? '',
            'username' => $username ?? 'admin',
            'password' => $password ?? ''
        ];
    }
}
