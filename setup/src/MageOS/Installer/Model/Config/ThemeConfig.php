<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use MageOS\Installer\Model\Theme\ThemeRegistry;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Collects theme configuration interactively
 */
class ThemeConfig
{
    public function __construct(
        private readonly ThemeRegistry $themeRegistry
    ) {
    }

    /**
     * Collect theme configuration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @return array{
     *     install: bool,
     *     theme: string|null,
     *     hyva_project_key: string|null,
     *     hyva_api_token: string|null
     * }
     */
    public function collect(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): array {
        // Ask if user wants to install a theme (default YES to encourage Hyva)
        $installQuestion = new ConfirmationQuestion(
            '? Install a theme? (Hyva recommended) [<comment>Y/n</comment>]: ',
            true
        );
        $installTheme = $questionHelper->ask($input, $output, $installQuestion);

        if (!$installTheme) {
            $output->writeln('<comment>ℹ️  Skipping theme installation (Luma will be used)</comment>');
            return [
                'install' => false,
                'theme' => ThemeRegistry::THEME_LUMA,
                'hyva_project_key' => null,
                'hyva_api_token' => null
            ];
        }

        // Show available themes (sorted by sort_order)
        $themes = $this->themeRegistry->getAvailableThemes();

        // Sort by sort_order
        uasort($themes, fn($a, $b) => $a['sort_order'] <=> $b['sort_order']);

        $themeChoices = [];
        $themeMap = [];

        $output->writeln('');
        $output->writeln('  <info>Available themes:</info>');

        $index = 1;
        $defaultIndex = 1;
        foreach ($themes as $themeId => $themeInfo) {
            $choice = sprintf('%d) %s - %s', $index, $themeInfo['name'], $themeInfo['description']);
            $output->writeln('  ' . $choice);
            $themeChoices[$index] = $themeInfo['name'];
            $themeMap[$index] = $themeId;

            // Remember Hyva's index as default
            if ($themeInfo['is_recommended']) {
                $defaultIndex = $index;
            }

            $index++;
        }

        $output->writeln('');

        // Ask user to select theme (Hyva is default)
        $themeQuestion = new ChoiceQuestion(
            sprintf('? Select theme [<comment>%d</comment>]: ', $defaultIndex),
            $themeChoices,
            $defaultIndex
        );
        $selectedIndex = $questionHelper->ask($input, $output, $themeQuestion);

        // Get the theme ID from the map
        $themeId = null;
        foreach ($themeMap as $idx => $id) {
            if ($themeChoices[$idx] === $selectedIndex) {
                $themeId = $id;
                break;
            }
        }

        if (!$themeId) {
            throw new \RuntimeException('Invalid theme selection');
        }

        // If already installed (Luma), we're done
        if ($this->themeRegistry->isAlreadyInstalled($themeId)) {
            $output->writeln(sprintf(
                '<info>✓ Using %s theme (already installed)</info>',
                $themes[$themeId]['name']
            ));
            return [
                'install' => false,
                'theme' => $themeId,
                'hyva_project_key' => null,
                'hyva_api_token' => null
            ];
        }

        // For Hyva, collect credentials
        if ($themeId === ThemeRegistry::THEME_HYVA) {
            return $this->collectHyvaCredentials($input, $output, $questionHelper, $themeId);
        }

        return [
            'install' => true,
            'theme' => $themeId,
            'hyva_project_key' => null,
            'hyva_api_token' => null
        ];
    }

    /**
     * Collect Hyva-specific credentials
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @param string $themeId
     * @return array{
     *     install: bool,
     *     theme: string,
     *     hyva_project_key: string,
     *     hyva_api_token: string
     * }
     */
    private function collectHyvaCredentials(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper,
        string $themeId
    ): array {
        $isFirstAttempt = true;

        while (true) {
            try {
                if ($isFirstAttempt) {
                    $output->writeln('');
                    $output->writeln('<info>=== Hyva Theme Credentials ===</info>');
                } else {
                    $output->writeln('');
                    $output->writeln('<info>=== Hyva Theme Credentials (Retry) ===</info>');
                }

                $output->writeln('');
                $output->writeln('<comment>ℹ️  Hyva requires API credentials from your account</comment>');
                $output->writeln('<comment>   Get your credentials at: https://www.hyva.io/hyva-theme-license.html</comment>');
                $output->writeln('');

                // Project key
                $projectKeyQuestion = new Question('? Hyva project key: ');
                $projectKeyQuestion->setValidator(function ($answer) {
                    if (empty($answer)) {
                        throw new \RuntimeException('Project key is required for Hyva installation');
                    }
                    return $answer;
                });
                $projectKey = $questionHelper->ask($input, $output, $projectKeyQuestion);

                // API token
                $output->writeln('');
                $output->writeln('<comment>ℹ️  Your API token can be found in your Hyva account</comment>');
                $output->writeln('');

                $apiTokenQuestion = new Question('? Hyva API token: ');
                $apiTokenQuestion->setValidator(function ($answer) {
                    if (empty($answer)) {
                        throw new \RuntimeException('API token is required for Hyva installation');
                    }
                    return $answer;
                });
                $apiToken = $questionHelper->ask($input, $output, $apiTokenQuestion);

                return [
                    'install' => true,
                    'theme' => $themeId,
                    'hyva_project_key' => $projectKey ?? '',
                    'hyva_api_token' => $apiToken ?? ''
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
                    throw new \RuntimeException('Hyva credentials configuration failed. Installation aborted.');
                }

                $isFirstAttempt = false;
            }
        }
    }
}
