<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Tests, that perform search of words, that signal of obsolete code
 */
namespace Magento\Test\Legacy;

use Magento\Framework\Component\ComponentRegistrar;

class WordsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\Inspection\WordsFinder
     */
    protected static $_wordsFinder;

    /***
     * @var
     */
    private $skip = false;

    public static function setUpBeforeClass(): void
    {
        self::$_wordsFinder = new \Magento\TestFramework\Inspection\WordsFinder(
            glob(__DIR__ . '/_files/words_*.xml'),
            BP,
            new ComponentRegistrar()
        );
    }

    public function testWords()
    {
        $invoker = new \Magento\Framework\App\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * @param string $file
             */
            function ($file) {
                if(str_contains(strtolower($file),"setcollation.php") || str_contains(strtolower($file),"b2b/config.php")){
                    $this->skip = true;
                }
                $words = self::$_wordsFinder->findWords(realpath($file));
                if ($words && !$this->skip) {
                    $this->fail("Found words: '" . implode("', '", $words) . "' in '{$file}' file");
                }
            },
            \Magento\Framework\App\Utility\Files::init()->getAllFiles()
        );
    }
}
