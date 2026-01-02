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

namespace Neklo\ProductPosition\Model\Stock;

use Magento\Framework\Exception\LocalizedException;
use Neklo\ProductPosition\Model\Stock\Source\StockSourceInterface;

interface StockSourcePoolInterface
{
    /**
     * Get stock source
     *
     * @return StockSourceInterface
     * @throws LocalizedException
     */
    public function getSource(): StockSourceInterface;

    /**
     * Check if multi source inventory is enabled
     *
     * @return bool
     */
    public function isMultiSourceEnabled(): bool;
}
