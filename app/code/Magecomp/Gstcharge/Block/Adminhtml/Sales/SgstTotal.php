<?php

namespace Magecomp\Gstcharge\Block\Adminhtml\Sales;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Directory\Model\Currency;
use Magento\Framework\DataObject;
use Magento\Quote\Model\QuoteFactory;

class SgstTotal extends Template
{

    /**
     * @var \Magecomp\Gstcharge\Helper\Data
     */
    protected $_dataHelper;
   

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;
    
    protected $quoteFactory;

    public function __construct(
        Context $context,
        GstHelper $dataHelper,
        Currency $currency,
        QuoteFactory $quoteFactory,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->quoteFactory = $quoteFactory;
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

        if (!$this->getSource()->getSgstCharge() || $this->getSource()->getSgstCharge() <= 0) {
            return $this;
        }
        
        $taxType = $this->quoteFactory->create()->load($order->getQuoteId())->getExclPrice();

        $taxTypeLabel = ($taxType == 1) ? 'Excl. of SGST' : 'Incl. of SGST';
        
        $total = new DataObject(
            [
                'code' => 'sgst_charge',
                'value' => $this->getSource()->getSgstCharge(),
                'label' => $taxTypeLabel,
            ]
        );
       
        $this->getParentBlock()->addTotalBefore($total, 'grand_total');

        return $this;
    }
}
