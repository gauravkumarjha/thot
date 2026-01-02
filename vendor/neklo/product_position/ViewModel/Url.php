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

namespace Neklo\ProductPosition\ViewModel;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Neklo\ProductPosition\Model\Resolver\Category;
use Neklo\ProductPosition\Model\Resolver\Store;

class Url implements ArgumentInterface
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var Store
     */
    private $storeResolver;

    /**
     * @var Category
     */
    private $categoryResolver;

    /**
     * @param UrlInterface $url
     * @param Store $storeResolver
     * @param Category $categoryResolver
     */
    public function __construct(
        UrlInterface $url,
        Store $storeResolver,
        Category $categoryResolver
    ) {
        $this->url = $url;
        $this->storeResolver = $storeResolver;
        $this->categoryResolver = $categoryResolver;
    }

    /**
     * Get next page url
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getNextPageUrl(): string
    {
        try {
            return $this->url->getUrl(
                'neklo_productposition/ajax/page',
                [
                    'id' => $this->categoryResolver->getCategory()->getId(),
                    'store' => $this->storeResolver->getStore()->getId()
                ]
            );
        } catch (\Exception $e) {
            return '';
        }
    }
}
