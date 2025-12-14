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
use Symfony\Component\Console\Question\ConfirmationQuestion;
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
        $isFirstAttempt = true;

        while (true) {
            try {
                if ($isFirstAttempt) {
                    $output->writeln('');
                    $output->writeln('<info>=== Admin Account ===</info>');
                } else {
                    $output->writeln('');
                    $output->writeln('<info>=== Admin Account (Retry) ===</info>');
                }

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
                    return $answer;
                });
                $password = $questionHelper->ask($input, $output, $passwordQuestion);

                // Check password strength and show info (not blocking)
                $hasLower = preg_match('/[a-z]/', $password);
                $hasUpper = preg_match('/[A-Z]/', $password);
                $hasNumber = preg_match('/[0-9]/', $password);
                $hasSpecial = preg_match('/[^a-zA-Z0-9]/', $password);

                if (!$hasLower || !$hasUpper || !$hasNumber) {
                    $output->writeln('<comment>ℹ️  Weak password detected. Consider using uppercase, lowercase, and numbers for better security.</comment>');
                } elseif (!$hasSpecial) {
                    $output->writeln('<comment>ℹ️  Good password. Consider adding special characters for even better security.</comment>');
                } else {
                    $output->writeln('<info>✓ Strong password detected!</info>');
                }

                return [
                    'firstName' => $firstName ?? '',
                    'lastName' => $lastName ?? '',
                    'email' => $email ?? '',
                    'username' => $username ?? 'admin',
                    'password' => $password ?? ''
                ];
            } catch (\RuntimeException $e) {
                // Show error and ask to retry
                $output->writeln('');
                $output->writeln('<error>❌ ' . $e->getMessage() . '</error>');

                $retryQuestion = new ConfirmationQuestion(
                    "\n<question>? Validation failed. Do you want to try again?</question> [<comment>Y/n</comment>]: ",
                    true
                );
                $retry = $questionHelper->ask($input, $output, $retryQuestion);

                if (!$retry) {
                    throw new \RuntimeException('Admin account configuration failed. Installation aborted.');
                }

                $isFirstAttempt = false;
            }
        }
    }
}
