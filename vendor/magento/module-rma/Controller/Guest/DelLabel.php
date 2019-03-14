<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Controller\Guest;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;

class DelLabel extends \Magento\Rma\Controller\Guest
{
    /**
     * Delete Tracking Number action
     *
     * @return \Magento\Framework\View\Result\Layout
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            throw new NotFoundException(__('Page not found.'));
        }

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage(__('Invalid Form Key. Please refresh the page.'));

            /** @var \Magento\Framework\Controller\Result\Redirect $redirect */
            $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $redirect->setPath('*/*/view', ['entity_id' => (int)$this->getRequest()->getParam('entity_id')]);

            return $redirect;
        }

        if ($this->_loadValidRma()) {
            try {
                $rma = $this->_coreRegistry->registry('current_rma');

                if (!$rma->isAvailableForPrintLabel()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Shipping Labels are not allowed.'));
                }

                $response = false;
                $number = intval($this->getRequest()->getPost('number'));

                if (empty($number)) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Please enter a valid tracking number.')
                    );
                }
                /** @var $trackingNumber \Magento\Rma\Model\Shipping */
                $trackingNumber = $this->_objectManager->create(\Magento\Rma\Model\Shipping::class)->load($number);
                if ($trackingNumber->getRmaEntityId() !== $rma->getId()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('You selected the wrong RMA.'));
                }
                $trackingNumber->delete();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $response = ['error' => true, 'message' => $e->getMessage()];
            } catch (\Exception $e) {
                $response = ['error' => true, 'message' => __('We can\'t delete the label right now.')];
            }
        } else {
            $response = ['error' => true, 'message' => __('You selected the wrong RMA.')];
        }
        if (is_array($response)) {
            $this->_objectManager->get(
                \Magento\Framework\Session\Generic::class
            )->setErrorMessage($response['message']);
        }

        return $this->resultLayoutFactory->create();
    }
}
