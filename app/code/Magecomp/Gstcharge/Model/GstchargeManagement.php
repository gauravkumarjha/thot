<?php
namespace Magecomp\Gstcharge\Model;

use Magento\Quote\Api\CartRepositoryInterface;


class GstchargeManagement
{

    protected $quoteFactory;
    protected $_emulation;
    protected $totalsCollector;
  
    public function __construct(
        CartRepositoryInterface $quoteFactory,
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        \Magento\Quote\Model\ShippingAddressManagementInterface $shippingAddress
    )
    {
            $this->quoteFactory = $quoteFactory;
            $this->totalsCollector = $totalsCollector;
            $this->_emulation = $emulation;
            $this->shippingAddress = $shippingAddress;
    }

    public function calculateGst($quoteId,$storeId)
    {
        try {
            $quote = $this->quoteFactory->getActive($quoteId);           
            $this->_emulation->startEnvironmentEmulation($storeId , 'frontend');
            $this->totalsCollector->collectQuoteTotals($quote);
            $quote->save();
            $this->_emulation->stopEnvironmentEmulation();
            $response = [
                "status"=>true,
                'message' => __("Gst added to quote")
            ];
            return json_encode($response);
            
        } catch (\Expection $e) {
            $response = [
                "status"=>false,
                'message' => __($e->getMessage())
            ];
            return json_encode($response);
        }
    }

}

