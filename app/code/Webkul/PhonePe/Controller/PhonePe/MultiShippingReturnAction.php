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

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Multishipping\Controller\Checkout;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Customer\Model\Session;
use Magento\Multishipping\Model\Checkout\Type\Multishipping\State;
use Webkul\PhonePe\Helper\Data;
use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;

class MultiShippingReturnAction extends Checkout implements CsrfAwareActionInterface
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
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $orderRepository;

    /**
     * @var \Webkul\PhonePe\Logger\Logger
     */
    protected $logger;

    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @var \Magento\Framework\Session\Generic
     */
    private $genericSession;

    /**
     * @param Context $context
     * @param \Magento\Framework\Controller\ResultFactory $result
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $accountManagement
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Webkul\PhonePe\Logger\Logger $logger
     * @param SessionManagerInterface $session
     * @param \Magento\Framework\Session\Generic $genericSession
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\ResultFactory $result,
        \Magento\Framework\UrlInterface $urlBuilder,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Webkul\PhonePe\Logger\Logger $logger,
        SessionManagerInterface $session,
        \Magento\Framework\Session\Generic $genericSession,
        Data $helper
    ) {
        $this->resultRedirect = $result;
        $this->urlBuilder = $urlBuilder;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->session = $session;
        $this->genericSession = $genericSession;
        parent::__construct(
            $context,
            $customerSession,
            $customerRepository,
            $accountManagement
        );
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
        $this->logger->info('phone pe multishipping payment redirect executed ');
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
            $responseData = $this->getRequest()->getParams();
            $encyTransId = $responseData['transactionId'];
            $transactionId = $this->helper->decryptData($encyTransId);
            $checkStatus = $phonePeClient->getOrderStatus($transactionId, true);
            $this->logger->info('phone pe mulitshipping payment redirect response  :- '.json_encode($checkStatus->jsonSerialize()));
            $state = $checkStatus->getState();
    
            if ($state == Data::COMPLETED_STATE || $state == Data::PENDING_STATE) {
                $this->_getCheckout()->createOrders();
                $this->_getState()->setCompleteStep(State::STEP_OVERVIEW);

                if ($this->session->getAddressErrors()) {
                    $this->_getState()->setActiveStep(State::STEP_RESULTS);
                    $resultRedirect->setUrl($this->urlBuilder->getUrl("multishipping/checkout/results"));
                } else {
                    $this->_getState()->setActiveStep(State::STEP_SUCCESS);
                    $this->_getCheckout()->getCheckoutSession()->clearQuote();
                    $this->_getCheckout()->getCheckoutSession()->setDisplaySuccess(true);
                    if ($state != Data::PENDING_STATE) {
                        $paymentDetails = $checkStatus->getPaymentDetails();
                        // $payerId = $paymentDetails[0]->transactionId;
                        $payerId = $checkStatus->getOrderId();
                        $orders = $this->genericSession->getOrderIds();
                        foreach ($orders as $orderId) {
                            $order = $this->orderRepository->get($orderId);
                            $this->helper->createInvoice($payerId, $order, $transactionId);
                        }
                    }
                    $resultRedirect->setUrl($this->urlBuilder->getUrl("multishipping/checkout/success"));
                }
            } elseif ($state == "FAILED") {
                $this->messageManager->addErrorMessage(__("Something went wrong with the payment."));
                $resultRedirect->setUrl(
                    $this->urlBuilder->getUrl("checkout/cart")
                );
                return $resultRedirect;
            }
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
