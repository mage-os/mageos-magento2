<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Code\Scanner;

use Magento\Setup\Module\Di\Code\Scanner\ArrayScanner;
use PHPUnit\Framework\TestCase;

class ArrayScannerTest extends TestCase
{
    /**
     * @var ArrayScanner
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_testDir;

    protected function setUp(): void
    {
        $this->_model = new ArrayScanner();
        $this->_testDir = str_replace('\\', '/', realpath(__DIR__ . '/../../') . '/_files');
    }

    public function testCollectEntities()
    {
        $actual = $this->_model->collectEntities([$this->_testDir . '/additional.php']);
        $expected = ['Some_Model_Proxy', 'Some_Model_EntityFactory'];
        $this->assertEquals($expected, $actual);
    }

    /**
     * A file living under a runtime-writable report directory must never be
     * include()d, even if a caller passes its path. Proven both by the empty
     * return value and by the absence of a sentinel that the file would create
     * if it were ever executed as PHP.
     */
    public function testCollectEntitiesSkipsReportDirectoryAndDoesNotExecute()
    {
        $base = sys_get_temp_dir() . '/mageos_di_guard_' . uniqid();
        $blockedDirs = [$base . '/var/report', $base . '/var/page_cache'];
        $sentinel = $base . '/pwned';
        $maliciousFiles = [];
        foreach ($blockedDirs as $dir) {
            mkdir($dir, 0777, true);
            $maliciousFiles[] = $dir . '/deadbeef.php';
            file_put_contents(
                $dir . '/deadbeef.php',
                "<?php file_put_contents('" . $sentinel . "', 'x'); return ['X'];"
            );
        }

        // The guard is anchored to the application root, so the scanner is given this fixture root.
        $model = new ArrayScanner($base);

        try {
            $actual = $model->collectEntities($maliciousFiles);
            $this->assertSame([], $actual, 'Files under runtime-writable directories must be skipped');
            $this->assertFileDoesNotExist(
                $sentinel,
                'The guarded file must never be executed as PHP'
            );
        } finally {
            foreach ($maliciousFiles as $file) {
                $this->removeFile($file);
            }
            $this->removeFile($sentinel);
            foreach ($blockedDirs as $dir) {
                $this->removeDir($dir);
            }
            $this->removeDir($base . '/var');
            $this->removeDir($base);
        }
    }

    /**
     * A blocked segment that is not anchored at the scanner's root must not reject legitimate
     * code, e.g. an install whose own path contains "/var/tmp/".
     */
    public function testCollectEntitiesIncludesCodeUnderRootWhosePathContainsBlockedSegment()
    {
        $base = sys_get_temp_dir() . '/var/tmp/mageos_di_guard_' . uniqid();
        $codeDir = $base . '/app/code';
        mkdir($codeDir, 0777, true);
        $file = $codeDir . '/additional.php';
        file_put_contents($file, "<?php return ['Some_Model_Proxy'];");

        $model = new ArrayScanner($base);

        try {
            $this->assertSame(['Some_Model_Proxy'], $model->collectEntities([$file]));
        } finally {
            $this->removeFile($file);
            $this->removeDir($codeDir);
            $this->removeDir($base . '/app');
            $this->removeDir($base);
            $this->removeDir(sys_get_temp_dir() . '/var/tmp');
            $this->removeDir(sys_get_temp_dir() . '/var');
        }
    }

    /**
     * Empty paths, non-existent files, and existing non-PHP files are all
     * rejected by the include guard and yield no collected entities.
     */
    public function testCollectEntitiesSkipsNonPhpAndTraversalPaths()
    {
        $this->assertSame([], $this->_model->collectEntities(['']));
        $this->assertSame(
            [],
            $this->_model->collectEntities([sys_get_temp_dir() . '/does_not_exist_' . uniqid() . '.php'])
        );

        $nonPhpFile = sys_get_temp_dir() . '/mageos_di_guard_nonphp_' . uniqid() . '.txt';
        file_put_contents($nonPhpFile, "<?php return ['Y'];");
        try {
            $this->assertSame([], $this->_model->collectEntities([$nonPhpFile]));
        } finally {
            $this->removeFile($nonPhpFile);
        }
    }

    /**
     * @param string $file
     * @return void
     */
    private function removeFile(string $file): void
    {
        if (is_file($file)) {
            unlink($file);
        }
    }

    /**
     * @param string $dir
     * @return void
     */
    private function removeDir(string $dir): void
    {
        if (is_dir($dir)) {
            rmdir($dir);
        }
    }
}
