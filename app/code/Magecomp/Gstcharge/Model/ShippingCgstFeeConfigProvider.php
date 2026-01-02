<?php
namespace Magecomp\Gstcharge\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Checkout\Model\Session;

class ShippingCgstFeeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magecomp\Gstcharge\Helper\Data
     */
    protected $dataHelper;
    protected $storeManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    /**
     * @param \Magecomp\Gstcharge\Helper\Data $dataHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        GstHelper $dataHelper,
        Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->dataHelper = $dataHelper;
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager; 
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $GstchargeConfig = [];
        $storeId =$this->storeManager->getStore()->getId();
        $enabled = $this->dataHelper->isModuleEnabled();
        
        if ($enabled) {
            $quote = $this->checkoutSession->getQuote();
            $ShippingGstTaxType = $this->dataHelper->getShippingGstTaxType();
            $address = $quote->getShippingAddress();
            $GstchargeConfig['shipping_cgst_label'] = 'Incl. of Shipping CGST';
            if ($ShippingGstTaxType == 1) {
                $GstchargeConfig['shipping_cgst_label'] = 'Excl. of Shipping CGST';
            }
            $GstchargeConfig['shipping_cgst_charge'] = $address->getShippingCgstCharge();
            $GstchargeConfig['show_hide_Shipping_Cgstcharge_block'] = ($enabled) ? true : false;
            $GstchargeConfig['show_hide_Shipping_Cgstcharge_shipblock'] = ($enabled) ? true : false;
        }
        return $GstchargeConfig;
    }
}
