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

namespace Neklo\ProductPosition\Model;

use Magento\CatalogInventory\Model\Configuration;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public const IS_ENABLED = 'neklo_productposition/general/is_enabled';
    public const COLUMN_COUNT = 'neklo_productposition/grid/column_count';
    public const ROW_COUNT = 'neklo_productposition/grid/row_count';
    public const GRID_MODE = 'neklo_productposition/grid/display_mode';
    public const PER_PAGE_VALUE_LIST = 'catalog/frontend/grid_per_page_values';

    public const PER_PAGE_VALUE_DELIMITER = ',';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::IS_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get columns count
     *
     * @return int
     */
    public function getColumnCount(): int
    {
        return (int)$this->scopeConfig->getValue(
            self::COLUMN_COUNT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get rows count
     *
     * @return int
     */
    public function getRowCount(): int
    {
        return (int)$this->scopeConfig->getValue(
            self::ROW_COUNT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get mode
     *
     * @return int
     */
    public function getMode(): int
    {
        return (int)$this->scopeConfig->getValue(
            self::GRID_MODE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get number of values per page
     *
     * @return array
     */
    public function getPerPageValues(): array
    {
        $value = (string)$this->scopeConfig->getValue(
            self::PER_PAGE_VALUE_LIST,
            ScopeInterface::SCOPE_STORE
        );

        return empty($value) ? [] : explode(self::PER_PAGE_VALUE_DELIMITER, $value);
    }

    /**
     * Check if stock is managed
     *
     * @return bool
     */
    public function isManageStock(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            Configuration::XML_PATH_MANAGE_STOCK,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if out of stock products should be displayed
     *
     * @return bool
     */
    public function isShowOutOfStock(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            Configuration::XML_PATH_SHOW_OUT_OF_STOCK,
            ScopeInterface::SCOPE_STORE
        );
    }
}
