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

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\PaymentShippingRestriction\Model\ResourceModel\ShippingMapping\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Webkul\PaymentShippingRestriction\Model\ShippingMappingFactory
     */
    protected $shippingMappingFactory;

   /**
    * @param Context $context
    * @param Filter $filter
    * @param CollectionFactory $collectionFactory
    * @param \Webkul\PaymentShippingRestriction\Model\ShippingMappingFactory $shippingMappingFactory
    */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Webkul\PaymentShippingRestriction\Model\ShippingMappingFactory $shippingMappingFactory
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->shippingMappingFactory = $shippingMappingFactory;
        parent::__construct($context);
    }

    /**
     * checks allowed role
     *
     * @return void
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_PaymentShippingRestriction::shippingmapping');
    }

    /**
     * execute
     *
     * @return void
     */
    public function execute()
    {
        $mappingIds = [];
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        foreach ($collection as $mapping) {
            $shippingCode = $mapping->getShippingCode();
            $this->deleteAllRecordsWithShippingCode($shippingCode);
        }
        $this->messageManager->addSuccess(__('Mapping(s) deleted successfully'));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Remove Item
     *
     * @param object $item
     */
    protected function removeItem($mapping)
    {
        $mapping->delete();
    }

    /**
     * delete all records with shipping code
     *
     * @param string $shippingCode
     * @return void
     */
    protected function deleteAllRecordsWithShippingCode($shippingCode)
    {
        if (!empty($shippingCode)) {
            $data = $this->shippingMappingFactory->create()->getCollection()
            ->addFieldToFilter(
                'shipping_code',
                ['eq'=>$shippingCode]
            );
            if (!empty($data)) {
                foreach ($data as $item) {
                    $this->removeItem($item);
                }
            }
        }
    }
}
