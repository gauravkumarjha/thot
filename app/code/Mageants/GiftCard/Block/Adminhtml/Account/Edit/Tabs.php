<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\GiftCard\Block\Adminhtml\Account\Edit;

/**
 * Tabs class for order Tab
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('post_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Account Information'));
    }


    protected function _prepareLayout()
    {
        $this->addTab(
            'order',
            [
                'label' => __('Order History'),
                'title' => __('Order History'),
                'url' => $this->getUrl('giftcertificate/gcaccount/order', ['account_id' => $this->getRequest()->getParam('account_id')]),
                'class' => 'ajax',
                'after' => 'resend'
            ]
        );

        return parent::_prepareLayout();
    }
}
