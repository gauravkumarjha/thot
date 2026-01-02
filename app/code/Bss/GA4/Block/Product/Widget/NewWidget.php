<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * @category   BSS
 * @package    Bss_GA4
 * @author     Extension Team
 * @copyright  Copyright (c) 2022-2023 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\GA4\Block\Product\Widget;

class NewWidget extends \Magento\Catalog\Block\Product\Widget\NewWidget
{
    /**
     * @var \Bss\GA4\Model\GA4WidgetHelper
     */
    protected $widgetHelper;

    /**
     * Construct
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Bss\GA4\Model\GA4WidgetHelper $widgetHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Bss\GA4\Model\GA4WidgetHelper $widgetHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $data
        );
        $this->widgetHelper = $widgetHelper;
    }

    /**
     * Pre-pare list items data
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Db_Statement_Exception
     */
    public function prepareListItems($collection)
    {
        return $this->widgetHelper->prepareListItems($collection, $this->getTitle());
    }

    /**
     * Make a different between widgets
     *
     * @return string
     */
    public function getIndex()
    {
        return $this->widgetHelper->getIndex();
    }

    /**
     * Convert data to String
     *
     * @param string|int|float|array $data
     * @return bool|string
     */
    public function serializer($data)
    {
        return $this->widgetHelper->serializer($data);
    }

    /**
     * Check module is enable
     *
     * @return mixed
     */
    public function isEnableModule()
    {
        return $this->widgetHelper->isEnableModule();
    }
}
