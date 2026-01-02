<?php
declare(strict_types=1);
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at thisURL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_GA4
 * @author     Extension Team
 * @copyright  Copyright (c) 2022-2022 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\GA4\Model;

class WidgetProduct
{
    /**
     * @var null
     */
    public $collection;

    /**
     * Set collection
     *
     * @param mixed $collecion
     * @return void
     */
    public function setWidgetCollection($collecion)
    {
        $this->collection = $collecion;
    }

    /**
     * Get collection
     *
     * @return null
     */
    public function getWidgetCollection()
    {
        return $this->collection;
    }
}
