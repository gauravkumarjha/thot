<?php

/**
 * Copyright © 2021 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\OrderEraser\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Wyomind\Framework\Helper\Install;
use Wyomind\Framework\Helper\License\UpdateFactory;

class Recurring implements InstallSchemaInterface
{

    protected $license;
    private $framework = null;

    public function __construct(
        Install       $framework,
        UpdateFactory $license
    ) {
        $this->framework = $framework;
        $this->license = $license;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface   $setup,
        ModuleContextInterface $context
    ) {

        $files = [];
        $this->framework->copyFilesByMagentoVersion(__FILE__, $files);

        if ($context->getVersion() != null) {
            $this->license->create()->update(__CLASS__, $context);
        }
    }
}
