<?php
/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the NekloEULA that is bundled with this package in the file LICENSE.txt.
 *
 * It is also available through the world-wide-web at this URL: http://store.neklo.com/LICENSE.txt
 *
 * Copyright (c)  Neklo (http://store.neklo.com/)
 */

declare(strict_types=1);

namespace Neklo\ProductPosition\Model\Resolver;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Product
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CatalogHelper
     */
    private CatalogHelper $catalogHelper;

    /**
     * @param RequestInterface $request
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        RequestInterface $request,
        ProductRepositoryInterface $productRepository,
        CatalogHelper $catalogHelper
    ) {
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->catalogHelper = $catalogHelper;
    }

    /**
     * Get product id by param request
     *
     * @return int
     */
    public function getProductId(): int
    {
        return (int)$this->catalogHelper->getProduct()->getId();
    }

    /**
     * Get product
     *
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    public function getProduct(): ProductInterface
    {
        return $this->productRepository->getById($this->getProductId());
    }
}
