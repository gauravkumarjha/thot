<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PaymentShippingRestriction
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\PaymentShippingRestriction\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Webkul PaymentShippingRestriction shippingmappingcontroller
 */
abstract class ShippingMapping extends \Magento\Backend\App\Action
{
    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_PaymentShippingRestriction::shippingmapping');
    }
}
