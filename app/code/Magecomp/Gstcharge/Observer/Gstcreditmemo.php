<?php
namespace Magecomp\Gstcharge\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magecomp\Gstcharge\Model\CgstFeeConfigProvider;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Tax\Model\Config;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Quote\Model\QuoteFactory;
use Magento\Framework\Event\Observer;

class Gstcreditmemo implements ObserverInterface
{
    /**
     * @var \Magecomp\Surcharge\Model\Quote\Address\Total\SurchargeFactory
     */
    protected $_totalSurchargeFactory;

    /**
     * @var \Magecomp\Surcharge\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_modelConfig;
    protected $_productloader;
    protected $_categoryFactory;
    protected $quoteFactory;
    protected $storeManager;

    public function __construct(
        CgstFeeConfigProvider $totalSurchargeFactory,
        GstHelper $helperData,
        Config $modelConfig,
        ProductFactory $_productloader,
        CategoryFactory $categoryFactory,
        QuoteFactory $quoteFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_totalSurchargeFactory = $totalSurchargeFactory;
        $this->_helperData = $helperData;
        $this->_modelConfig = $modelConfig;
        $this->_categoryFactory = $categoryFactory;
        $this->_productloader = $_productloader;
        $this->quoteFactory = $quoteFactory;
        $this->storeManager = $storeManager;
    }

    public function execute(Observer $observer)
    { 
          $storeId =$this->storeManager->getStore()->getId();
              $creditmemoId = $observer->getEvent()->getCreditmemo();
              $order=$observer->getEvent()->getCreditmemo()->getOrder();
              $quoteId=$order->getQuoteId();
              $quote = $this->quoteFactory->create()->load($quoteId);
              $om = \Magento\Framework\App\ObjectManager::getInstance();
              $storeManager1 = $om->create('Magento\Quote\Api\CartRepositoryInterface');
              $test1=$storeManager1->get($quoteId);
              $quote = $this->quoteFactory->create()->load($quoteId);
              $exckudingTax=$test1->getExclPrice();
              //$exckudingTax=$quote->getExclPrice();
              $shippingAddress = $order->getShippingAddress();
        try {
            if (!($this->_helperData->isModuleEnabledDisable($order->getStoreId()))) {
                return 0;
            }

              
            if ($quote->getIsVirtual()) {
                $shippingAddress = $quote->getBillingAddress();
            }
            if ($shippingAddress) {
                $creditmemoId->setBuyerGstNumber($order->getBuyerGstNumber());
                $creditmemoId->setPercentShippingCgstCharge($order->getPercentShippingCgstCharge());
                $creditmemoId->setShippingCgstCharge($order->getShippingCgstCharge());
                $creditmemoId->setPercentShippingSgstCharge($order->getPercentShippingSgstCharge());
                $creditmemoId->setShippingSgstCharge($order->getShippingSgstCharge());
                $creditmemoId->setPercentShippingIgstCharge($order->getPercentShippingIgstCharge());
                $creditmemoId->setShippingIgstCharge($order->getShippingIgstCharge());

                $CountryId=$shippingAddress->getCountryId();
                $CustomerRegionId=$shippingAddress->getRegionId();
                $SystemRegionId=$this->_helperData->getGstStateConfig();

                if ($CountryId!='IN') {
                      return 0;
                }

                $TotalGstPrice=0;
                foreach ($creditmemoId->getAllItems() as $item) {
                       $gstPercent=0;
                       $product=$this->_productloader->create()->load($item->getProductId());
                       $itemPriceAfterDiscount= $item->getDiscountAmount()?($item->getDiscountAmount() / $item->getPrice()) * 100 : '0.00' ;
                       $prdPrice=$item->getPrice()-$itemPriceAfterDiscount;
                       $gstPercent=$product->getGstSource();
                       $gstPercentMinPrice=$product->getGstSourceMinprice();
                       $gstPercentAfterMinprice=$product->getGstSourceAfterMinprice();

                    if ($gstPercent<=0) {
                        $cats = $product->getCategoryIds();
                        foreach ($cats as $category_id) {
                              $_cat = $this->_categoryFactory->create()->load($category_id) ;
                              $gstPercent=$_cat->getGstCatSource();
                              $gstPercentMinPrice=$_cat->getGstCatSourceMinprice();
                              $gstPercentAfterMinprice=$_cat->getGstCatSourceAfterMinprice();
                            if ($gstPercent!='') {
                                if ($gstPercentMinPrice > 0 && $gstPercentMinPrice > $prdPrice) {
                                       $gstPercent=$gstPercentAfterMinprice;
                                }
                                break;
                            }
                        }
                    } else {
                        if ($gstPercentMinPrice > 0 && $gstPercentMinPrice > $prdPrice) {
                                $gstPercent=$gstPercentAfterMinprice;
                        }
                    }
                    if ($gstPercent<=0) {
                          $gstPercent                =    $this->_helperData->getGstTaxperConfigStore($order->getStoreId());
                          $gstPercentMinPrice        =    $this->_helperData->getGstTaxMinPriceConfig();
                          $gstPercentAfterMinprice    =    $this->_helperData->getGstTaxPerMinPriceConfig();
                        if ($gstPercentMinPrice > 0 && $gstPercentMinPrice > $prdPrice) {
                              $gstPercent=$gstPercentAfterMinprice;
                        }
                    }
                    if($gstPercent=='-1'){
                        $gstPercent = 0;
                    }
                    $rowTotal = $item->getRowTotal();
                    if ($product->getTypeId()=="bundle") {
                          $rowTotal = $item->getPrice();
                    }
                    $DiscountAmount=$item->getDiscountAmount();

                    if ($exckudingTax) {
                        $GstPrice= ((($rowTotal-$DiscountAmount)*$gstPercent)/100);
                    } else {
                        $totalPercent = 100 + $gstPercent;
                        $perPrice     = ($rowTotal-$DiscountAmount) / $totalPercent;
                        $GstPrice     = $perPrice * $gstPercent;
                    }
                    if ($CountryId=='IN' && $CustomerRegionId==$SystemRegionId) {
                        $GstPrice=round($GstPrice,3);
                          $item->setCgstCharge($GstPrice/2);
                          $item->setCgstPercent($gstPercent/2);
                          $item->setSgstCharge($GstPrice/2);
                          $item->setSgstPercent($gstPercent/2);
                    } elseif ($CountryId=='IN' && $CustomerRegionId!=$SystemRegionId) {
                         $item->setIgstCharge($GstPrice);
                         $item->setIgstPercent($gstPercent);
                    }
                          $item->setExclPrice($exckudingTax);
                }

            }
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }
}
