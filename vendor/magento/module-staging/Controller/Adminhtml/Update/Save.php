<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Staging\Controller\Adminhtml\Update;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Staging\Api\Data\UpdateInterface;
use Magento\Staging\Model\ResourceModel\Db\CampaignValidator;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Staging::staging';

    /**
     * @var \Magento\Staging\Api\UpdateRepositoryInterface
     */
    protected $updateRepository;

    /**
     * @var \Magento\Staging\Model\UpdateFactory
     */
    protected $updateFactory;

    /**
     * @var CampaignValidator
     */
    private $campaignValidator;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Staging\Api\UpdateRepositoryInterface $updateRepository
     * @param \Magento\Staging\Model\UpdateFactory $updateFactory
     * @param CampaignValidator $campaignValidator
     * @param Escaper|null $escaper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Staging\Api\UpdateRepositoryInterface $updateRepository,
        \Magento\Staging\Model\UpdateFactory $updateFactory,
        CampaignValidator $campaignValidator = null,
        Escaper $escaper = null
    ) {
        $this->updateRepository = $updateRepository;
        $this->updateFactory = $updateFactory;
        $this->campaignValidator = $campaignValidator ?? ObjectManager::getInstance()->get(CampaignValidator::class);
        $this->escaper = $escaper ?? ObjectManager::getInstance()->get(Escaper::class);
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Staging::staging');
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        try {
            $updateData = $this->getRequest()->getParam('general');

            if (!isset($updateData['id']) || empty($updateData['id'])) {
                $update = $this->updateFactory->create();
                $update->setIsCampaign(true);
            } else {
                /** @var UpdateInterface $update */
                $update = $this->updateRepository->get($updateData['id']);

                if (strtotime($update->getStartTime()) < time()
                    && strtotime($updateData['start_time']) !== strtotime($update->getStartTime())
                ) {
                    $this->messageManager->addErrorMessage(
                        __(
                            "The Start Time of this Update cannot be changed. It's been already started."
                        )
                    );
                    return $this->resultRedirectFactory->create()->setPath('*/*/');
                }

                if (!$this->campaignValidator->canBeUpdated($update, strtotime($updateData['end_time']))) {
                    $this->messageManager->addErrorMessage(
                        __(
                            "Future Update in this time range already exists. 
                            Select a different range to add a new Future Update."
                        )
                    );
                    return $this->resultRedirectFactory->create()->setPath('*/*/');
                }
            }
            /** @var \Magento\Staging\Model\Update $update */
            $update->setData($updateData);
            $this->updateRepository->save($update);

            $this->messageManager->addSuccessMessage(
                sprintf('You saved the "%s" update.', $this->escaper->escapeHtml($update->getName()))
            );
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Cannot save Update."));

            if (isset($update) && $update->getId()) {
                return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['id' => $update->getId()]);
            }

            return $this->resultRedirectFactory->create()->setPath('*/*/edit');
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
