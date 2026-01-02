<?php

namespace Magecomp\Gstcharge\Block\Adminhtml\Sales\Order\Invoice;

use Magento\Framework\View\Element\Template\Context;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Model\OrderFactory;

class ShippingCgstInvoiceTotal extends Template
{

    /**
     * @var \Magecomp\Gstcharge\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Order invoice
     *
     * @var \Magento\Sales\Model\Order\Invoice|null
     */
    protected $_invoice = null;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_source;

    /**
     * OrderFee constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    protected $quoteFactory;
    protected $orderFactory;
     
    public function __construct(
        Context $context,
        GstHelper $dataHelper,
        QuoteFactory $quoteFactory,
        OrderFactory $orderFactory,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->quoteFactory = $quoteFactory;
        $this->orderFactory = $orderFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    public function getInvoice()
    {
        return $this->getParentBlock()->getInvoice();
    }
    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getInvoice();
        $this->getSource();
        if (!$this->getInvoice()->getShippingCgstCharge() || $this->getInvoice()->getShippingCgstCharge() <=0) {
            return $this;
        }
        
        $order = $this->orderFactory->create()->load($this->getInvoice()->getOrderId());
        $shippingTaxType = $this->quoteFactory->create()->load($order->getQuoteId())->getShipExclPrice();

        $shippingTaxTypeLabel = ($shippingTaxType == 1) ? 'Excl. of Shipping CGST' : 'Incl. of Shipping CGST';

        
        $total = new DataObject(
            [
                'code' => 'shipping_cgst_charge',
                'value' => $this->getInvoice()->getShippingCgstCharge(),
                'label' => $shippingTaxTypeLabel,
            ]
        );

        $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        return $this;
    }
}
