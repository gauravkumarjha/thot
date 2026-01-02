<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\GiftCard\Block\Adminhtml\Account\Edit\Tabs\View;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Sales\Model\OrderFactory as SalesOrder;
use Magento\Backend\Model\Session;
use Mageants\GiftCard\Model\Account;
use Mageants\GiftCard\Model\Customer;

/**
 * Order class for Fetch order for Grid
 */
class Order extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var Magento\Sales\Model\OrderFactory
     */
    protected $salesOrder;

    /**
     * @var  Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var Mageants\GiftCard\Model\Account
     */
    protected $modelAccount;
    
    /**
     * @var Mageants\GiftCard\Model\Customer
     */
    protected $modelCustomer;

    /**
     * @var array
     */
    protected $orderIds = [];

    /**
     * @param Context $context
     * @param Data $backendHelper
     * @param SalesOrder $salesOrder
     * @param Session $session
     * @param Account $modelAccount
     * @param Customer $modelCustomer
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        SalesOrder $salesOrder,
        Session $session,
        Account $modelAccount,
        Customer $modelCustomer,
        array $data = []
    ) {
        $this->salesOrder = $salesOrder;
        $this->session = $session;
        $this->modelAccount = $modelAccount;
        $this->modelCustomer = $modelCustomer;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('entity_id');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Add Column Filter To Collection
     *
     * @param object $column
     */
    protected function _addColumnFilterToCollection($column)
    {
        return $this->getCollection()->addFieldToFilter($column->getId(), $column->getFilter()->getValue());
    }

    /**
     * Prepare collection
     */
    protected function _prepareCollection()
    {
        $orderIds = $this->getGiftCardOrderIds();
        $collection = $this->salesOrder->create()->getCollection();
        $collection->addFieldToFilter('increment_id', ['in' => $orderIds]);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare column for Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'increment_id',
            [
                'header' => __('Order Id'),
                'index' => 'increment_id',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        
        $this->addColumn(
            'created_at',
            [
                'header' => __('Purchase On'),
                'index' => 'created_at',
                'type' => 'datetime',
                'filter' => false,
            ]
        );
        
        $this->addColumn(
            'customer_firstname',
            [
                'header' => __('Customer Name'),
                'index' => 'customer_firstname',
                'type' => 'text',
            ]
        );
        
        $this->addColumn(
            'base_grand_total',
            [
                'header' => __('Total Amount'),
                'index' => 'base_grand_total',
                'type' => 'text',
                'filter' => false,
                'sortable' => false,
                'renderer' => \Mageants\GiftCard\Block\Adminhtml\Account\Edit\Tabs\View\Order\Grid\Renderer\TotalAmount::class
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * Return Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/order', ['_current' => true]);
    }

    /**
     * Return Row url
     *
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isHidden()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getGiftCardOrderIds()
    {
        $accountid = (int) $this->getRequest()->getParam('account_id');
        $incrementIds = [];
        if ($accountid) {
            $accountData = $this->modelAccount;
            $accountData = $accountData->load($accountid);
            $giftCardCode = $accountData->getGiftCode();
            $customerData = $this->modelCustomer->load($accountData->getOrderId());

            $orderCollection = $this->salesOrder->create()->getCollection();
            $orderCollection->addFieldToSelect(['increment_id']);
            $connection = $orderCollection->getConnection();
            $select = $orderCollection->getSelect();

            $select->where(
                $connection->quoteInto('main_table.increment_id IN (?)', $customerData->getOrderId()) .
                ' OR (' .
                $connection->quoteInto('main_table.giftcard_code = ?', $giftCardCode) .
                ' AND ' .
                $connection->quoteInto('main_table.giftcard_account_id = ?', $accountid) .
                ')'
            );

            $orderCollection = $orderCollection->load();

            foreach ($orderCollection as $order) {
                $incrementIds[] = $order->getIncrementId();
            }
            $this->session->setGiftCardOwnOrderId($customerData->getOrderId());
            $this->session->setGiftCardValue($accountData->getInitialCodeValue());
        }
        return $incrementIds;
    }
}
