<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\I18n\Dictionary\Loader\File;

use Magento\Setup\Module\I18n\Dictionary;
use Magento\Setup\Module\I18n\Dictionary\Loader\File\AbstractFile;
use Magento\Setup\Module\I18n\Dictionary\Phrase;
use Magento\Setup\Module\I18n\Factory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AbstractFileTest extends TestCase
{
    /**
     * @var Dictionary|MockObject
     */
    protected $_dictionaryMock;

    /**
     * @var Factory|MockObject
     */
    protected $_factoryMock;

    /**
     * @var AbstractFile|MockObject
     */
    protected $_abstractLoaderMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->_dictionaryMock = $this->createMock(Dictionary::class);
        $this->_factoryMock = $this->createMock(Factory::class);
    }

    /**
     * @return void
     */
    public function testLoadWrongFile(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Cannot open dictionary file: "wrong_file.csv".');
        $abstractLoaderMock = $this->getMockForAbstractClass(
            AbstractFile::class,
            [],
            '',
            false
        );

        /** @var AbstractFile $abstractLoaderMock */
        $abstractLoaderMock->load('wrong_file.csv');
    }

    /**
     * @return void
     */
    public function testLoad(): void
    {
        $abstractLoaderMock = $this->getMockForAbstractClass(
            AbstractFile::class,
            [$this->_factoryMock],
            '',
            true,
            true,
            true,
            ['_openFile', '_readFile', '_closeFile']
        );
        $abstractLoaderMock
            ->method('_readFile')
            ->willReturnCallback(function () use (&$callCount) {
                $callCount++;
                if ($callCount === 1) {
                    return ['phrase1', 'translation1'];
                } elseif ($callCount === 2) {
                    return ['phrase2', 'translation2', 'context_type2', 'context_value2'];
                }
            });

        $phraseFirstMock = $this->createMock(Phrase::class);
        $phraseSecondMock = $this->createMock(Phrase::class);

        $this->_factoryMock->expects($this->once())
            ->method('createDictionary')
            ->willReturn($this->_dictionaryMock);
        $this->_factoryMock
            ->method('createPhrase')
            ->willReturnCallback(
                function ($args) use ($phraseFirstMock, $phraseSecondMock) {
                    if ($args == [
                            'phrase' => 'phrase1',
                            'translation' => 'translation1',
                            'context_type' => '',
                            'context_value' => ''
                        ]) {
                        return $phraseFirstMock;
                    } elseif ($args == [
                            'phrase' => 'phrase2',
                            'translation' => 'translation2',
                            'context_type' => 'context_type2',
                            'context_value' => 'context_value2'
                        ]) {
                        return $phraseSecondMock;
                    }
                }
            );

        $this->_dictionaryMock
            ->method('addPhrase')
            ->willReturnCallback(
                function ($arg) use ($phraseFirstMock, $phraseSecondMock) {
                    if ($arg == $phraseFirstMock || $arg == $phraseSecondMock) {
                        return null;
                    }
                }
            );

        /** @var AbstractFile $abstractLoaderMock */
        $this->assertEquals($this->_dictionaryMock, $abstractLoaderMock->load('test.csv'));
    }

    /**
     * @return void
     */
    public function testErrorsInPhraseCreating(): void
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Invalid row #1: "exception_message".');
        $abstractLoaderMock = $this->getMockForAbstractClass(
            AbstractFile::class,
            [$this->_factoryMock],
            '',
            true,
            true,
            true,
            ['_openFile', '_readFile']
        );
        $abstractLoaderMock
            ->method('_readFile')
            ->willReturnCallback(
                function () {
                    return ['phrase1', 'translation1'];
                }
            );

        $this->_factoryMock->expects($this->once())
            ->method('createDictionary')
            ->willReturn($this->_dictionaryMock);
        $this->_factoryMock
            ->method('createPhrase')
            ->willThrowException(new \DomainException('exception_message'));

        /** @var AbstractFile $abstractLoaderMock */
        $this->assertEquals($this->_dictionaryMock, $abstractLoaderMock->load('test.csv'));
    }
}
