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
namespace Bss\GA4\Block;

use Bss\GA4\Helper\Data;
use Bss\GA4\Model\DataItem;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class EventAddToWishlist extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var DataItem
     */
    protected $additionalData;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var null
     */
    protected $product;

    /**
     * @var null
     */
    public $valueEvent;

    /**
     * @param Context $context
     * @param Data $dataHelper
     * @param DataItem $additionalData
     * @param Session $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Bss\GA4\Helper\Data $dataHelper,
        \Bss\GA4\Model\DataItem $additionalData,
        Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->dataHelper = $dataHelper;
        $this->additionalData = $additionalData;
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;
    }

    /**
     * Get product add to wishlist
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProduct()
    {
        if (!$this->product) {
            $dataInfo = $this->customerSession->getProductData();
            $storeId = $this->_storeManager->getStore()->getId();
            if ($dataInfo) {
                $this->product = $this->productRepository->getById($dataInfo['productId'], false, (string)$storeId);
            } else {
                return null;
            }
        }
        return $this->product;
    }

    /**
     * Prepare item
     *
     * @return bool|string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Db_Statement_Exception
     */
    public function prepareItem()
    {
        if ($this->getProduct() && !$this->customerSession->getRemoveFromWishList()) {
            $dataInfo = $this->customerSession->getProductData();
            $storeId = $this->_storeManager->getStore()->getId();
            $index = 1;
            $totalPrice = 0;
            if ($this->getProduct()->getTypeId() == "grouped") {
                $items = [];
                if (isset($dataInfo['super_group'])) {
                    foreach ($dataInfo['super_group'] as $productId => $qty) {
                        $index++;
                        $product = $this->productRepository->getById($productId, false, (string)$storeId);
                        $item = $this->additionalData->renderItem($product);
                        $item['quantity'] = $qty;
                        $item['index'] = $index;
                        $items[] = $item;
                        $totalPrice += $product->getFinalPrice($qty) * $qty;
                    }
                } else {
                    $associatedProducts = $this->getProduct()->getTypeInstance()->getAssociatedProducts($this->getProduct());
                    foreach ($associatedProducts as $product) {
                        $index++;
                        $item = $this->additionalData->renderItem($product);
                        $item["quantity"] = $product->getQty();
                        $item['index'] = $index;
                        $items[] = $item;
                        $totalPrice += $product->getFinalPrice($product->getQty()) * $item["quantity"];
                    }
                }
                $this->valueEvent = $totalPrice;
                return $this->serializeItem($items);
            } else {
                $this->additionalData->setItemVariant = false;
                $item = $this->additionalData->renderItem($this->getProduct());
                if (isset($dataInfo["item_variant"])) {
                    $item["item_variant"] = $dataInfo["item_variant"];
                }
                $item['quantity'] = $dataInfo['qty'];
                $this->valueEvent = $item["price"] * $item['quantity'];
                return "[" . $this->serializeItem($item) . "]";
            }
        }
        if ($this->customerSession->getRemoveFromWishList()) {
            $this->customerSession->unsRemoveFromWishList();
        }
        return false;
    }

    /**
     * Get Value
     *
     * @return float
     */
    public function getValue()
    {
        if ($this->valueEvent) {
            return $this->valueEvent;
        }
        return 0;
    }

    /**
     * Get currency
     *
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCurrency()
    {
        return $this->dataHelper->getCurrency();
    }

    /**
     * Serialize item
     *
     * @param array $item
     * @return bool|string
     */
    public function serializeItem($item)
    {
        return $this->dataHelper->serializeItem($item);
    }

    /**
     * Escaper.
     *
     * @return \Magento\Framework\Escaper
     */
    public function escaper()
    {
        return $this->_escaper;
    }
}
