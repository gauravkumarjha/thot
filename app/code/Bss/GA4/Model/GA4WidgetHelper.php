<?php
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
 * @copyright  Copyright (c) 2022-2023 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\GA4\Model;

class GA4WidgetHelper
{
    /**
     * @var \Bss\GA4\Model\DataItem
     */
    protected $dataItem;

    /**
     * @var \Bss\GA4\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $sessionCustomer;

    /**
     * @var \Bss\GA4\Model\Config
     */
    protected $config;

    /**
     * @param DataItem $dataItem
     * @param \Bss\GA4\Helper\Data $dataHelper
     * @param \Magento\Customer\Model\Session $sessionCustomer
     * @param Config $config
     */
    public function __construct(
        \Bss\GA4\Model\DataItem $dataItem,
        \Bss\GA4\Helper\Data $dataHelper,
        \Magento\Customer\Model\Session $sessionCustomer,
        \Bss\GA4\Model\Config $config
    ) {
        $this->dataItem = $dataItem;
        $this->dataHelper = $dataHelper;
        $this->sessionCustomer = $sessionCustomer;
        $this->config = $config;
    }

    /**
     * Pre-pare list items data
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param null|string $title
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Db_Statement_Exception
     */
    public function prepareListItems($collection, $title = null)
    {
        $index = 0;
        $dataItems = [];
        $this->dataItem->setItemVariant = false;
        foreach ($collection as $product) {
            $index++;
            $prepareItem = $this->dataItem->renderItem($product, $index);
            if ($product->getTypeId() == "simple") {
                $prepareItem['price'] =  $product->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
            }
            $prepareItem['item_list_id'] = '';
            $prepareItem['item_list_name'] = $title ?? "Default Widget Product List";
            $dataItems[] = $prepareItem;
        }
        $data = $dataItems;
        return $this->dataHelper->serializeItem($data);
    }

    /**
     * Make a different between widgets
     *
     * @return string
     */
    public function getIndex()
    {
        if (!$this->sessionCustomer->getIndexWidget()) {
            $this->sessionCustomer->setIndexWidget(1);
        } else {
            $this->sessionCustomer->setIndexWidget($this->sessionCustomer->getIndexWidget() + 1);
        }
        return "bss-" . $this->sessionCustomer->getIndexWidget();
    }

    /**
     * Convert data to String
     *
     * @param string|int|float|array $data
     * @return bool|string
     */
    public function serializer($data)
    {
        return $this->dataHelper->serializeItem($data);
    }

    /**
     * Check module is enable
     *
     * @return mixed
     */
    public function isEnableModule()
    {
        return $this->config->getConfigValue(Config::XML_PATH_ENABLED);
    }
}
