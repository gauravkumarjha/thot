<?php
namespace Magecomp\Gstcharge\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Checkout\Model\Session;

class ShippingSgstFeeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magecomp\Gstcharge\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    protected $storeManager;
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
            $GstchargeConfig['shipping_sgst_label'] = 'Incl. of Shipping SGST';
            if ($ShippingGstTaxType == 1) {
                $GstchargeConfig['shipping_sgst_label'] = 'Excl. of Shipping SGST';
            }
            $GstchargeConfig['shipping_sgst_charge'] = $address->getShippingSgstCharge();
            $GstchargeConfig['show_hide_Shipping_Sgstcharge_block'] = ($enabled) ? true : false;
            $GstchargeConfig['show_hide_Shipping_Sgstcharge_shipblock'] = ($enabled) ? true : false;
        }
        return $GstchargeConfig;
    }
}
