<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Controller\Returns;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;

/**
 * Controller class AddComment. Represents routines and logic for addComment action.
 */
class AddComment extends \Magento\Rma\Controller\Returns
{
    /**
     * Add RMA comment action
     *
     * @return void|\Magento\Framework\Controller\Result\Redirect
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
            $redirect->setPath('*/*/view', ['entity_id' => (int) $this->getRequest()->getParam('entity_id')]);

            return $redirect;
        }

        if (!$this->_loadValidRma()) {
            return;
        }
        try {
            $comment = $this->getRequest()->getPost('comment');
            $comment = trim(strip_tags($comment));
            if (empty($comment)) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a valid message.'));
            }
            /** @var $statusHistory \Magento\Rma\Model\Rma\Status\History */
            $statusHistory = $this->_objectManager->create(\Magento\Rma\Model\Rma\Status\History::class);
            $rma = $this->_coreRegistry->registry('current_rma');
            $statusHistory->setRmaEntityId($rma->getId());
            $statusHistory->setComment($comment);
            $statusHistory->sendCustomerCommentEmail();
            $statusHistory->saveComment($comment, true, false);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Cannot add message.'));
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/view', ['entity_id' => (int) $this->getRequest()->getParam('entity_id')]);
    }
}
