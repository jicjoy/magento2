<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Cybersource\Test\Unit\Gateway\Validator;

use Magento\Cybersource\Gateway\Validator\DecisionValidator;
use Magento\Cybersource\Gateway\Validator\CancelValidator;
use Magento\Payment\Gateway\Validator\Result;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class CancelValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ResultInterfaceFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    private $cancelResultFactory;

    /**
     * @var ResultInterfaceFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    private $decisionResultFactory;

    /**
     * @var DecisionValidator
     */
    private $decisionValidator;

    /**
     * @var CancelValidator
     */
    private $cancelValidator;

    protected function setUp()
    {
        $this->cancelResultFactory = $this->getMockBuilder(ResultInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->decisionResultFactory = clone $this->cancelResultFactory;
        $this->decisionValidator = new DecisionValidator($this->decisionResultFactory);
        $this->cancelValidator = new CancelValidator(
            $this->decisionValidator,
            $this->cancelResultFactory
        );
    }

    /**
     * Test validator in case of wrong response.
     */
    public function testValidateNoDecision()
    {
        $response = ['data', 'data2'];

        $this->decisionResultFactory->method('create')
            ->with([
                'isValid' => false,
                'failsDescription' => [__('Your payment has been declined. Please try again.')]
            ])
            ->willReturn(new Result(false, [__('Your payment has been declined. Please try again.')]));

        $this->cancelResultFactory->expects(static::once())
            ->method('create')
            ->with([
                'isValid' => false,
                'failsDescription' => [__('Your payment has been declined. Please try again.')]
            ])
            ->willReturn(new Result(false, [__('Your payment has been declined. Please try again.')]));

        $result = $this->cancelValidator->validate(['response' => $response]);
        static::assertFalse($result->isValid());
    }

    /**
     * Test validator for the negative decision.
     *
     * @param $response
     * @dataProvider negativeResponseDataProvider
     */
    public function testValidateNegativeDecision($response)
    {
        $this->decisionResultFactory->method('create')
            ->with([
                'isValid' => false,
                'failsDescription' => [__('Your payment has been declined. Please try again.')]
            ])
            ->willReturn(new Result(false, [__('Your payment has been declined. Please try again.')]));

        $this->cancelResultFactory->expects(static::once())
            ->method('create')
            ->with([
                'isValid' => false,
                'failsDescription' => [__('Your payment has been declined. Please try again.')]
            ])
            ->willReturn(new Result(false, [__('Your payment has been declined. Please try again.')]));

        $result = $this->cancelValidator->validate(['response' => $response]);
        static::assertFalse($result->isValid());
    }

    public function negativeResponseDataProvider()
    {
        return [
            [[DecisionValidator::DECISION => 'data', DecisionValidator::REASON_CODE => '',]],
            [
                [
                    DecisionValidator::DECISION => 'REJECT',
                    DecisionValidator::REASON_CODE => 111
                ]
            ],
        ];
    }

    /**
     * Test validator with acceptable response.
     *
     * @param array $response
     * @param bool $decisionResult
     * @param \PHPUnit_Framework_MockObject_Matcher_InvokedCount $cancelResultCall
     * @dataProvider acceptableResponseDataProvider
     */
    public function testValidate($response, $decisionResult, $cancelResultCall)
    {
        $this->decisionResultFactory->method('create')
            ->with([
                'isValid' => $decisionResult,
                'failsDescription' => [__('Your payment has been declined. Please try again.')]
            ])
            ->willReturn(new Result($decisionResult, [__('Your payment has been declined. Please try again.')]));

        $this->cancelResultFactory->expects($cancelResultCall)
            ->method('create')
            ->with([
                'isValid' => true,
                'failsDescription' => [__('Your payment has been declined. Please try again.')]
            ])
            ->willReturn(new Result(true, [__('Your payment has been declined. Please try again.')]));

        $result = $this->cancelValidator->validate(['response' => $response]);
        static::assertTrue($result->isValid());
    }

    public function acceptableResponseDataProvider()
    {
        return [
            [
                [
                    DecisionValidator::DECISION => 'ACCEPT',
                    DecisionValidator::REASON_CODE => ''
                ],
                true,
                static::never()
            ],
            [
                [
                    DecisionValidator::DECISION => 'REJECT',
                    DecisionValidator::REASON_CODE => 102
                ],
                false,
                static::once()
            ]
        ];
    }
}
