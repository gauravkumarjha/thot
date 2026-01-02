<?php
namespace Magecomp\Gstcharge\Model\Quote\Total;

/**
* Class Custom
* @package MageDelight\HelloWorld\Model\Total\Quote
*/

use Magecomp\Gstcharge\Helper\Data as GstHelper;

class Custom extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{

   /**
    * @var \Magento\Framework\Pricing\PriceCurrencyInterface
    */
    protected $_priceCurrency;
    protected $helperData;
   /**
    * Custom constructor.
    * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    */
    public function __construct(
        GstHelper $helperData,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        $this->_priceCurrency = $priceCurrency;
        $this->helperData = $helperData;
    }
   /**
    * @param \Magento\Quote\Model\Quote $quote
    * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
    * @param \Magento\Quote\Model\Quote\Address\Total $total
    * @return $this|bool
    */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        //Fix for discount applied twice
        $items = $shippingAssignment->getItems();
        if (!count($items)) {
            return $this;
        }
           

        parent::collect($quote, $shippingAssignment, $total);
        //$address             = $shippingAssignment->getShipping()->getAddress();
        $label               = '';
        $enabled = $this->helperData->isModuleEnabled();


        if ($total->getDiscountAmount() && $enabled && !$this->helperData->getDiscountGstTaxType()) {
            $dicount_fee = $this->helperData->getDiscountGstCharge($quote);
            if ($enabled && $dicount_fee) {
                $discountAmount  = $total->getDiscountAmount()+$dicount_fee;
               
                $total->setDiscountAmount($discountAmount);
                $total->setBaseDiscountAmount($discountAmount);
                if (!$this->helperData->getDiscountGstTaxType()) {
                    $total->setGrandTotal($total->getGrandTotal() + $dicount_fee);
                    $total->setBaseGrandTotal($total->getBaseGrandTotal() + $dicount_fee);
                }
            }
        }

        return $this;
    }
}
