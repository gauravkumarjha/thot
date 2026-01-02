<?php
 /**
  * Webkul Software
  *
  * @category Webkul
  * @package Webkul_PaymentShippingRestriction
  * @author Webkul
  * @copyright Copyright (c)Webkul Software Private Limited (https://webkul.com)
  * @license https://store.webkul.com/license.html
  */
namespace Webkul\PaymentShippingRestriction\Model\Config\Source;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Payment\Model\Config;

class PaymentOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
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
    * @param Config $paymentModelConfig
    */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Config $paymentModelConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_paymentModelConfig = $paymentModelConfig;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $payments = $this->_paymentModelConfig->getActiveMethods();
        $methods = [];
        foreach ($payments as $paymentCode => $paymentModel) {
            if ($paymentCode!='free') {
                $paymentTitle = $this->_scopeConfig
                    ->getValue('payment/'.$paymentCode.'/title');
                if (empty($paymentTitle)) {
                    $paymentTitle = $paymentCode;
                }
                $methods[$paymentCode] = [
                    'label' => $paymentTitle,
                    'value' => $paymentCode
                ];
            }
        }
        return $methods;
    }
}
