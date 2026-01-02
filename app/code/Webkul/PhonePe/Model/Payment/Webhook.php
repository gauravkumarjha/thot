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
namespace Webkul\PhonePe\Model\Payment;

use Webkul\PhonePe\Api\WebhookInterface;
use PhonePe\payments\v1\PhonePePaymentClient;
use Magento\Sales\Model\Order as OrderModel;
use Webkul\PhonePe\Helper\Data;

class Webhook implements WebhookInterface
{
    public const PENDING_STATUS = "pending";
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var OrderModel
     */
    protected $orderModel;

    /**
     * @var \Webkul\PhonePe\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * __construct function
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param Data $helper
     * @param OrderModel $orderModel
     * @param \Webkul\PhonePe\Logger\Logger $logger
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        Data $helper,
        OrderModel $orderModel,
        \Webkul\PhonePe\Logger\Logger $logger,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository
    ) {
        $this->request = $request;
        $this->helper = $helper;
        $this->orderModel = $orderModel;
        $this->logger = $logger;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->cartRepository = $cartRepository;
    }
    
    /**
     * Handle payment success
     *
     * @api
     * @return bool
     */
    public function executeWebhook()
    {
        try {
            $environment = $this->helper->getEnv();
            $merchantId = $this->helper->getConfigValue('merchant_id');
            $saltKey = $this->helper->getConfigValue('salt_key');
            $saltIndex = $this->helper->getConfigValue('salt_index');
            $phonePeClient = new PhonePePaymentClient($merchantId, $saltKey, $saltIndex, $environment, true);
            $response = $this->request->getParams();
            $transactionId = $response['transactionId'];
            $quoteId = $response['quoteId'];
            $transactionId = $this->helper->decryptData($transactionId);
            $quoteId = $this->helper->decryptData($quoteId);
            $quote = $this->cartRepository->get($quoteId);
            $orderIncrementId = $quote->getReservedOrderId();
            if (!$quote->getIsActive()) {
                $checkStatus = $phonePeClient->statusCheck($transactionId);
                $payerId = $checkStatus->getTransactionId();
                $state = $checkStatus->getState();
                if (isset($response['isMultishipping'])) {
                    $orders = $this->orderCollectionFactory->create()
                    ->addFieldToFilter('quote_id', ['eq' => $quoteId]);
                    foreach ($orders as $order) {
                        $this->createInvoice($order, $state, $payerId, $transactionId);
                    }
                } else {
                    $order = $this->orderModel->loadByIncrementId($orderIncrementId);
                    $this->createInvoice($order, $state, $payerId, $transactionId);
                }
            }
            return true;
        } catch (\Exception $e) {
            $this->logger->info('PhonePePaymentCallback'. $e->getMessage());
        }
    }

    /**
     * Create invoice
     *
     * @param Magento\Sales\Model\Order $order
     * @param string $state
     * @param string $payerId
     * @param string $transactionId
     */
    public function createInvoice($order, $state, $payerId, $transactionId)
    {
        if ($order->getStatus() == self::PENDING_STATUS) {
            if ($state == Data::COMPLETED_STATE) {
                $this->helper->createInvoice($payerId, $order, $transactionId);
            } elseif ($state == Data::FAILED_STATE) {
                $order->cancel();
                $order->save();
            }
        }
    }
}
