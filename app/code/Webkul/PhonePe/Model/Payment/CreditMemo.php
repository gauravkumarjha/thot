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

use PhonePe\payments\v1\PhonePePaymentClient;
use Webkul\PhonePe\Api\CreditMemoInterface;
use Webkul\PhonePe\Helper\Data;

class CreditMemo implements CreditMemoInterface
{
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $itemCreationFactory;

    /**
     * @var \Magento\Sales\Api\CreditmemoRepositoryInterface
     */
    protected $creditmemoRepository;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $refundOrder;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Webkul\PhonePe\Logger\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo\ItemCreationFactory $itemCreationFactory
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository
     * @param \Magento\Sales\Model\RefundOrder $refundOrder
     * @param \Magento\Framework\App\Request\Http $request
     * @param Data $helper
     * @param \Webkul\PhonePe\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Sales\Model\Order\Creditmemo\ItemCreationFactory $itemCreationFactory,
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository,
        \Magento\Sales\Model\RefundOrder $refundOrder,
        \Magento\Framework\App\Request\Http $request,
        Data $helper,
        \Webkul\PhonePe\Logger\Logger $logger
    ) {
        $this->itemCreationFactory = $itemCreationFactory;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->refundOrder = $refundOrder;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->request = $request;
    }

    /**
     * Handle payment success
     *
     * @api
     * @return bool
     */
    public function execute()
    {
        try {
            $response = $this->request->getParams();
            $creditmemoId = $response['creditmemoId'];
            $transactionId = $response['transactionId'];
            $environment = $this->helper->getEnv();
            $merchantId = $this->helper->getConfigValue('merchant_id');
            $saltKey = $this->helper->getConfigValue('salt_key');
            $saltIndex = $this->helper->getConfigValue('salt_index');
            $phonePeClient = new PhonePePaymentClient($merchantId, $saltKey, $saltIndex, $environment, true);
            $transactionId = $this->helper->decryptData($transactionId);
            $creditmemoId = $this->helper->decryptData($creditmemoId);
            $checkStatus = $phonePeClient->statusCheck('R'.$transactionId);
            if ($checkStatus->getState() == Data::COMPLETED_STATE) {
                $creditmemo = $this->creditmemoRepository->get($creditmemoId);
                $creditmemo->setState(\Magento\Sales\Model\Order\Creditmemo::STATE_REFUNDED)->save();
            }
        } catch (\Exception $e) {
            $this->logger->info('PhonePeRefundCallback'. $e->getMessage());
        }
        return true;
    }
}
