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

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Category
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $categoryRepository;

    /**
     * @var CatalogHelper
     */
    private CatalogHelper $catalogHelper;

    /**
     * @param RequestInterface $request
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        RequestInterface $request,
        CategoryRepositoryInterface $categoryRepository,
        CatalogHelper $catalogHelper
    ) {
        $this->request = $request;
        $this->categoryRepository = $categoryRepository;
        $this->catalogHelper = $catalogHelper;
    }

    /**
     * Get category id by param request
     *
     * @return int
     */
    public function getCategoryId(): int
    {
        return (int)$this->request->getParam('id');
    }

    /**
     * Get category
     *
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    public function getCategory(): CategoryInterface
    {
        return $this->categoryRepository->get($this->getCategoryId());
    }
}
