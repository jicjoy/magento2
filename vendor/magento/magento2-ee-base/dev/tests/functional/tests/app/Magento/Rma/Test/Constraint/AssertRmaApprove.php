<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Rma\Test\Constraint;

use Magento\Customer\Test\Fixture\Customer;
use Magento\Rma\Test\Fixture\Rma;
use Magento\Rma\Test\Page\Adminhtml\RmaIndex;
use Magento\Rma\Test\Page\Adminhtml\RmaView;
use Magento\Sales\Test\Fixture\OrderInjectable;

/**
 * Assert that rma items can be approved.
 */
class AssertRmaApprove extends AbstractAssertRmaOnBackend
{
    /**
     * Assert that rma items can be approved and data is saved correctly.
     *
     * @param Rma $rma
     * @param array $approve
     * @param RmaIndex $rmaIndex
     * @param RmaView $rmaView
     * @return void
     */
    public function processAssert(Rma $rma, array $approve, RmaIndex $rmaIndex, RmaView $rmaView)
    {
        /** @var OrderInjectable $order */
        $order = $rma->getDataFieldConfig('order_id')['source']->getOrder();
        $orderItems = $order->getEntityId()['products'];

        $this->open($rma, $rmaIndex);
        $rmaView->getRmaForm()->openTab('items');
        foreach ($approve as $key => $data) {
            $data['product'] = $orderItems[$key]->getName();
            $approve[$key] = $data;
        }
        $rmaView->getRmaForm()->getTab('items')->setFieldsData(['items' => ['value' => $approve]]);
        $rmaView->getPageActions()->saveAndContinue();

        $pageMessage = $rmaIndex->getMessagesBlock()->getSuccessMessage();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
            $pageMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_SAVE_MESSAGE
            . "\nActual: " . $pageMessage
        );

        $rmaView->getRmaForm()->openTab('items');
        $pageItems = $rmaView->getRmaForm()->getTab('items')->getFieldsData()['items'];
        $this->verifyItems($approve, $pageItems);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Approve information of rma items on edit page equals initial data.';
    }
}
