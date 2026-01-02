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
namespace Webkul\PhonePe\Controller\Adminhtml\Order\Creditmemo;

use Magento\Backend\App\Action;
use Magento\Sales\Helper\Data as SalesData;
use Magento\Sales\Model\Order\Email\Sender\CreditmemoSender;
use Webkul\PhonePe\Helper\Data;
use PhonePe\payments\v2\models\request\builders\StandardCheckoutRefundRequestBuilder;
use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;

class Save extends \Magento\Sales\Controller\Adminhtml\Order\Creditmemo\Save
{
    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * @var CreditmemoSender
     */
    protected $creditmemoSender;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var SalesData
     */
    private $salesData;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $order;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    public $currencyFactory;

    /**
     * @var \Magento\Sales\Api\CreditmemoManagementInterface
     */
    public $creditmemoManagement;

    /**
     * @var \Psr\Log\LoggerInterface $logger
     */
    public $logger;

    /**
     * @param Action\Context $context
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
     * @param CreditmemoSender $creditmemoSender
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Sales\Model\OrderRepository $order
     * @param Data $helper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Sales\Api\CreditmemoManagementInterface $creditmemoManagement
     * @param \Psr\Log\LoggerInterface $logger
     * @param SalesData|null $salesData
     */
    public function __construct(
        Action\Context $context,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
        CreditmemoSender $creditmemoSender,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Sales\Model\OrderRepository $order,
        Data $helper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Sales\Api\CreditmemoManagementInterface $creditmemoManagement,
        \Psr\Log\LoggerInterface $logger,
        SalesData $salesData = null
    ) {
        $this->order = $order;
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
        $this->messageManager = $messageManager;
        $this->currencyFactory = $currencyFactory;
        $this->creditmemoManagement = $creditmemoManagement;
        $this->logger = $logger;
        parent::__construct($context, $creditmemoLoader, $creditmemoSender, $resultForwardFactory, $salesData);
    }

    /**
     * Save creditmemo
     *
     * We can save only new creditmemo. Existing creditmemos are not editable
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Backend\Model\View\Result\Forward
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $this->logger->info('phone pe credidmemo executed ');
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPost('creditmemo');
        if (!empty($data['comment_text'])) {
            $this->_getSession()->setCommentText($data['comment_text']);
        }
        try {
            $this->creditmemoLoader->setOrderId($this->getRequest()->getParam('order_id'));
            $this->creditmemoLoader->setCreditmemo($this->getRequest()->getParam('creditmemo'));
            $this->creditmemoLoader->setInvoiceId($this->getRequest()->getParam('invoice_id'));
            $this->creditmemoLoader->setCreditmemoId($this->getRequest()->getParam('creditmemo_id'));
            $creditmemo = $this->creditmemoLoader->load();
            if ($creditmemo) {
                if (!$creditmemo->isValidGrandTotal()) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('The credit memo\'s total must be positive.')
                    );
                }

                if (!empty($data['comment_text'])) {
                    $creditmemo->addComment(
                        $data['comment_text'],
                        isset($data['is_visible_on_front']),
                        isset($data['comment_customer_notify'])
                    );

                    $creditmemo->setCustomerNoteNotify(isset($data['comment_customer_notify']));
                    $creditmemo->setCustomerNote($data['comment_text']);
                }

                if (isset($data['do_offline'])) {
                    //do not allow online refund for Refund to Store Credit
                    if (!$data['do_offline'] && !empty($data['refund_customerbalance_return_enable'])) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __('Cannot create online refund for Refund to Store Credit.')
                        );
                    }
                }
                $creditmemoManagement = $this->creditmemoManagement;
                $creditmemo->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
                $doOffline = isset($data['do_offline']) ? (bool)$data['do_offline'] : false;
                $order = $this->order->get($creditmemo->getOrderId());
                if ($order->getPayment()->getMethod() == Data::PHONEPE && !$doOffline) {
                    $creditmemoData = $creditmemoManagement->refund($creditmemo, $doOffline);
                    $creditmemoId = $creditmemoData->getEntityId();
                    $creditmemoData->setState(\Magento\Sales\Model\Order\Creditmemo::STATE_OPEN)->save();
                    $this->createPhonePeCreditMemo($order, $creditmemoId);
                    $this->messageManager->addSuccessMessage(__('Refund request has been generated successfully.'));
                } else {
                    $creditmemoManagement->refund($creditmemo, $doOffline);
                    $this->messageManager->addSuccessMessage(__('You created the credit memo.'));
                }
                if (!empty($data['send_email']) && $this->salesData->canSendNewCreditMemoEmail()) {
                    $this->creditmemoSender->send($creditmemo);
                }
                $this->_getSession()->getCommentText(true);
                $resultRedirect->setPath('sales/order/view', ['order_id' => $creditmemo->getOrderId()]);
                return $resultRedirect;
            } else {
                $resultForward = $this->resultForwardFactory->create();
                $resultForward->forward('noroute');
                return $resultForward;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_getSession()->setFormData($data);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(__('We can\'t save the credit memo right now.'));
        }
        $resultRedirect->setPath('sales/*/new', ['_current' => true]);
        return $resultRedirect;
    }

    /**
     * Create phonepe credit memo
     *
     * @param Magento\Sales\Model\Order $order
     * @param int $creditmemoId
     */
    public function createPhonePeCreditMemo($order, $creditmemoId)
    {
        try {
            $env = $this->helper->getEnv();
            $clientId = $this->helper->getConfigValue('client_id');
            $clientSecret = $this->helper->getConfigValue('client_secret');
            $clientVersion = $this->helper->getConfigValue('client_version');
            $additionalInfo = $order->getPayment()->getAdditionalInformation();
            $transactionId = $additionalInfo['raw_details_info']['merchantTransId'];
            $currencyCodeFrom = $order->getOrderCurrencyCode();
            $rate = $this->currencyFactory->create()
                    ->load($currencyCodeFrom)
                    ->getAnyRate('INR');
            $grandTotal = $order->getGrandTotal();
            $price = ($rate * $grandTotal)*100;
            $standardCheckoutClient = StandardCheckoutClient::getInstance(
                $clientId,
                $clientVersion,
                $clientSecret,
                $env
            );

            $refundRequest = StandardCheckoutRefundRequestBuilder::builder()
            ->merchantRefundId("REFUND_".$creditmemoId)
            ->originalMerchantOrderId($transactionId)
            ->amount($price)
            ->build();
            $standardCheckoutClient->refund($refundRequest);
            $this->logger->info('phone pe refund response  :- '.json_encode($standardCheckoutClient->jsonSerialize()));
        } catch (\PhonePe\common\exceptions\PhonePeException $e) {
            $this->logger->critical($e->getMessage());
            $this->messageManager->addErrorMessage(__('We can\'t save the credit memo right now.'));
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->messageManager->addErrorMessage(__('We can\'t save the credit memo right now.'));
        }
    }
}
