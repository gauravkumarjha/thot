<?php

/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_PhonePe
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited ( https://webkul.com )
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\PhonePe\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const CIPHERING = "AES-256-CBC";
    public const OPTIONS = 0;
    public const ENCRYPTION_IV = '1234567891011121';
    public const ENCRYPTION_KEY = "Webkul";
    public const COMPLETED_STATE= "COMPLETED";
    public const FAILED_STATE= "FAILED";
    public const PENDING_STATE= "PENDING";
    public const PHONEPE= "phonepe";
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\View\Element\Template
     */
    protected $template;

    /**
     * @var \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface
     */
    protected $transactionBuilder;
    
    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $invoiceService;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $transaction;

    /**
     * @var InvoiceSender
     */
    protected $invoiceSender;

    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @var \Magento\Framework\Url\DecoderInterface
     */
    protected $urlDecoder;

    /**
     * @param \Magento\Framework\View\Element\Template $template
     * @param Transaction\BuilderInterface $transactionBuilder
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Framework\DB\Transaction $transaction
     * @param InvoiceSender $invoiceSender
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Url\DecoderInterface $urlDecoder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\View\Element\Template $template,
        Transaction\BuilderInterface $transactionBuilder,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        InvoiceSender $invoiceSender,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Url\DecoderInterface $urlDecoder,
        ScopeConfigInterface $scopeConfig,
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->transactionBuilder = $transactionBuilder;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
        $this->urlEncoder = $urlEncoder;
        $this->urlDecoder = $urlDecoder;
        $this->template = $template;
    }

    /**
     * Get environment value
     *
     * @return string
     */
    public function getEnv()
    {
        $environment = $this->getConfigValue('environment');
        return $environment;
    }

    /**
     * Get Config Value
     *
     * @param string $fieldId
     * @return string
     */
    public function getConfigValue($fieldId)
    {
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $result = ($this->_scopeConfig->getValue('payment/phonepe/'.$fieldId, $scope));
        return $result;
    }

    /**
     * This function is used to invoice the order
     *
     * @param int $payerId
     * @param \Magento\Sales\Model\Order $order
     * @param int $transactionId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createInvoice($payerId, $order, $transactionId)
    {
        try {
            $payment = $order->getPayment();
            $payment->setTransactionId($payerId);
            $additionData['merchantTransId'] = $transactionId;
            $payment->setAdditionalInformation(
                [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $additionData]
            );
            $trans = $this->transactionBuilder;
            $transaction = $trans->setPayment($payment)
                ->setOrder($order)
                ->setTransactionId($payerId)
                ->setAdditionalInformation(
                    [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $additionData]
                )
                ->setFailSafe(true)
                ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);
            $payment->setParentTransactionId(null);
            $payment->save();
            $transaction->save();
            if (!$order->canInvoice()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Cannot create an invoice.')
                );
            }
            $invoice = $this->invoiceService->prepareInvoice($order);
            if (!$invoice->getTotalQty()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Cannot create an invoice without products.')
                );
            }
            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)
                    ->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING)
                    ->save();
            $invoice->setTransactionId($payerId);
            $invoice->setRequestedCaptureCase(
                \Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE
            );
            $invoice->register();
            $invoice->save();
            $transactionSave = $this->transaction
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
            $transactionSave->save();
            $this->invoiceSender->send($invoice);
            
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e));
        }
    }

    /**
     * Encrypt Data
     *
     * @param string $data
     * @return string
     */
    public function encryptData($data)
    {
        list(
            $ciphering, $options, $encryption_iv, $encryption_key
        ) = $this->getOpensslConfig();
        $encryptData = openssl_encrypt($data, $ciphering, $encryption_key, $options, $encryption_iv);
        return $this->urlEncoder->encode($encryptData);
    }

    /**
     * Decrypt Data
     *
     * @param string $data
     * @return string
     */
    public function decryptData($data)
    {
        list(
            $ciphering, $options, $decryption_iv, $decryption_key
        ) = $this->getOpensslConfig();
        $data = $this->urlDecoder->decode($data);
        $decryptData = openssl_decrypt($data, $ciphering, $decryption_key, $options, $decryption_iv);
        return $decryptData;
    }

    /**
     * Openssl Data
     *
     * @return array
     */
    public function getOpensslConfig()
    {
        $ciphering = self::CIPHERING;
        $options = self::OPTIONS;
        $encryption_iv = self::ENCRYPTION_IV;
        $encryption_key = self::ENCRYPTION_KEY;
        return [$ciphering, $options, $encryption_iv, $encryption_key];
    }
}
