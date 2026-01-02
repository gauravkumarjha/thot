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

namespace Neklo\ProductPosition\Plugin\CatalogImportExport\Model\Indexer\Stock;

use Magento\CatalogInventory\Model\Indexer\Stock\Processor;
use Magento\ImportExport\Model\Import;

class ImportPlugin
{
    /**
     * @var Processor
     */
    private $stockIndexerProcessor;

    /**
     * @param Processor $stockIndexerProcessor
     */
    public function __construct(Processor $stockIndexerProcessor)
    {
        $this->stockIndexerProcessor = $stockIndexerProcessor;
    }

    /**
     *  Reindex imported products
     *
     * @param Import $subject
     * @param bool $import
     * @return bool
     */
    public function afterImportSource(Import $subject, bool $import): bool
    {
        if (!$this->stockIndexerProcessor->isIndexerScheduled()) {
            $this->stockIndexerProcessor->reindexAll();
        }

        return $import;
    }
}
