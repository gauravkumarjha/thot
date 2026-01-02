<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PhonePe
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\PhonePe\Api;
 
interface CreditMemoInterface
{
    /**
     * Handle PhonePe callback request
     *
     * @api
     * @return bool
     */
    public function execute();
}
