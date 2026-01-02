<?php

namespace Magecomp\Gstcharge\Block\Sales\Totals;

use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\QuoteFactory;

class SgstInvoiceTotal extends Template
{
    /**
     * @var \Magecomp\Gstcharge\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var Order
     */
    protected $_order;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_source;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    protected $quoteFactory;

    public function __construct(
        Context $context,
        GstHelper $dataHelper,
        QuoteFactory $quoteFactory,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        $this->quoteFactory = $quoteFactory;
        parent::__construct($context, $data);
    }

    /**
     * Check if we nedd display full tax total info
     *
     * @return bool
     */

    public function displayFullSummary()
    {
        return true;
    }

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->_source;
    }

    public function getStore()
    {
        return $this->_order->getStore();
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();
        if (!$this->_source->getSgstCharge() || $this->_source->getSgstCharge() <= 0) {
            return $this;
        }

        $taxType = $this->quoteFactory->create()->load($this->_order->getQuoteId())->getExclPrice();

        $taxTypeLabel = ($taxType == 1) ? 'Excl. of SGST' : 'Incl. of SGST';

        $fee = new DataObject(
            [
                'code' => 'sgst_charge',
                'strong' => false,
                'value' => $this->_order->getSgstCharge(),
                'label' => $taxTypeLabel,
            ]
        );

        $parent->addTotal($fee, 'sgst_charge');

        return $this;
    }
}
