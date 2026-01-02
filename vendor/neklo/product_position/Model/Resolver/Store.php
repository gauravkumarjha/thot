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

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class Store
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        RequestInterface $request,
        StoreManagerInterface $storeManager
    ) {
        $this->request = $request;
        $this->storeManager = $storeManager;
    }

    /**
     * Get store id by param request
     *
     * @return int
     */
    public function getStoreId(): int
    {
        return (int)$this->request->getParam('store');
    }

    /**
     * Get store
     *
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStore(): StoreInterface
    {
        return $this->storeManager->getStore($this->getStoreId());
    }

    /**
     * Get stores
     *
     * @return StoreInterface[]
     */
    public function getStores(): array
    {
        return $this->storeManager->getStores(false, true);
    }
}
