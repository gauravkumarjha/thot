<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCard\Block\Adminhtml\Codeset\Edit\Tabs\View;

/**
 * Class Codeset edit tab Deletelinks
 */
class Deletelinks extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Delete link create
     *
     * @param  object $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $link="<a href='".$this->getUrl('giftcertificate/index/delete/', ['id'=>$row->getId()])."' >Delete </a>";
        return htmlspecialchars_decode($link, ENT_QUOTES);
    }
}
