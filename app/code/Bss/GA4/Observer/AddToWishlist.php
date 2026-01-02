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

use Bss\GA4\Model\DataItem;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class AddToWishlist implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var DataItem
     */
    protected $dataItem;

    /**
     * @param Session $customerSession
     * @param RequestInterface $request
     * @param DataItem $dataItem
     */
    public function __construct(
        Session                                 $customerSession,
        \Magento\Framework\App\RequestInterface $request,
        \Bss\GA4\Model\DataItem $dataItem
    ) {
        $this->customerSession = $customerSession;
        $this->request = $request;
        $this->dataItem = $dataItem;
    }

    /**
     * Set data to event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = [
            'productId' => $observer->getProduct()->getId(),
            'qty' => $this->request->getParam('qty') ?? 1
        ];
        if ($observer->getProduct()->getTypeId() == "configurable") {
            $data["item_variant"] = $this->dataItem->getVariantConfigurable($observer->getItem()->getProduct());
        }
        if ($this->request->getParam('super_group')) {
            $data['super_group'] = $this->request->getParam('super_group');
        }
        if ($this->customerSession->getProductData()) {
            $this->customerSession->unsProductData();
        }
        $this->customerSession->setProductData($data);
    }
}
