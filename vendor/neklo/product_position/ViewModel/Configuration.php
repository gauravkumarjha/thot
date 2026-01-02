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

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Neklo\ProductPosition\Model\Config;
use Neklo\ProductPosition\Model\Source\System\Config\Mode;

class Configuration implements ArgumentInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Get columns number
     *
     * @return int
     */
    public function getColumnCount(): int
    {
        return $this->config->getColumnCount();
    }

    /**
     * Get rows number
     *
     * @return int
     */
    public function getRowCount(): int
    {
        return $this->config->getRowCount();
    }

    /**
     * Get mode
     *
     * @return int
     */
    public function getMode(): int
    {
        return $this->config->getMode();
    }

    /**
     * Check if pagination is enabled
     *
     * @return bool
     */
    public function isPaginationEnabled(): bool
    {
        return $this->getMode() === Mode::MODE_PAGINATION_CODE;
    }

    /**
     * Get mode class
     *
     * @return string
     */
    public function getModeClass(): string
    {
        switch ($this->getMode()) {
            case Mode::MODE_PAGINATION_CODE:
                $modeClass = 'Pager';
                break;
            default:
                $modeClass = 'Sorter';
                break;
        }

        return $modeClass;
    }
}
