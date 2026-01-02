<?php
declare(strict_types=1);
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at thisURL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_GA4
 * @author     Extension Team
 * @copyright  Copyright (c) 2022-2022 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\GA4\Observer;

use Bss\GA4\Helper\Data;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class AddToCart implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @param Session $session
     * @param RequestInterface $request
     * @param Data $dataHelper
     */
    public function __construct(
        Session $session,
        \Magento\Framework\App\RequestInterface $request,
        \Bss\GA4\Helper\Data $dataHelper
    ) {
        $this->session = $session;
        $this->request = $request;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Set data to event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($observer->getProduct()->getTypeId() != "grouped") {
            $data = [
                'isAddToCart' => true,
                'productId' => $observer->getProduct()->getId(),
                'qty' => $observer->getProduct()->getQty()
            ];
            $price = (float)$observer->getQuoteItem()->getProduct()
                    ->getFinalPrice($observer->getProduct()->getQty()) * $observer->getProduct()->getQty();
            if ($observer->getProduct()->getTypeId() == "bundle") {
                $price = $observer->getQuoteItem()->getPrice();
            }
            $data["price"] = $this->dataHelper->convertPriceCurrency($price);
            if ($observer->getProduct()->getTypeId() == "configurable") {
                $item = $observer->getQuoteItem();
                $data["productId"] = array_keys($item->getQtyOptions())[0];
                $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                $variant = [];
                foreach ($options['attributes_info'] as $option) {
                    $variant[] = $option['label'] . ": " . $option['value'];
                }
                if ($variant) {
                    $data['variant'] = implode(',', $variant);
                }
            }
            if ($this->session->getProductAddToCart()) {
                $this->session->unsProductAddToCart();
            }
            $this->session->setProductAddToCart($data);
        }
    }
}
