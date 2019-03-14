<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Rma\Controller\Returns;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;

class Create extends \Magento\Rma\Controller\Returns
{
    /**
     * Try to load valid collection of ordered items
     *
     * @param int $orderId
     * @return bool
     */
    protected function _loadOrderItems($orderId)
    {
        /** @var $rmaHelper \Magento\Rma\Helper\Data */
        $rmaHelper = $this->_objectManager->get(\Magento\Rma\Helper\Data::class);
        if ($rmaHelper->canCreateRma($orderId)) {
            return true;
        }

        $incrementId = $this->_coreRegistry->registry('current_order')->getIncrementId();
        $message = __('We can\'t create a return transaction for order #%1.', $incrementId);
        $this->messageManager->addError($message);
        $this->_redirect('sales/order/history');
        return false;
    }

    /**
     * Customer create new return
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $orderId = (int)$this->getRequest()->getParam('order_id');
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_objectManager->create(\Magento\Sales\Model\Order::class)->load($orderId);
        if (empty($orderId)) {
            $this->_redirect('sales/order/history');
            return;
        }
        $this->_coreRegistry->register('current_order', $order);

        if (!$this->_loadOrderItems($orderId)) {
            return;
        }

        /** @var \Magento\Framework\Stdlib\DateTime\DateTime $coreDate */
        $coreDate = $this->_objectManager->get(\Magento\Framework\Stdlib\DateTime\DateTime::class);
        if (!$this->_canViewOrder($order)) {
            $this->_redirect('sales/order/history');
            return;
        }
        $post = $this->getRequest()->getPostValue();
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
                if (!$rmaModel->setData($rmaData)->saveRma($post)) {
                    $url = $this->_url->getUrl('*/*/create', ['order_id' => $orderId]);
                    $this->getResponse()->setRedirect($this->_redirect->error($url));
                    return;
                }
                /** @var $statusHistory \Magento\Rma\Model\Rma\Status\History */
                $statusHistory = $this->_objectManager->create(\Magento\Rma\Model\Rma\Status\History::class);
                $statusHistory->setRmaEntityId($rmaModel->getEntityId());
                $statusHistory->sendNewRmaEmail();
                $statusHistory->saveSystemComment();
                if (isset($post['rma_comment']) && !empty($post['rma_comment'])) {
                    $comment = $this->_objectManager->create(\Magento\Rma\Model\Rma\Status\History::class);
                    $comment->setRmaEntityId($rmaModel->getEntityId());
                    $comment->saveComment($post['rma_comment'], true, false);
                }
                $this->messageManager->addSuccess(__('You submitted Return #%1.', $rmaModel->getIncrementId()));
                $this->getResponse()->setRedirect($this->_redirect->success($this->_url->getUrl('*/*/history')));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t create a return right now. Please try again later.')
                );
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            }
        }
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Create New Return'));
        if ($block = $this->_view->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->renderLayout();
    }
}
