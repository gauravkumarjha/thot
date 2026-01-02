<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ShopByBrand
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\ShopByBrand\Model\ResourceModel\Layer\Filter;

use Magento\Framework\App\Http\Context;
use Magento\Framework\Indexer\DimensionFactory;
use Magento\Framework\Search\Request\IndexScopeResolverInterface;

class Price extends \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Lof\ShopByBrand\Model\Layer\Resolver $layerResolver,
        \Magento\Customer\Model\Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null,
        IndexScopeResolverInterface $priceTableResolver = null,
        Context $httpContext = null,
        DimensionFactory $dimensionFactory = null
    ) {
        parent::__construct($context, $eventManager, $layerResolver, $session, $storeManager, $connectionName, $priceTableResolver, $httpContext, $dimensionFactory);
    }
}
