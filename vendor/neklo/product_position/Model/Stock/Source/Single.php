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

namespace Neklo\ProductPosition\Model\Stock\Source;

use Magento\Catalog\Api\Data\ProductInterface;

class Single implements StockSourceInterface
{
    public const SOURCE_TYPE = 'single';

    /**
     * @inheritDoc
     */
    public function isSalable(ProductInterface $product): bool
    {
        return (bool)$product->isAvailable();
    }
}
