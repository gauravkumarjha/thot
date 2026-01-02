<?php
namespace Magecomp\Gstcharge\Model\Quote\Total;

use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\QuoteValidator;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;

class Sgst extends AbstractTotal
{

    protected $helperData;
    protected $_priceCurrency;
    protected $storeManager;

    /**
     * Collect grand total address amount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    protected $quoteValidator = null;

    public function __construct(
        QuoteValidator $quoteValidator,
        PriceCurrencyInterface $priceCurrency,
        GstHelper $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->quoteValidator = $quoteValidator;
        $this->_priceCurrency = $priceCurrency;
        $this->helperData = $helperData;
        $this->storeManager = $storeManager; 
    }

    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        if (!count($shippingAssignment->getItems())) {
            return $this;
        }
        $storeId =$this->storeManager->getStore()->getId();
        $enabled = $this->helperData->isModuleEnabled();
        $fee = $this->helperData->getSgstCharge($quote);
        
        if ($enabled && $fee) {

            $total->setSgstCharge($fee);
            $quote->setSgstCharge($fee);
            $quote->getShippingAddress()->setSgstCharge($fee);
            if ($this->helperData->getGstTaxType()) {
                $total->setTotalAmount('sgst_charge', $fee);
                $total->setBaseTotalAmount('sgst_charge', $fee);
                $total->setBaseGrandTotal($total->getBaseGrandTotal() + $fee);
            } else {
                 $total->setGrandTotal($total->getGrandTotal());
                 $total->setBaseGrandTotal($total->getBaseGrandTotal());
            }
        }
        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     */
    public function fetch(Quote $quote, Total $total)
    {
        $storeId =$this->storeManager->getStore()->getId();
        $enabled = $this->helperData->isModuleEnabled();
       
        $fee = $this->helperData->getSgstCharge();
        $result = [];
        if ($enabled) {
            $result = [
                'code' => 'sgst_charge',
                'title' => 'sgst',
                'value' => $fee
            ];
        }
        return $result;
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('SGST Fee');
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     */
    protected function clearValues(Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }
}
