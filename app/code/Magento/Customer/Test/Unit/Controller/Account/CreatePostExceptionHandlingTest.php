<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Controller\Account;

use Magento\Customer\Controller\Account\CreatePost;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\AbstractMessage;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Validator\Exception as ValidatorException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * Test class for CreatePost exception handling methods
 */
class CreatePostExceptionHandlingTest extends TestCase
{
    /**
     * @var CreatePost|MockObject
     */
    private $controller;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManagerMock;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->messageManagerMock = $this->createMock(ManagerInterface::class);

        $this->controller = $this->getMockBuilder(CreatePost::class)
            ->disableOriginalConstructor()
            ->getMock();

        $reflection = new ReflectionClass($this->controller);
        $messageManagerProperty = $reflection->getProperty('messageManager');
        $messageManagerProperty->setValue($this->controller, $this->messageManagerMock);
    }

    /**
     * Test processValidatorException with multiple messages
     */
    public function testProcessValidatorExceptionWithMessages(): void
    {
        $errorMessage1 = $this->createMock(AbstractMessage::class);
        $errorMessage1->expects($this->once())
            ->method('getText')
            ->willReturn('First Name is not valid!');

        $errorMessage2 = $this->createMock(AbstractMessage::class);
        $errorMessage2->expects($this->once())
            ->method('getText')
            ->willReturn('Last Name is not valid!');

        $validatorException = $this->createMock(ValidatorException::class);
        $validatorException->expects($this->once())
            ->method('getMessages')
            ->willReturn([$errorMessage1, $errorMessage2]);

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($this->callback(function ($message) {
                return is_string($message) &&
                    (str_contains($message, 'First Name is not valid!') ||
                        str_contains($message, 'Der Vorname ist ungültig!')) &&
                    (str_contains($message, 'Last Name is not valid!') ||
                        str_contains($message, 'Der Nachname ist ungültig!'));
            }));

        $this->invokePrivateMethod('processValidatorException', [$validatorException]);
    }

    /**
     * Test processValidatorException with empty messages (fallback)
     */
    public function testProcessValidatorExceptionWithEmptyMessages(): void
    {
        $validatorException = $this->getMockBuilder(ValidatorException::class)
            ->setConstructorArgs([new Phrase('Combined error message')])
            ->onlyMethods(['getMessages'])
            ->getMock();
        $validatorException->expects($this->once())
            ->method('getMessages')
            ->willReturn([]);

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($this->isType('string'));

        $this->invokePrivateMethod('processValidatorException', [$validatorException]);
    }

    /**
     * Test processValidatorException with string messages (not AbstractMessage)
     */
    public function testProcessValidatorExceptionWithStringMessages(): void
    {
        $validatorException = $this->createMock(ValidatorException::class);
        $validatorException->expects($this->once())
            ->method('getMessages')
            ->willReturn(['First Name is not valid!', 'Last Name is not valid!']);

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($this->isType('string'));

        $this->invokePrivateMethod('processValidatorException', [$validatorException]);
    }

    /**
     * Test processStandardInputException with errors
     */
    public function testProcessStandardInputExceptionWithErrors(): void
    {
        $error1 = $this->getMockBuilder(LocalizedException::class)
            ->setConstructorArgs([new Phrase('Error 1')])
            ->getMock();

        $error2 = $this->getMockBuilder(LocalizedException::class)
            ->setConstructorArgs([new Phrase('Error 2')])
            ->getMock();

        $inputException = $this->getMockBuilder(InputException::class)
            ->setConstructorArgs([new Phrase('Main message')])
            ->onlyMethods(['getErrors'])
            ->getMock();
        $inputException->expects($this->once())
            ->method('getErrors')
            ->willReturn([$error1, $error2]);

        $this->messageManagerMock->expects($this->exactly(2))
            ->method('addErrorMessage')
            ->with($this->isType('string'));

        $this->invokePrivateMethod('processStandardInputException', [$inputException]);
    }

    /**
     * Test processStandardInputException without errors
     */
    public function testProcessStandardInputExceptionWithoutErrors(): void
    {
        $inputException = $this->getMockBuilder(InputException::class)
            ->setConstructorArgs([new Phrase('Main error message')])
            ->onlyMethods(['getErrors'])
            ->getMock();
        $inputException->expects($this->once())
            ->method('getErrors')
            ->willReturn([]);

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($this->isType('string'));

        $this->invokePrivateMethod('processStandardInputException', [$inputException]);
    }

    /**
     * Test processInputException routes to ValidatorException handler
     */
    public function testProcessInputExceptionRoutesToValidatorException(): void
    {
        $validatorException = $this->getMockBuilder(ValidatorException::class)
            ->setConstructorArgs([new Phrase('Fallback message')])
            ->onlyMethods(['getMessages'])
            ->getMock();
        $validatorException->expects($this->once())
            ->method('getMessages')
            ->willReturn([]);

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($this->isType('string'));

        $this->invokePrivateMethod('processInputException', [$validatorException]);
    }

    /**
     * Test processInputException routes to standard InputException handler
     */
    public function testProcessInputExceptionRoutesToStandardInputException(): void
    {
        $inputException = $this->getMockBuilder(InputException::class)
            ->setConstructorArgs([new Phrase('Standard error')])
            ->onlyMethods(['getErrors'])
            ->getMock();
        $inputException->expects($this->once())
            ->method('getErrors')
            ->willReturn([]);

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($this->isType('string'));

        $this->invokePrivateMethod('processInputException', [$inputException]);
    }

    /**
     * Invoke a private method using reflection
     *
     * @param string $methodName
     * @param array $args
     * @return mixed
     */
    private function invokePrivateMethod(string $methodName, array $args)
    {
        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod($methodName);
        return $method->invokeArgs($this->controller, $args);
    }
}
