<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\Watchlog\Setup;

/**
 * Class Recurring
 * @package Wyomind\Watchlog\Setup
 */
class Recurring implements \Magento\Framework\Setup\InstallSchemaInterface
{


    /**
     * @var null|\Wyomind\Framework\Helper\License\Update
     */
    private $_framework = null;

    /**
     * Recurring constructor.
     * @param \Wyomind\Framework\Helper\License\Update $framework
     */
    public function __construct(
        \Wyomind\Framework\Helper\License\Update $framework
    ) {
    
        $this->_framework = $framework;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
    

        if ($context->getVersion()) {
            $this->_framework->update(__CLASS__, $context);
        }

    }
}
