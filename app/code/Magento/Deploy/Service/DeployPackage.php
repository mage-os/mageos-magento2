<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
namespace Magento\Deploy\Service;

use Magento\Deploy\Package\Package;
use Magento\Deploy\Package\PackageFile;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Locale\ResolverInterface as LocaleResolver;
use Magento\Framework\View\Asset\ContentProcessorException;
use Magento\Deploy\Console\InputValidator;
use Psr\Log\LoggerInterface;

/**
 * Deploy package service
 */
class DeployPackage
{
    /**
     * Worker processes used for the stylesheets of a single package.
     *
     * Kept small deliberately. Deployment already runs one process per package, so these workers
     * compete with those; measured on a 12-package install, two workers took the deploy from
     * 6.82s to 5.04s while four gave 5.16s and eight 5.33s.
     */
    private const STYLESHEET_WORKERS = 2;

    /**
     * Below this many stylesheets, forking costs more than it saves.
     */
    private const MIN_STYLESHEETS_FOR_WORKERS = 8;

    /**
     * Environment variable that keeps package file processing in a single process.
     */
    private const DISABLE_WORKERS_ENV = 'MAGE_DEPLOY_SINGLE_PROCESS';

    /**
     * Application state object
     *
     * Allows to switch between different application areas
     *
     * @var AppState
     */
    private $appState;

    /**
     * Locale resolver interface
     *
     * Check if given locale code is a valid one
     *
     * @var LocaleResolver
     */
    private $localeResolver;

    /**
     * Service for deploying static files
     *
     * @var DeployStaticFile
     */
    private $deployStaticFile;

    /**
     * Logger interface
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Total count of processed files
     *
     * @var int
     */
    private $count = 0;

    /**
     * Total count of the errors
     *
     * @var int
     */
    private $errorsCount = 0;

    /**
     * DeployPackage constructor
     *
     * @param AppState $appState
     * @param LocaleResolver $localeResolver
     * @param DeployStaticFile $deployStaticFile
     * @param LoggerInterface $logger
     */
    public function __construct(
        AppState $appState,
        LocaleResolver $localeResolver,
        DeployStaticFile $deployStaticFile,
        LoggerInterface $logger
    ) {
        $this->appState = $appState;
        $this->localeResolver = $localeResolver;
        $this->deployStaticFile = $deployStaticFile;
        $this->logger = $logger;
    }

    /**
     * Execute package deploy procedure
     *
     * @param Package $package
     * @param array $options
     * @param bool $skipLogging
     * @return bool true on success
     */
    public function deploy(Package $package, array $options, $skipLogging = false)
    {
        $result = $this->appState->emulateAreaCode(
            $package->getArea() === Package::BASE_AREA ? 'global' : $package->getArea(),
            function () use ($package, $options, $skipLogging) {
                // emulate application locale needed for correct file path resolving
                $this->localeResolver->setLocale($package->getLocale());
                $this->deployEmulated($package, $options, $skipLogging);
            }
        );
        $package->setState(Package::STATE_COMPLETED);
        return $result;
    }

    /**
     * Execute package deploy procedure when area already emulated
     *
     * @param Package $package
     * @param array $options
     * @param bool $skipLogging
     * @return bool
     */
    public function deployEmulated(Package $package, array $options, $skipLogging = false)
    {
        $this->count = 0;
        $this->errorsCount = 0;
        $this->register($package, null, $skipLogging);

        /** @var PackageFile[] $pending */
        $pending = [];
        /** @var PackageFile $file */
        foreach ($package->getFiles() as $file) {
            $fileId = $file->getDeployedFileId();
            ++$this->count;
            $this->register($package, $file, $skipLogging);
            if ($this->checkFileSkip($fileId, $options)) {
                continue;
            }
            $pending[] = $file;
        }

        // Only stylesheets are handed to workers. LESS compilation is nearly all of a package's
        // cost and each stylesheet is written independently, to a path derived from the asset.
        // Handing every file to a worker measured slower: most files are cheap copies, and
        // deployment already runs one process per package, so extra forks only add contention.
        $stylesheets = [];
        foreach ($pending as $index => $file) {
            if (pathinfo($file->getDeployedFileName(), PATHINFO_EXTENSION) === 'css') {
                $stylesheets[] = $file;
                unset($pending[$index]);
            }
        }
        $workers = $this->getStylesheetWorkerCount(count($stylesheets));
        if ($workers > 1 && $this->processFilesInParallel($stylesheets, $package, $workers)) {
            $stylesheets = [];
        }
        $pending = array_merge($stylesheets, $pending);

        foreach ($pending as $file) {
            try {
                $this->processFile($file, $package);
            } catch (ContentProcessorException $exception) {
                $errorMessage = __(
                    'Compilation from source: %1',
                    $file->getSourcePath()
                    . PHP_EOL
                    . $exception->getMessage()
                    . PHP_EOL
                );
                $this->errorsCount++;
                $this->logger->critical($errorMessage);
                $package->deleteFile($file->getFileId());
                throw new LocalizedException($errorMessage);
            } catch (\Exception $exception) {
                $this->logger->critical(
                    'Compilation from source ' . $file->getSourcePath() . ' failed' . PHP_EOL . (string)$exception
                );
                $this->errorsCount++;
            }
        }

        // execute package post-processors (may adjust content of deployed files, or produce derivative files)
        foreach ($package->getPostProcessors() as $processor) {
            $processor->process($package, $options);
        }

        return true;
    }

    /**
     * Apply proper deployment action
     *
     * File can be created if content is already provided, or copied from parent package or published
     *
     * @param PackageFile $file
     * @param Package $package
     * @return void
     */
    /**
     * How many worker processes to use for this package's stylesheets.
     *
     * @param int $stylesheetCount
     * @return int
     */
    private function getStylesheetWorkerCount($stylesheetCount)
    {
        if ($stylesheetCount < self::MIN_STYLESHEETS_FOR_WORKERS
            || !function_exists('pcntl_fork')
            || filter_var((string)getenv(self::DISABLE_WORKERS_ENV), FILTER_VALIDATE_BOOLEAN)
        ) {
            return 1;
        }

        return self::STYLESHEET_WORKERS;
    }

    /**
     * Deploy the given files across worker processes and wait for all of them.
     *
     * Files are dealt round-robin because a handful of large stylesheets dominate the cost and
     * they are scattered through the list. Falls back to the caller when forking is refused, so
     * the result is the same either way.
     *
     * @param PackageFile[] $files
     * @param Package $package
     * @param int $workers
     * @return bool Whether the files were deployed here
     * @throws LocalizedException
     */
    private function processFilesInParallel(array $files, Package $package, $workers)
    {
        $buckets = array_fill(0, $workers, []);
        foreach (array_values($files) as $index => $file) {
            $buckets[$index % $workers][] = $file;
        }

        $children = [];
        foreach ($buckets as $bucket) {
            if (!$bucket) {
                continue;
            }
            $pid = pcntl_fork();
            if ($pid === -1) {
                $this->waitForWorkers($children);
                return false;
            }
            if ($pid === 0) {
                $status = 0;
                foreach ($bucket as $file) {
                    try {
                        $this->processFile($file, $package);
                    } catch (\Exception $exception) {
                        $this->logger->critical(
                            'Compilation from source ' . $file->getSourcePath() . ' failed'
                            . PHP_EOL . (string)$exception
                        );
                        $status = 1;
                    }
                }
                // phpcs:ignore Magento2.Security.LanguageConstruct.ExitUsage
                exit($status);
            }
            $children[] = $pid;
        }

        if ($this->waitForWorkers($children) > 0) {
            throw new LocalizedException(
                __('Deployment of package %1 failed in a worker process.', $package->getPath())
            );
        }

        return true;
    }

    /**
     * Reap every worker, returning how many did not succeed.
     *
     * @param int[] $children
     * @return int
     */
    private function waitForWorkers(array $children)
    {
        $failed = 0;
        foreach ($children as $pid) {
            $status = 0;
            do {
                $result = pcntl_waitpid($pid, $status);
                // Retry when the wait itself was interrupted by a signal.
            } while ($result === -1 && pcntl_get_last_error() === PCNTL_EINTR);

            if ($result !== $pid || !pcntl_wifexited($status) || pcntl_wexitstatus($status) !== 0) {
                ++$failed;
            }
        }

        return $failed;
    }

    private function processFile(PackageFile $file, Package $package)
    {
        if ($file->getContent()) {
            $this->deployStaticFile->writeFile(
                $file->getDeployedFileName(),
                $package->getPath(),
                $file->getContent()
            );
        } else {
            $parentPackage = $package->getParent();
            if ($this->checkIfCanCopy($file, $package, $parentPackage)) {
                $this->deployStaticFile->copyFile(
                    $file->getDeployedFileId(),
                    $parentPackage->getPath(),
                    $package->getPath()
                );
            } else {
                $this->deployStaticFile->deployFile(
                    $file->getFileName(),
                    [
                        'area' => $package->getArea(),
                        'theme' => $package->getTheme(),
                        'locale' => $package->getLocale(),
                        'module' => $file->getModule(),
                    ]
                );
            }
        }
    }

    /**
     * Check if file can be copied from parent package
     *
     * @param PackageFile $file
     * @param Package $package
     * @param Package $parentPackage
     * @return bool
     */
    private function checkIfCanCopy(PackageFile $file, Package $package, ?Package $parentPackage = null)
    {
        return $parentPackage
            && $file->getOrigPackage() !== $package
            && (
                $file->getArea() !== $package->getArea()
                || $file->getTheme() !== $package->getTheme()
                || $file->getLocale() !== $package->getLocale()
            )
            && $file->getOrigPackage() === $parentPackage
            && $this->deployStaticFile->readFile($file->getDeployedFileId(), $parentPackage->getPath());
    }

    /**
     * Check if file can be deployed
     *
     * @param string $filePath
     * @param array $options
     * @return boolean
     */
    private function checkFileSkip($filePath, array $options)
    {
        if ($filePath !== '.') {
            $filePath = (string)$filePath;
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $basename = pathinfo($filePath, PATHINFO_BASENAME);
            if ($ext === 'less' && strpos($basename, '_') === 0) {
                return true;
            }
            $option = isset(InputValidator::$fileExtensionOptionMap[$ext])
                ? InputValidator::$fileExtensionOptionMap[$ext]
                : null;
            return $option ? (isset($options[$option]) ? $options[$option] : false) : false;
        }
        return false;
    }

    /**
     * Add operation to log and package info files
     *
     * @param Package $package
     * @param PackageFile|null $file
     * @param bool $skipLogging
     * @return void
     */
    private function register(Package $package, ?PackageFile $file = null, $skipLogging = false)
    {
        $info = [
            'count' => $this->count,
            'last' => $file ? $file->getSourcePath() : ''
        ];
        $this->deployStaticFile->writeTmpFile('info.json', $package->getPath(), json_encode($info));

        if (!$skipLogging) {
            $logMessage = '.';
            if ($file) {
                $logMessage = "Processing file '{$file->getSourcePath()}'";
                if ($file->getArea()) {
                    $logMessage .= "  for area '{$file->getArea()}'";
                }
                if ($file->getTheme()) {
                    $logMessage .= ", theme '{$file->getTheme()}'";
                }
                if ($file->getLocale()) {
                    $logMessage .= ", locale '{$file->getLocale()}'";
                }
                if ($file->getModule()) {
                    $logMessage .= "module '{$file->getModule()}'";
                }
            }

            $this->logger->info($logMessage);
        }
    }
}
