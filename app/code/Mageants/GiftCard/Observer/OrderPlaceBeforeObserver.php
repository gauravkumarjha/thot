<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCard\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Mageants\GiftCard\Model\Account;

class OrderPlaceBeforeObserver implements ObserverInterface
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Account
     */
    protected $modelAccount;

    /**
     * @param CheckoutSession $checkoutSession
     * @param ManagerInterface $messageManager
     * @param Account $modelAccount
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        ManagerInterface $messageManager,
        Account $modelAccount
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->messageManager = $messageManager;
        $this->modelAccount = $modelAccount;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $giftCode = $this->checkoutSession->getGiftCardCode();
        $accountid = $this->checkoutSession->getAccountid();
        $appliedGiftcardBaseAmount = $this->checkoutSession->getGifts();
        if ($giftCode && $accountid) {
            $accountData = $this->modelAccount->load($accountid);
            $giftCardCurrentBalance = $accountData->getCurrentBalance();

            if ($appliedGiftcardBaseAmount > $giftCardCurrentBalance) {

                $this->checkoutSession->setGift('');
                $this->checkoutSession->setGifts('');
                $this->checkoutSession->setGiftCardCode("");
                $this->checkoutSession->setAccountid('');
                $this->checkoutSession->setGiftbalance('');
                $this->checkoutSession->getQuote()->collectTotals()->save();

                $errorMessage = 'gift-amount-miss-match.';
                throw new LocalizedException(__($errorMessage));
            }
        }
    }
}
