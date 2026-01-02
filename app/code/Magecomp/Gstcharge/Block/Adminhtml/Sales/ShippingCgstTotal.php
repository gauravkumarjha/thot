<?php

namespace Magecomp\Gstcharge\Block\Adminhtml\Sales;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Directory\Model\Currency;
use Magento\Framework\DataObject;
use Magento\Quote\Model\QuoteFactory;

class ShippingCgstTotal extends Template
{

    /**
     * @var \Magecomp\Gstcharge\Helper\Data
     */
    protected $_dataHelper;
   

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $quoteFactory;
     
    protected $_currency;

    public function __construct(
        Context $context,
        GstHelper $dataHelper,
        Currency $currency,
        QuoteFactory $quoteFactory,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this -> quoteFactory = $quoteFactory;
        $this->_currency = $currency;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

    /**
     *
     *
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $order = $this->getOrder();
        $this->getSource();

        if (!$this->getSource()->getShippingCgstCharge() || $this->getSource()->getShippingCgstCharge() <= 0) {
            return $this;
        }
        
        $shippingTaxType = $this->quoteFactory->create()->load($order->getQuoteId())->getShipExclPrice();

        $shippingTaxTypeLabel = ($shippingTaxType == 1) ? 'Excl. of Shipping CGST' : 'Incl. of Shipping CGST';
        
        $total = new DataObject(
            [
                'code' => 'shipping_cgst_charge',
                'value' => $this->getSource()->getShippingCgstCharge(),
                'label' => $shippingTaxTypeLabel,
            ]
        );
       
        $this->getParentBlock()->addTotalBefore($total, 'grand_total');

        return $this;
    }
}
