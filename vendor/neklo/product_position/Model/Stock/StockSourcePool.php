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
use Magento\Framework\Module\Manager;
use Neklo\ProductPosition\Model\Stock\Source\Multi;
use Neklo\ProductPosition\Model\Stock\Source\Single;
use Neklo\ProductPosition\Model\Stock\Source\StockSourceInterface;

class StockSourcePool implements StockSourcePoolInterface
{
    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var StockSourceInterface[]
     */
    private $sources;

    /**
     * @param Manager $moduleManager
     * @param array $sources
     */
    public function __construct(
        Manager $moduleManager,
        array $sources
    ) {
        $this->moduleManager = $moduleManager;
        $this->sources = $sources;
    }

    /**
     * @inheritDoc
     */
    public function getSource(): StockSourceInterface
    {
        $source = $this->isMultiSourceEnabled() ? Multi::SOURCE_TYPE : Single::SOURCE_TYPE;

        try {
            return $this->sources[$source];
        } catch (\Exception $e) {
            throw new LocalizedException(__('Stock source "%1" doesn\'t exist', $source), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function isMultiSourceEnabled(): bool
    {
        return $this->moduleManager->isEnabled('Magento_Inventory');
    }
}
