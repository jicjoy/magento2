<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Controller\Guest;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;

class Create extends \Magento\Rma\Controller\Guest
{
    /**
     * Try to load valid collection of ordered items
     *
     * @param int $orderId
     * @return bool
     */
    protected function _loadOrderItems($orderId)
    {
        if ($this->rmaHelper->canCreateRma($orderId)) {
            return true;
        }

        $incrementId = $this->_coreRegistry->registry('current_order')->getIncrementId();
        $message = __('We can\'t create a return transaction for order #%1.', $incrementId);
        $this->messageManager->addError($message);
        return false;
    }

    /**
     * Customer create new return
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $result = $this->salesGuestHelper->loadValidOrder($this->_request);
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }
        $order = $this->_coreRegistry->registry('current_order');
        $orderId = $order->getId();
        if (!$this->_loadOrderItems($orderId)) {
            return $this->resultRedirectFactory->create()->setPath('sales/order/history');
        }

        $post = $this->getRequest()->getPostValue();
        /** @var \Magento\Framework\Stdlib\DateTime\DateTime $coreDate */
        $coreDate = $this->_objectManager->get(\Magento\Framework\Stdlib\DateTime\DateTime::class);
        if ($post && !empty($post['items'])) {
            if (!$this->getRequest()->isPost()) {
                throw new NotFoundException(__('Page not found.'));
            }

            if (!$this->formKeyValidator->validate($this->getRequest())) {
                $this->messageManager->addErrorMessage(__('Invalid Form Key. Please refresh the page.'));

                /** @var \Magento\Framework\Controller\Result\Redirect $redirect */
                $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $redirect->setPath('*/*/create', ['order_id' => $orderId]);
                return $redirect;
            }
            try {
                /** @var $rmaModel \Magento\Rma\Model\Rma */
                $rmaModel = $this->_objectManager->create(\Magento\Rma\Model\Rma::class);
                $rmaData = [
                    'status' => \Magento\Rma\Model\Rma\Source\Status::STATE_PENDING,
                    'date_requested' => $coreDate->gmtDate(),
                    'order_id' => $order->getId(),
                    'order_increment_id' => $order->getIncrementId(),
                    'store_id' => $order->getStoreId(),
                    'customer_id' => $order->getCustomerId(),
                    'order_date' => $order->getCreatedAt(),
                    'customer_name' => $order->getCustomerName(),
                    'customer_custom_email' => $post['customer_custom_email'],
                ];
                $result = $rmaModel->setData($rmaData)->saveRma($post);

                if (!$result) {
                    return $this->resultRedirectFactory->create()->setPath('*/*/create', ['order_id' => $orderId]);
                }
                /** @var $statusHistory \Magento\Rma\Model\Rma\Status\History */
                $statusHistory = $this->_objectManager->create(\Magento\Rma\Model\Rma\Status\History::class);
                $statusHistory->setRmaEntityId($result->getId());
                $statusHistory->sendNewRmaEmail();
                $statusHistory->saveSystemComment();
                if (isset($post['rma_comment']) && !empty($post['rma_comment'])) {
                    /** @var $statusHistory \Magento\Rma\Model\Rma\Status\History */
                    $comment = $this->_objectManager->create(\Magento\Rma\Model\Rma\Status\History::class);
                    $comment->setRmaEntityId($result->getId());
                    $comment->saveComment($post['rma_comment'], true, false);
                }
                $this->messageManager->addSuccess(__('You submitted Return #%1.', $rmaModel->getIncrementId()));
                $url = $this->_url->getUrl('*/*/returns');
                return $this->resultRedirectFactory->create()->setUrl($this->_redirect->success($url));
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t create a return right now. Please try again later.')
                );
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            }
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Create New Return'));
        if ($block = $resultPage->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        return $resultPage;
    }
}
