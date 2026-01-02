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
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class RemoveItem implements ObserverInterface
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
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $item = $observer->getQuoteItem();
        $data = [
            'removeItem' => true,
            'productId' => $item->getProduct()->getId(),
            'qty' => $item->getQty() ?? 1
        ];
        if ($item->getChildren() && $item->getChildren()[0]) {
            $data['productId'] = $item->getChildren()[0]->getProductId();
        }
        $price = (float)$item->getProduct()->getFinalPrice($data['qty']) * $data['qty'];
        $data["price"] = $this->dataHelper->convertPriceCurrency($price);
        if ($this->session->getRemoveItem()) {
            $this->session->unsRemoveItem();
        }
        $this->session->setRemoveItem($data);
    }
}
