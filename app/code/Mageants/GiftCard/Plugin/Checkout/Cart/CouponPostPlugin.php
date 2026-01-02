<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\GiftCard\Plugin\Checkout\Cart;

use Magento\Checkout\Controller\Cart\CouponPost;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Checkout\Model\Cart;
use Mageants\GiftCard\Helper\ApplyHelper;

class CouponPostPlugin
{
    /**
     * @var Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var Mageants\GiftCard\Helper\ApplyHelper
     */
    protected $giftApplyHelper;

    /**
     * @param CheckoutSession $checkoutSession
     * @param Cart $cart
     * @param ApplyHelper $giftApplyHelper
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        Cart $cart,
        ApplyHelper $giftApplyHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->cart = $cart;
        $this->giftApplyHelper = $giftApplyHelper;
    }

    /**
     * After execute plugin for CouponPost controller
     *
     * @param CouponPost $subject
     * @param Redirect $result
     * @return Redirect
     */
    public function afterExecute(CouponPost $subject, Redirect $result)
    {
        $GiftCode = $this->checkoutSession->getGiftCardCode();

        if ($GiftCode) {
            $items = $this->cart->getItems();
            $data = [];
            $inItems = [];

            if ($items) {
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
            }

            if (isset($data['categoryids'])) {
                $data['gift_code'] = $this->checkoutSession->getGiftCardCode();

                $this->giftApplyHelper->removeGiftCard();
                $this->giftApplyHelper->applyGiftCard($data, $this->cart);
            }
        }

        return $result;
    }
}
