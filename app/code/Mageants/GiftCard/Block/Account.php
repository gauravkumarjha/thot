<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCard\Block;

use Magento\Framework\View\Element\Template\Context;
use Mageants\GiftCard\Model\Account as Giftaccount;
use Magento\Customer\Model\SessionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepositoryInterface;

/**
 * Account class for customer account
 */
class Account extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Mageants\GiftCard\Model\Account
     */
    protected $_giftorders;
    
    /**
     * @var Magento\Customer\Model\SessionFactory
     */
    protected $_customerSession;

    /**
     * @var Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepositoryInterface;

    /**
     * @param Context $context
     * @param Giftaccount $giftAccount
     * @param SessionFactory $customerSession
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param array $data
     */
    public function __construct(
        Context $context,
        Giftaccount $giftAccount,
        SessionFactory $customerSession,
        CustomerRepositoryInterface $customerRepositoryInterface,
        array $data = []
    ) {
        $this->_giftorders = $giftAccount;
        $this->_customerSession = $customerSession;
        $this->_isScopePrivate = true;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        parent::__construct($context, $data);
    }

    /**
     * Return reorder url
     *
     * @param object $order
     * @return string
     */
    public function getReorderUrl($order)
    {
        return $this->getUrl('sales/order/reorder', ['order_id' => $order->getId()]);
    }
    
    /**
     * Return Gift order
     *
     * @return collection object
     */
    public function getGiftOrder()
    {
        $current_customer_id = $this->_customerSession->create()->getCustomer()->getId();
        $customer = $this->_customerRepositoryInterface->getById($current_customer_id);

        $collection = $this->_giftorders->getCollection();
        $joinConditions = 'main_table.order_increment_id = sales_order.increment_id WHERE customer_email = "'.
                            $customer->getEmail().'"';
        $collection->getSelect()->joinLeft(
            ['sales_order'],
            $joinConditions,
            []
        )->columns("sales_order.increment_id");

        return $collection;
    }
}
