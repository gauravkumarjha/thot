<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCard\Api;

/**
 * Class GiftCard Management Interface
 */
interface GiftCardManagementInterface
{
    /**
     * Adds a gift card to a specified cart.
     *
     * @param int $cartId The cart ID.
     * @param string $giftCard The coupon code data.
     * @return bool
     */
    public function set($cartId, $giftCard);
}
