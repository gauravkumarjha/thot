<?php

/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PhonePe
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited ( https://webkul.com )
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\PhonePe\Model;

use Magento\Payment\Model\Method\AbstractMethod;

class PaymentMethod extends AbstractMethod
{
    public const METHOD_CODE = 'phonepe';

    /**
     * @var string
     */
    protected $_code = self::METHOD_CODE;

    /**
     * @var bool
     */
    protected $_isGateway = true;

    /**
     * Availability option.
     *
     * @var bool
     */
    protected $_isInitializeNeeded = false;

    /**
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * @var bool
     */
    protected $_canCapturePartial = true;

    /**
     * @var bool
     */
    protected $_canRefund = true;

    /**
     * @var bool
     */
    protected $_canVoid = true;

    /**
     * @var bool
     */
    protected $_canUseInternal = false;

    /**
     * @var bool
     */
    protected $_canUseCheckout = true;

    /**
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * @var \Magento\Sales\Api\TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
}
