<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Cybersource\Test\Unit\Gateway\Response\SilentOrder;

use Magento\Cybersource\Gateway\Response\SilentOrder\VaultDetailsHandler;
use Magento\Cybersource\Gateway\Vault\PaymentTokenManagement;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class VaultDetailsHandlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PaymentTokenManagement|MockObject
     */
    private $paymentTokenManagement;

    /**
     * @var PaymentDataObjectInterface|MockObject
     */
    private $paymentDO;

    /**
     * @var PaymentTokenInterface|MockObject
     */
    private $paymentToken;

    /**
     * @var VaultDetailsHandler
     */
    private $handler;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->paymentTokenManagement = $this->getMockBuilder(PaymentTokenManagement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->paymentDO = $this->getMockBuilder(PaymentDataObjectInterface::class)
            ->getMock();
        $payment = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->paymentDO->method('getPayment')
            ->willReturn($payment);

        $extAttributes = $this->getMockBuilder(OrderPaymentExtensionInterface::class)
            ->setMethods(['getVaultPaymentToken'])
            ->getMock();

        $this->paymentToken = $this->getMockBuilder(PaymentTokenInterface::class)
            ->getMockForAbstractClass();
        $extAttributes->method('getVaultPaymentToken')
            ->willReturn($this->paymentToken);
        $payment->method('getExtensionAttributes')
            ->willReturn($extAttributes);

        $this->handler = new VaultDetailsHandler($this->paymentTokenManagement, new SubjectReader());
    }

    /**
     * Checks a case when Vault Payment Token can be updated by transaction details.
     */
    public function testHandle()
    {
        $card = '1111';
        $expMonth = 12;
        $expYear = 2019;
        $type = '001';
        $response = [
            'req_card_expiry_date' => '12-2019',
            'req_card_number' => 'xxxxxxxxxxxx1111',
            'req_card_type' => $type
        ];

        $this->paymentTokenManagement->method('update')
            ->with(
                $this->paymentToken,
                $card,
                $type,
                $expMonth,
                $expYear
            );

        $this->handler->handle(['payment' => $this->paymentDO], $response);
    }
}
