<?php
namespace Magecomp\Gstcharge\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magecomp\Gstcharge\Helper\Data as GstHelper;

class IgstFeeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magecomp\Gstcharge\Helper\Data
     */
    protected $dataHelper;
    protected $storeManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */



    /**
     * @param \Magecomp\Gstcharge\Helper\Data $dataHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        GstHelper $dataHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->dataHelper = $dataHelper;
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
            $gsttpye = $this->dataHelper->getGstTaxType();
            $GstchargeConfig['igst_label'] = 'Incl. of IGST ';
            if ($gsttpye == 1) {
                $GstchargeConfig['igst_label'] = 'Excl. of IGST';
            }
            $GstchargeConfig['igst_charge'] = $this->dataHelper->getIgstCharge();
            $GstchargeConfig['show_hide_igst_block'] = ($enabled) ? true : false;
            $GstchargeConfig['show_hide_igst_shipblock'] = ($enabled ) ? true : false;
        }
        return $GstchargeConfig;
    }
}
