<?php
namespace Chetaru\Team\Model\Config\Source;
 
class Device implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
			['value' => 'desktop', 'label' => __('Desktop')],
			['value' => 'mobile', 'label' => __('Mobile')]
		];
    }
}