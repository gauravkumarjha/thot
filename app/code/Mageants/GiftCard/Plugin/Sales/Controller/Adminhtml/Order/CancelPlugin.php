<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCard\Plugin\Sales\Controller\Adminhtml\Order;

use Magento\Sales\Controller\Adminhtml\Order\Cancel;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Message\ManagerInterface;
use Mageants\GiftCard\Model\AccountFactory;

class CancelPlugin
{
    const COUPONRULENAME = 'gift_certificate';
    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var Mageants\GiftCard\Model\Account
     */
    protected $account;

    /**
     * Constructor
     *
     * @param ManagerInterface $messageManager
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        ManagerInterface $messageManager,
        OrderRepositoryInterface $orderRepository,
        AccountFactory $account
    ) {
        $this->messageManager = $messageManager;
        $this->orderRepository = $orderRepository;
        $this->account = $account;
    }

    /**
     * After execute plugin for Cancel controller
     *
     * @param Cancel $subject
     * @param Redirect $result
     * @return Redirect
     */
    public function afterExecute(Cancel $subject, Redirect $result)
    {
        $orderId = $subject->getRequest()->getParam('order_id');
        $order = false;
        if ($orderId) {
            $order = $this->orderRepository->get($orderId);
        }
        if ($order) {
            $orderStatus = $order->getStatus();
            $couponRuleName = $order->getCouponRuleName();
            $giftcardAccountId = $order->getGiftcardAccountId();
            $giftcardCode = $order->getGiftcardCode();
            $refundStatus = $order->getGiftValueRefundStatus();
            if ($orderStatus == 'canceled'
                && $giftcardCode != ''
                && $giftcardAccountId != ''
                && $couponRuleName === self::COUPONRULENAME
                && $refundStatus != 1) {
                try {
                    $giftcardCode = $order->getGiftcardCode();
                    $orderGiftValue = $order->getBaseOrderGift() ?? 0;

                    $availableCode = $this->account->create()->getCollection()
                        ->addFieldToFilter('gift_code', trim($giftcardCode))
                        ->addFieldToFilter('account_id', $giftcardAccountId);
                    foreach ($availableCode as $code) {
                        $currentBalance = $code->getCurrentBalance();
                    }

                    if (isset($currentBalance)) {
                        $accountdata = [
                            'current_balance'=> $currentBalance + $orderGiftValue,
                            'account_id'=> $giftcardAccountId,
                            'status' => 1
                        ];
                        $giftAccount = $this->account->create();
                        $giftAccount->setData($accountdata);
                        $giftAccount->save();

                        $order->setOrderGiftRefund($order->getOrderGift());
                        $order->setbaseOrderGiftRefund($order->getbaseOrderGift());
                        $order->setGiftValueRefundStatus(1)->save();
                    }
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $this->messageManager->addErrorMessage(__('Order not found.'));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('An error occurred while processing the order cancellation.'));
                }
            }
        }

        return $result;
    }
}
