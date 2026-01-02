<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCard\Plugin;

use Magento\Checkout\Model\Cart;
use Psr\Log\LoggerInterface;
use Mageants\GiftCard\Helper\ApplyHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartRepositoryInterface;

class CartAfterSavePlugin
{
    protected $logger;
    protected $giftApplyHelper;
    protected $checkoutSession;
    protected $quoteRepository;

    private $isGiftCardApplied = false;

    public function __construct(
        LoggerInterface $logger,
        ApplyHelper $giftApplyHelper,
        CheckoutSession $checkoutSession,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->logger = $logger;
        $this->giftApplyHelper = $giftApplyHelper;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
    }

    public function afterSave(Cart $subject, $result)
    {
        $quote = $subject->getQuote();
        $GiftCode = $this->checkoutSession->getGiftCardCode();

        if ($GiftCode) {
            $items = $quote->getAllVisibleItems();
            $data = [];
            $inItems = [];

            foreach ($items as $item) {
                $itemProdId = $item->getProduct()->getId();
                if (!in_array($itemProdId, $inItems)) {
                    $_catIds = '';
                    $itemCatIds = $item->getProduct()->getCategoryIds();
                    $i = 1;
                    foreach ($itemCatIds as $catid) {
                        $_catIds .= $catid;
                        if (count($itemCatIds) > $i) {
                            $_catIds .= ",";
                        }
                        $i++;
                    }
                    $data['categoryids'][] = $_catIds;
                    $inItems[] = $itemProdId;
                }
            }

            if (isset($data['categoryids'])) {
                $data['gift_code'] = $this->checkoutSession->getGiftCardCode();

                $this->giftApplyHelper->removeGiftCard();
                $this->giftApplyHelper->applyGiftCard($data, $subject);
            }
        }

        return $result;
    }
}
