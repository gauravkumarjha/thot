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

namespace Webkul\PhonePe\Controller\Multishipping\Checkout;

use Magento\Checkout\Api\PaymentProcessingRateLimiterInterface;
use Magento\Checkout\Api\Exception\PaymentProcessingRateLimitExceededException;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Checkout\Api\AgreementsValidatorInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\PaymentException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Multishipping\Controller\Checkout\OverviewPost;
use Magento\Multishipping\Model\Checkout\Type\Multishipping\State;
use Psr\Log\LoggerInterface;
use Webkul\PhonePe\Helper\Data;
use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;
use PhonePe\payments\v2\models\request\builders\StandardCheckoutPayRequestBuilder;

/**
 * Placing orders.
 *
 */
class OverviewPosts extends OverviewPost
{
    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @var PaymentProcessingRateLimiterInterface
     */
    private $paymentRateLimiter;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    public $currencyFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $checkoutHelper;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param Data $helper
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $accountManagement
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param Validator $formKeyValidator
     * @param LoggerInterface $logger
     * @param AgreementsValidatorInterface $agreementValidator
     * @param SessionManagerInterface $session
     * @param PaymentProcessingRateLimiterInterface $paymentRateLimiter
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        \Webkul\PhonePe\Helper\Data $helper,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        Validator $formKeyValidator,
        LoggerInterface $logger,
        AgreementsValidatorInterface $agreementValidator,
        SessionManagerInterface $session,
        ?PaymentProcessingRateLimiterInterface $paymentRateLimiter
    ) {
        $this->session = $session;
        $this->helper = $helper;
        $this->checkoutHelper = $checkoutHelper;
        $this->currencyFactory = $currencyFactory;
        $this->urlBuilder = $urlBuilder;
        $this->paymentRateLimiter = $paymentRateLimiter;
        parent::__construct(
            $context,
            $customerSession,
            $customerRepository,
            $accountManagement,
            $formKeyValidator,
            $logger,
            $agreementValidator,
            $session,
            $paymentRateLimiter
        );
    }

    /**
     * Overview action
     *
     * @return void
     */
    public function execute()
    {
        $this->logger->info('phone pe multishipping payment initialize executed ');
        try {
            $this->paymentRateLimiter->limit();
            if (!$this->_validateMinimumAmount()) {
                return;
            }

            if (!$this->agreementsValidator->isValid(array_keys($this->getRequest()->getPost('agreement', [])))) {
                $this->messageManager->addErrorMessage(
                    __('Please agree to all Terms and Conditions before placing the order.')
                );
                $this->_redirect('*/*/billing');
                return;
            }

            $paymentData = $this->getRequest()->getPost('payment');
            $paymentInstance = $this->_getCheckout()->getQuote()->getPayment();
            if (isset($paymentData['cc_number'])) {
                $paymentInstance->setCcNumber($paymentData['cc_number']);
            }
            if (isset($paymentData['cc_cid'])) {
                $paymentInstance->setCcCid($paymentData['cc_cid']);
            }
            if ($paymentInstance->getMethod() == Data::PHONEPE) {
                $quote = $this->_getCheckout()->getQuote();
                $redirectUrl = $this->phonepeCheckout($quote);
                $this->_redirect($redirectUrl);
                return;
            }
            $this->_getCheckout()->createOrders();
            $this->_getState()->setCompleteStep(State::STEP_OVERVIEW);

            if ($this->session->getAddressErrors()) {
                $this->_getState()->setActiveStep(State::STEP_RESULTS);
                $this->_redirect('*/*/results');
            } else {
                $this->_getState()->setActiveStep(State::STEP_SUCCESS);
                $this->_getCheckout()->getCheckoutSession()->setDisplaySuccess(true);
                $this->_getCheckout()->getCheckoutSession()->clearQuote();
                $this->_redirect('*/*/success');
            }
        } catch (PaymentProcessingRateLimitExceededException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_redirect('*/*/overview');
        } catch (PaymentException $ex) {
            $message = $ex->getMessage();
            if (!empty($message)) {
                $this->messageManager->addErrorMessage($message);
            }
            $this->_redirect('*/*/billing');
        } catch (\Magento\Checkout\Exception $e) {
            $this->checkoutHelper->sendPaymentFailedEmail(
                $this->_getCheckout()->getQuote(),
                $e->getMessage(),
                'multi-shipping'
            );
            $this->_getCheckout()->getCheckoutSession()->clearQuote();
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_redirect('*/cart');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->checkoutHelper->sendPaymentFailedEmail(
                $this->_getCheckout()->getQuote(),
                $e->getMessage(),
                'multi-shipping'
            );
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_redirect('*/*/billing');
        } catch (\Exception $e) {
            $this->logger->critical($e);
            try {
                $this->checkoutHelper->sendPaymentFailedEmail(
                    $this->_getCheckout()->getQuote(),
                    $e->getMessage(),
                    'multi-shipping'
                );
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
            $this->messageManager->addErrorMessage(__('Order place error'));
            $this->_redirect('*/*/billing');
        }
    }

    /**
     * Phonepe checkout
     *
     * @param Magento\Quote\Model\Quote $quote
     * @return string
     */
    public function phonepeCheckout($quote)
    {
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
            $merchantTransactionId = "ORDER_" . time() . '_' .$quote->getId();
            $transactionId = $this->helper->encryptData($merchantTransactionId);
            $quoteId = $this->helper->encryptData($quote->getId());
            $queryParams =[
                'transactionId' => $transactionId,
                'quoteId' => $quoteId,
                'isMultishipping' => true
            ];
            $callBackUrl = $this->urlBuilder->getUrl("rest/V1/PhonePe/", [
                '_current' => true,
                '_use_rewrite' => false,
                '_query' => $queryParams
            ]);
            $redirectUrl = $this->urlBuilder->getUrl("phonepe/phonepe/multishippingreturnaction", [
                '_current' => true,
                '_use_rewrite' => false,
                '_query' => $queryParams
            ]);
            $currencyCodeFrom = $quote->getQuoteCurrencyCode();
            $rate = $this->currencyFactory->create()
                    ->load($currencyCodeFrom)
                    ->getAnyRate('INR');
            $grandTotal = $quote->getGrandTotal();
            $price = ($rate * $grandTotal)*100;
            $request = StandardCheckoutPayRequestBuilder::builder()
                ->merchantOrderId($merchantTransactionId)
                ->amount($price)
                ->redirectUrl($redirectUrl)
                ->build();
            $response = $phonePeClient->pay($request);
            $this->logger->info('phone pe multishippin payment initialize response  :- '.json_encode($response->jsonSerialize()));
            if ($response->getState() === "PENDING") {
                $redirectUrl = $response->getRedirectUrl();
                return $redirectUrl;
            } else {
                $message = "Payment initiation failed: " . $response->getState();
                $this->logger->info($message);
                $this->messageManager->addError(
                    __(
                        "There is an error in making payment with PhonePe payment method. Please contact the admin."
                    )
                );
                return $this->urlBuilder->getUrl('*/*/billing');
            }
        } catch (\PhonePe\common\exceptions\PhonePeException $e) {
            $this->logger->info($e->getMessage());
            $this->messageManager->addError(
                __(
                    "There is an error in making payment with PhonePe payment method. Please contact the admin."
                )
            );
            return $this->urlBuilder->getUrl('*/*/billing');
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
            $this->messageManager->addError(
                __(
                    "There is an error in making payment with PhonePe payment method. Please contact the admin."
                )
            );
            return $this->urlBuilder->getUrl('*/*/billing');
        }
    }
}
