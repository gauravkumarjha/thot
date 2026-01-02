<?php
namespace Mageplaza\LayeredNavigation\Plugin\Filter;

class Category
{
    public function afterGetItems(\Magento\Catalog\Model\Layer\Filter\Category $subject, $result)
    {
        $categoryIdToRemove = 54; // ID of the category you want to hide

        foreach ($result as $key => $item) {
            if ($item->getValue() == $categoryIdToRemove) {
                unset($result[$key]);
            }
        }

        return $result;
    }
}
