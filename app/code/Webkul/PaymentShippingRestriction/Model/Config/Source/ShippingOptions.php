<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PaymentShippingRestriction
 * @author    Webkul
 * @copyright Copyright (c)Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
 
namespace Webkul\PaymentShippingRestriction\Model\Config\Source;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Shipping\Model\Config;
 
class ShippingOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Config
     */
    protected $_deliveryModelConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Config $deliveryModelConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Config $deliveryModelConfig
    ) {
 
        $this->_scopeConfig = $scopeConfig;
        $this->_deliveryModelConfig = $deliveryModelConfig;
    }
  
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
      
        $deliveryMethods = $this->_deliveryModelConfig->getActiveCarriers();
        $deliveryMethodsArray = [];
         // customization chetaru start
        // foreach ($deliveryMethods as $shippingCode => $shippingModel) {
        //     $shippingTitle = $this->_scopeConfig->getValue('carriers/'.$shippingCode.'/title');
        //     if (empty($shippingTitle)) {
        //         $shippingTitle = $shippingCode;
        //     }
        //     $deliveryMethodsArray[$shippingCode] = [
        //         'label' => $shippingTitle,
        //         'value' => $shippingCode
        //     ];
        // }
        // customization chetaru end
        return $deliveryMethodsArray;
    }
}
