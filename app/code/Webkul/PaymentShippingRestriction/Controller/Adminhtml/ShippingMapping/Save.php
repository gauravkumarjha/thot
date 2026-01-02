<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PaymentShippingRestriction
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\PaymentShippingRestriction\Controller\Adminhtml\ShippingMapping;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Webkul PaymentShippingRestriction Adminhtml ShippingMapping Save Controller.
 */
class Save extends Action
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface $messageManager
     */
    protected $messageManager;

    /**
     * @var   \Webkul\PaymentShippingRestriction\Model\ShippingMappingFactory
     */
    protected $shippingMapping;

   /**
    * @param Context $context
    * @param PageFactory $resultPageFactory
    * @param \Magento\Framework\Message\ManagerInterface $messageManager
    * @param \Webkul\PaymentShippingRestriction\Model\ShippingMappingFactory $shippingMapping
    */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Webkul\PaymentShippingRestriction\Model\ShippingMappingFactory $shippingMapping
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $messageManager;
        $this->shippingMapping = $shippingMapping;
    }

    /**
     * PaymentShippingRestriction ShippingMapping Save action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     *
     * @throws \Magento\Framework\Validator\Exception|\Exception
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->getRequest()->getParams()) {
            $this->messageManager->addError(__("Something went wrong"));
            return $resultRedirect->setPath('*/*/');
        }
        try {
            $data = $this->getRequest()->getParams();
            if (empty($data['shipping_code'])) {
                $this->messageManager->addError(__("Shipping Method is Required"));
                return $resultRedirect->setPath('*/*/');
            }
            if (empty($data['payment_code'])) {
                $this->messageManager->addError(__("Please Select at least one payment method"));
                return $resultRedirect->setPath('*/*/');
            }
            if (array_key_exists('entity_id', $data)) {
                $recordData = $this->shippingMapping->create()->load($data['entity_id']);
                if (!empty($recordData)) {
                    if ($recordData->getShippingCode() == $data['shipping_code']) {
                        $this->deleteOldMappings($data['shipping_code']);
                        $this->saveMappingDataToTable($data);
                    } else {
                        if ($this->checkRecordMappingExist($data['shipping_code'])) {
                            $this->messageManager->addError(__("Mapping Already exist"));
                            return $resultRedirect->setPath('*/*/');
                        }
                        $this->deleteOldMappings($recordData->getShippingCode());
                        $this->saveMappingDataToTable($data);
                    }
                }
            } else {
                $mappingCollection = $this->shippingMapping->create()->getCollection()->
                addFieldToFilter('shipping_code', ['eq'=> $data['shipping_code']]);
                if (!empty($mappingCollection->getData())) {
                    $this->messageManager->addError(__("Mapping Already exist"));
                    return $resultRedirect->setPath('*/*/');
                }
                $this->saveMappingDataToTable($data);
            }
            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            return $resultRedirect->setPath('*/*/');
        }
    }

    /**
     * delete old mapping
     *
     * @param string $shippingCode
     * @return void
     */
    private function deleteOldMappings($shippingCode)
    {
        if (!empty($shippingCode)) {
            $mappingCollection = $this->shippingMapping->create()->getCollection()->
            addFieldToFilter('shipping_code', ['eq'=> $shippingCode]);
            if (!empty($mappingCollection)) {
                $mappingCollection->walk('delete');
            }
        }
    }

    /**
     * check whether mapping exist or not
     *
     * @param string $shippingCode
     * @return void
     */
    private function checkRecordMappingExist($shippingCode)
    {
        if (!empty($shippingCode)) {
            $mappingCollection = $this->shippingMapping->create()->getCollection()->
            addFieldToFilter('shipping_code', ['eq'=> $shippingCode]);
            if (!empty($mappingCollection->getData())) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * save mapping datato database
     *
     * @param array $data
     * @return void
     */
    public function saveMappingDataToTable($data)
    {
        try {
            $mappingCollection = $this->shippingMapping->create();
            foreach ($data['payment_code'] as $paymentMethods) {
                $bulkInsert[] = [
                  'shipping_code' => $data['shipping_code'],
                  'payment_code' => $paymentMethods
                ];
            }
            $mappingCollection->insertMultiple($bulkInsert, 'payment_shipping_mapping');
            $mappingCollection->save();
            $this->messageManager->addSuccess(__("The Mapping has been saved successfully"));
            return true;
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            return $resultRedirect->setPath('*/*/');
        }
    }
}
