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
namespace Webkul\PhonePe\Controller\PhonePe;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Webkul\PhonePe\Helper\Data;
use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;

class ReturnAction extends Action implements CsrfAwareActionInterface
{

    /**
     * @var Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultRedirect;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    protected $cartManagement;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $orderRepository;

    /**
     * @var \Webkul\PhonePe\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomerAddress $customerAddress
     */
    protected $customerAddress;

    /**
     * @param Context $context
     * @param \Magento\Framework\Controller\ResultFactory $result
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagement
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Webkul\PhonePe\Logger\Logger $logger
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Customer\Helper\Session\CurrentCustomerAddress $customerAddress
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\ResultFactory $result,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Quote\Api\CartManagementInterface $cartManagement,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Webkul\PhonePe\Logger\Logger $logger,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Customer\Helper\Session\CurrentCustomerAddress $customerAddress,
        Data $helper
    ) {
        $this->resultRedirect = $result;
        $this->urlBuilder = $urlBuilder;
        $this->cartManagement = $cartManagement;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->quoteRepository = $quoteRepository;
        $this->customerAddress = $customerAddress;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirect->create($this->resultRedirect::TYPE_REDIRECT);
        $this->logger->info('phone pe payment redirect executed ');
        try {
            $env = $this->helper->getEnv();
            $clientId = $this->helper->getConfigValue('client_id');
            $clientSecret = $this->helper->getConfigValue('client_secret');
            $clientVersion = $this->helper->getConfigValue('client_version');
            $phonePeClient = StandardCheckoutClient::getInstance(
                $clientId,
                $clientVersion,
                $clientSecret,
                $env
            );
            $response = $this->getRequest()->getParams();
            $transactionId = $response['transactionId'];
            $quoteId = $response['quoteId'];
            $transactionId = $this->helper->decryptData($transactionId);
            $quoteId = $this->helper->decryptData($quoteId);
            //  $quoteData = $this->quoteRepository->get($quoteId);
            // $order = $this->orderRepository->get($order);
            // if (!$quoteData->getBillingAddress()->getFirstname()) {
            //     $billAddress = $this->customerAddress->getDefaultBillingAddress();
            //     $quoteData->getBillingAddress()->setFirstname($billAddress->getFirstname())
            //     ->setLastname($billAddress->getLastname())
            //     ->setStreet($billAddress->getStreet())
            //     ->setCity($billAddress->getCity())
            //     ->setTelephone($billAddress->getTelephone())
            //     ->setPostcode($billAddress->getPostcode())
            //     ->setCountryId($billAddress->getCountryId())
            //     ->setRegionId($billAddress->getRegionId());
            // }
            $order = $this->orderRepository->get($quoteId);
            if (!$order || !$order->getId()) {
                throw new \Exception("Order not found for ID: $quoteId");
            }

          //  if ($quoteData->getIsActive()) {
                $checkStatus = $phonePeClient->getOrderStatus($transactionId, true);
                $this->logger->info('phone pe payment redirect response  transactionId :- ' .$transactionId .json_encode($checkStatus->jsonSerialize()));
        
                if ($checkStatus->getState() == Data::COMPLETED_STATE) {
                   // $order = $this->cartManagement->placeOrder($quoteId);
                   // $order = $this->orderRepository->get($order);
                    $paymentDetails = $checkStatus->getPaymentDetails();
                  
                    $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)
                        ->setStatus('processing')
                        ->addStatusHistoryComment(__('Payment confirmed by PhonePe (Txn: %1)', $transactionId));
                    $this->orderRepository->save($order);
                    // $payerId = $paymentDetails[0]->transactionId;
                    $payerId = $checkStatus->getOrderId();
                    $this->helper->createInvoice($payerId, $order, $transactionId);
                } elseif ($checkStatus->getState() == Data::FAILED_STATE) {
                    $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED)
                        ->setStatus('canceled')
                        ->addStatusHistoryComment(__('PhonePe payment failed. Transaction ID: %1', $transactionId));
                    $this->orderRepository->save($order);

                    $this->messageManager->addErrorMessage(__("Something went wrong with the payment."));
                    $resultRedirect->setUrl(
                        $this->urlBuilder->getUrl("checkout/cart")
                    );
                    return $resultRedirect;
                } elseif ($checkStatus->getState() == Data::PENDING_STATE) {
                    $this->logger->info('PhonePe pending transaction: ' . $transactionId . json_encode($checkStatus->jsonSerialize()));

                    // Prevent order confirmation email before placing order
                  //  $quoteData->setSendConfirmation(false);
                 //   $quoteData->getPayment()->setSkipOrderEmail(true);
                  //  $this->quoteRepository->save($quoteData);

                    // Place order without sending email
                   // $orderId = $this->cartManagement->placeOrder($quoteId);
                   // $order = $this->orderRepository->get($orderId);

                    // Explicitly disable email flags
                    $order->setCanSendNewEmailFlag(false);
                    $order->setEmailSent(0);

                    // Update order state & status
                    $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT)
                        ->setStatus(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);

                    // Save updated order
                    $this->orderRepository->save($order);
                    $this->messageManager->addErrorMessage(__("We haven’t received your payment. Please try again."));

                    // Log confirmation
                    $this->logger->info('Order ' . $order->getIncrementId() . ' set to pending, email disabled.');
                }
           // }
            $resultRedirect->setUrl($this->urlBuilder->getUrl("checkout/onepage/success"));
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            $this->messageManager->addError(
                __(
                    "There is an error in making payment with PhonePe payment method. Please contact the admin."
                )
            );
            $resultRedirect->setUrl($this->urlBuilder->getUrl('checkout/cart/', ['_current' => false]));
            return $resultRedirect;
        }
        return $resultRedirect;
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
            return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
