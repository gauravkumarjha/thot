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

namespace Neklo\ProductPosition\Model\Source\System\Config;

use Magento\Framework\Data\OptionSourceInterface;
use Neklo\ProductPosition\Model\Config;

class Count implements OptionSourceInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $valueList = $this->config->getPerPageValues();

        $optionArray = [];
        foreach ($valueList as $value) {
            $optionArray[] = [
                'value' => $value,
                'label' => $value,
            ];
        }

        return $optionArray;
    }

    /**
     * Convert config values to array
     *
     * @return array
     */
    public function toArray(): array
    {
        $valueList = $this->config->getPerPageValues();

        $optionArray = [];
        foreach ($valueList as $value) {
            $optionArray[$value] = $value;
        }

        return $optionArray;
    }
}
