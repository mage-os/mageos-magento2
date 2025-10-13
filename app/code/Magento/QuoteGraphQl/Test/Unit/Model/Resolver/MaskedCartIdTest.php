<?php
/**
 * Copyright 2022 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\QuoteGraphQl\Test\Unit\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\Context;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\QuoteIdToMaskedQuoteIdInterface;
use Magento\Quote\Model\ResourceModel\Quote\QuoteIdMask as QuoteIdMaskResourceModel;
use Magento\QuoteGraphQl\Model\Resolver\MaskedCartId;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for MaskedCartId resolver
 */
class MaskedCartIdTest extends TestCase
{
    /**
     * @var MaskedCartId
     */
    private MaskedCartId $maskedCartId;

    /**
     * @var QuoteIdToMaskedQuoteIdInterface|MockObject
     */
    private QuoteIdToMaskedQuoteIdInterface $quoteIdToMaskedQuoteId;

    /**
     * @var \Magento\QuoteGraphQl\Test\Unit\Model\Resolver\QuoteIdMaskFactory|MockObject
     */
    private QuoteIdMaskFactory $quoteIdMaskFactory;

    /**
     * @var QuoteIdMaskResourceModel|MockObject
     */
    private QuoteIdMaskResourceModel $quoteIdMaskResourceModelMock;

    /**
     * @var Field|MockObject
     */
    private Field $fieldMock;

    /**
     * @var ResolveInfo|MockObject
     */
    private ResolveInfo $resolveInfoMock;

    /**
     * @var Context|MockObject
     */
    private Context $contextMock;

    /**
     * @var Quote|MockObject
     */
    private Quote $quoteMock;

    /**
     * @var QuoteIdMask|MockObject
     */
    private QuoteIdMask $quoteIdMask;

    /**
     * @var array
     */
    private array $valueMock = [];

    /**
     * Set up test dependencies.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->fieldMock = $this->createMock(Field::class);
        $this->resolveInfoMock = $this->createMock(ResolveInfo::class);
        $this->contextMock = $this->createMock(Context::class);
        $this->quoteIdToMaskedQuoteId = $this->createMock(QuoteIdToMaskedQuoteIdInterface::class);
        $this->quoteIdMaskFactory = $this->createMock(QuoteIdMaskFactory::class);
        $this->quoteIdMaskResourceModelMock = $this->createMock(QuoteIdMaskResourceModel::class);
        $this->maskedCartId = new MaskedCartId(
            $this->quoteIdToMaskedQuoteId,
            $this->quoteIdMaskFactory,
            $this->quoteIdMaskResourceModelMock
        );
        $this->quoteMock = $this->createMock(Quote::class);
        $this->quoteIdMask = $this->createMock(QuoteIdMask::class);
    }

    /**
     * Ensure exception thrown when required model is missing from value.
     *
     * @return void
     */
    public function testResolveWithoutModelInValueParameter(): void
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('"model" value should be specified');
        $this->maskedCartId->resolve($this->fieldMock, $this->contextMock, $this->resolveInfoMock, $this->valueMock);
    }

    /**
     * Ensure resolver sets quote id on created quoteIdMask entity.
     *
     * @return void
     */
    public function testResolve(): void
    {
        $this->valueMock = ['model' => $this->quoteMock];
        $cartId = 1;
        $this->quoteMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn($cartId);
        $this->quoteIdMaskFactory->expects($this->once())->method('create')->willReturn($this->quoteIdMask);
        $this->quoteIdMask->setQuoteId($cartId);
        $this->maskedCartId->resolve($this->fieldMock, $this->contextMock, $this->resolveInfoMock, $this->valueMock);
    }

    /**
     * @return void
     * @throws \Exception
     */
    /**
     * Ensure resolver rethrows proper message when quote not found.
     *
     * @return void
     * @throws \Exception
     */
    public function testResolveForExceptionWhenQuoteNotExists(): void
    {
        $this->expectExceptionMessage('Current user does not have an active cart.');
        $this->valueMock = ['model' => $this->quoteMock];
        $cartId = 0;
        $this->quoteIdToMaskedQuoteId->method('execute')->with($cartId)->willThrowException(
            new NoSuchEntityException(
                __(
                    'No such entity with %fieldName = %fieldValue',
                    [
                        'fieldName' => 'quoteId',
                        'fieldValue' => $cartId
                    ]
                )
            )
        );
        $this->maskedCartId->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->resolveInfoMock,
            $this->valueMock
        );
    }
}
