<?php
namespace Magecomp\Gstcharge\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Gstnumberrequired implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Required')],
            ['value' => 0, 'label' => __('Optional')],
        ];
    }
}
