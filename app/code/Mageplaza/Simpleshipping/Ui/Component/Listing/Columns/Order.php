<?php

namespace Mageplaza\Simpleshipping\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class Order extends Column
{
    protected function _prepareDataSource()
    {
        $dataSource = parent::_prepareDataSource();
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item['custom_shipping_price'] = isset($item['custom_shipping_price']) ? $item['custom_shipping_price'] : '';
            }
        }
        return $dataSource;
    }
}
