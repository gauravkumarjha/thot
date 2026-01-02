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
namespace Bss\GA4\Model\Config;

use Magento\Framework\Phrase;

class Comment implements \Magento\Config\Model\Config\CommentInterface
{
    /**
     * Get comment text
     *
     * @param string $elementValue
     * @return Phrase
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getCommentText($elementValue)
    {
        $url = "https://support.google.com/analytics/answer/10447272#magento&zippy=%2Cmagento";

        return __('Ex: G-123456789. Follow <a href="' . $url .
            '"target="_blank">instructions</a> to setup data streams and get Measurement ID.');
    }
}
