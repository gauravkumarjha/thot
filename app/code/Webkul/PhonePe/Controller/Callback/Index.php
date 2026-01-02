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
namespace Webkul\PhonePe\Controller\Callback;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Webkul\PhonePe\Helper\Data;
use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;
use PhonePe\common\exceptions\PhonePeException;
use Magento\Sales\Model\Order as OrderModel;
use PhonePe\payments\v2\models\response\ResponseComponents\Payload;

class Index extends Action
{
    public const PENDING_STATUS = "pending";
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Webkul\PhonePe\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var OrderModel
     */
    protected $orderModel;

    /**
     * @var \Magento\Sales\Api\CreditmemoRepositoryInterface
     */
    protected $creditmemoRepository;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @param Context $context
     * @param \Webkul\PhonePe\Logger\Logger $logger
     * @param Data $helper
     * @param OrderModel $orderModel
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        Context $context,
        \Webkul\PhonePe\Logger\Logger $logger,
        Data $helper,
        OrderModel $orderModel,
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->logger = $logger;
        $this->helper = $helper;
        $this->cartRepository = $cartRepository;
        $this->orderModel = $orderModel;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->request = $request;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $this->logger->info('phone pe callback executed ');
        $headers = $this->request->getHeaders()->toArray();
        $requestBody = $this->request->getContent();

        
        try {
            $username = $this->helper->getConfigValue('callback_username');
            $password = $this->helper->getConfigValue('callback_password');
    
            // Initialize PhonePe Client
            $env = $this->helper->getEnv();
            $clientId = $this->helper->getConfigValue('client_id');
            $clientSecret = $this->helper->getConfigValue('client_secret');
            $clientVersion = $this->helper->getConfigValue('client_version');
    
            $standardCheckoutClient = StandardCheckoutClient::getInstance(
                $clientId,
                $clientVersion,
                $clientSecret,
                $env
            );
            $callbackResponse = $standardCheckoutClient->verifyCallbackResponse(
                $headers,
                json_decode($requestBody, true),
                $username,
                $password
            );
            $payload = $callbackResponse->getPayload();
            $this->logger->info('phone pe callback response  :- '.json_encode($callbackResponse->jsonSerialize()));
            $eventType = $callbackResponse->getType();
            if ($eventType == 'CHECKOUT_ORDER_COMPLETED' || $eventType == 'CHECKOUT_ORDER_FAILED') {
                $this->updateOrder($payload);
            } elseif ($eventType == 'PG_REFUND_COMPLETED') {
                $this->updateCreditMemo($payload);
            } else {
                $this->logger->warning('[PhonePe Callback] event type: ' . $callbackResponse->getType());
            }

        } catch (PhonePeException $e) {
            // Log or handle error
            $message = "Error validating callback response: " . $e->getMessage();
            $this->logger->info($message);
        }
    }

    /**
     * Update order
     *
     * @param Payload $payload
     * @return void
     */
    public function updateOrder($payload)
    {
        $merchantOrderId = $payload->getMerchantOrderId();
        $paymentDetails = $payload->getPaymentDetails();
        $state = $paymentDetails->sate;
        // $payerId = $paymentDetails->transactionId;
        $payerId = $payload->getOrderId();
        if ($merchantOrderId) {
            $parts = explode('_', $merchantOrderId);
            $quoteId = (int)$parts[2];
        }
        $quote = $this->cartRepository->get($quoteId);
        $orderIncrementId = $quote->getReservedOrderId();
        $order = $this->orderModel->loadByIncrementId($orderIncrementId);
        if ($order->getStatus() == self::PENDING_STATUS) {
            if ($state == Data::COMPLETED_STATE) {
                $this->helper->createInvoice($payerId, $order, $merchantOrderId);
            } elseif ($state == Data::FAILED_STATE) {
                $order->cancel();
                $order->save();
            }
        }
    }

    /**
     * Update credit memo
     *
     * @param Payload $payload
     * @return void
     */
    public function updateCreditMemo($payload)
    {
        $paymentDetails = $payload->getPaymentDetails();
        $state = $paymentDetails->state;
        if ($state == Data::COMPLETED_STATE) {
            $merchantRefundId = $payload->merchantRefundId;
            if ($merchantRefundId && strpos($merchantRefundId, 'REFUND_') === 0) {
                $creditmemoId = (int) str_replace('REFUND_', '', $merchantRefundId);
            }
            $creditmemo = $this->creditmemoRepository->get($creditmemoId);
            $creditmemo->setState(\Magento\Sales\Model\Order\Creditmemo::STATE_REFUNDED)->save();
        }
    }
}
