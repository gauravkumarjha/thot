<?php 
namespace DiMedia\Utility\Plugin;

class WishlistOrderPlugin
{
    public function beforeLoad(\Magento\Wishlist\Model\ResourceModel\Item\Collection $subject)
    {
        $subject->getSelect()->order('main_table.added_at DESC');
    }
}
