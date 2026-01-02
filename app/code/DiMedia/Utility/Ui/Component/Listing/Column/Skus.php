<?php

namespace DiMedia\Utility\Ui\Component\Listing\Column;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Skus extends Column
{
    private $serializer;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        SerializerInterface $serializer,
        array $components = [],
        array $data = []
    ) {
        $this->serializer = $serializer;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (!empty($item['skus'])) {
                    $skus = unserialize($item['skus']);
                    $skuList = [];
                    foreach ($skus as $skuData) {
                        if (isset($skuData['sku'])) {
                            $skuList[] = $skuData['sku'];
                        }
                    }
                    $item['skus'] = implode(', ', $skuList);
                }
            }
        }
        return $dataSource;
    }
}
